<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['items', 'payment'])
            ->when($request->filled('buyer_name'), function ($q) use ($request) {
                $q->where('buyer_name', 'like', '%' . $request->buyer_name . '%');
            })
            ->when($request->filled('buyer_mobile'), function ($q) use ($request) {
                $q->where('buyer_mobile', 'like', '%' . $request->buyer_mobile . '%');
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->when($request->filled('start_date') && !$request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date') && !$request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->latest()
            ->paginate(7)
            ->appends($request->all());

        return view('invoice.index', compact('invoices'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('invoice.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_mobile' => 'required|string|max:20',
            'note' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'buyer_name' => $validated['buyer_name'],
                'buyer_mobile' => $validated['buyer_mobile'],
                'note' => $validated['note'],
                'total_amount' => $validated['total_amount'],
            ]);

            foreach ($validated['items'] as $item) {
                $sub_total = $item['unit_price'] * $item['quantity'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'sub_total' => $sub_total,
                ]);
            }

            // Step 3: Save Payment
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $validated['amount_paid'],
                'due_amount' => $validated['due_amount'],
                'paid_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->withSuccess("Invoice created successfully");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors("Something went wrong: " . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['items', 'payment'])->find($id);

        if (request()->has('download') && request()->get('download') === 'pdf') {
            $pdf = Pdf::loadView('invoice.invoice_pdf', compact('invoice'));
            return $pdf->download('invoice_' . date('Y-m-d') . '.pdf');
        }
        return view('invoice.invoice', compact('invoice'));
        // return view('invoice.invoice_pdf', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoices = Invoice::with(['items', 'payment'])->find($id);
        return view('invoice.edit', compact('invoices'));
    }



    public function repayment(string $id)
    {
        $invoices = Invoice::with(['items', 'payment'])->find($id);
        return view('invoice.repayment', compact('invoices'));
    }
    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $isDueCollection = $request->filled('amount_paid') && !$request->has('items');

        $rules = [
            'buyer_name' => 'nullable|string|max:255',
            'buyer_mobile' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_amount' => 'nullable|numeric|min:0',
            'paid_at' => 'nullable|date',
        ];

        // ✅ Items validation only if not due collection
        if (!$isDueCollection) {
            $rules['buyer_name'] = 'required|string|max:255';
            $rules['buyer_mobile'] = 'required|string|max:20';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.product_id'] = 'required|integer|exists:products,id';
            $rules['items.*.product_name'] = 'required|string|max:255';
            $rules['items.*.unit_price'] = 'required|numeric|min:0';
            $rules['items.*.quantity'] = 'required|integer|min:1';
            $rules['items.*.sub_total'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return back()->withErrors(['error' => 'Invoice not found.']);
            }

            // Update invoice info
            $invoice->update($request->only('buyer_name', 'buyer_mobile', 'note', 'total_amount'));

            // ✅ Only handle items if present (i.e., not due collection)
            if (!$isDueCollection && $request->has('items')) {

                if ($request->has('deleted_items')) {
                    InvoiceItem::where('invoice_id', $invoice->id)
                        ->whereIn('id', $request->deleted_items)
                        ->delete();
                }

                foreach ($request->items as $item) {
                    $productName = trim($item['product_name']);
                    $unitPrice = (float) $item['unit_price'];
                    $quantity = (int) $item['quantity'];
                    $subTotal = round($unitPrice * $quantity, 2);

                    if (!empty($item['id'])) {
                        $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->find($item['id']);
                        if ($invoiceItem) {
                            $invoiceItem->update([
                                'product_name' => $productName,
                                'unit_price' => $unitPrice,
                                'quantity' => $quantity,
                                'sub_total' => $subTotal,
                            ]);
                        }
                    } else {
                        $invoice->items()->create([
                            'product_id' => $item['product_id'] ?? null,
                            'product_name' => $productName,
                            'unit_price' => $unitPrice,
                            'quantity' => $quantity,
                            'sub_total' => $subTotal,
                        ]);

                    }
                }
            }

            if ($request->filled('amount_paid') && $request->amount_paid > 0) {
                $invoice->payment()->create([
                    'amount_paid' => $request->amount_paid,
                    'due_amount' => $request->due_amount,
                    'paid_at' => $request->paid_at ?? now(),
                ]);
            }

            DB::commit();

            if ($request->filled('amount_paid') && $request->amount_paid > 0) {
                return redirect()->route('invoices.index')->withSuccess('Due Payment Received Successfully.');
            } else {
                return redirect()->route('invoices.index')->withSuccess('Invoice Updated Successfully.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);

        $invoice->items()->delete();

        $invoice->payment()->delete();

        $invoice->delete();

        return redirect()->back()->withSuccess('Invoice Delete successfully.');
    }

    public function change($locale = 'en')
    {
        session(['locale' => $locale]);
        return redirect()->back();
    }

}


