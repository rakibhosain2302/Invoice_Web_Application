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
        $query = Invoice::with(['items', 'payment']);

        if ($request->filled('buyer_name')) {
            $query->where('buyer_name', 'like', '%' . $request->buyer_name . '%');
        }

        if ($request->filled('buyer_mobile')) {
            $query->where('buyer_mobile', 'like', '%' . $request->buyer_mobile . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $invoices = $query->latest()->paginate(7);

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
                    'product_name' => $item['product_name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'sub_total' => $sub_total,
                ]);
            }

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
        $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_mobile' => 'required|string|max:20',
            'note' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sub_total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'paid_at' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::find($id);

            if ($request->has('deleted_items')) {
                InvoiceItem::where('invoice_id', $invoice->id)
                    ->whereIn('id', $request->deleted_items)
                    ->delete();
            }
            $invoice->update($request->only('buyer_name', 'buyer_mobile', 'note', 'total_amount'));
            foreach ($request->items as $item) {
                $product_name = trim($item['product_name']);
                $unit_price = (float) $item['unit_price'];
                $quantity = (int) $item['quantity'];
                $sub_total = round($unit_price * $quantity, 2);

                if (!empty($item['id'])) {
                    $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->find($item['id']);
                    if ($invoiceItem) {
                        if (
                            $invoiceItem->product_name !== $product_name ||
                            $invoiceItem->unit_price != $unit_price ||
                            $invoiceItem->quantity != $quantity
                        ) {
                            $invoiceItem->update([
                                'product_name' => $product_name,
                                'unit_price' => $unit_price,
                                'quantity' => $quantity,
                                'sub_total' => $sub_total,
                            ]);
                        }
                    }
                } else {
                    if ($product_name !== '' && $unit_price > 0 && $quantity > 0) {
                        $invoice->items()->create([
                            'product_name' => $product_name,
                            'unit_price' => $unit_price,
                            'quantity' => $quantity,
                            'sub_total' => $sub_total,
                        ]);
                    }
                }
            }
            if ($request->has('payment_id')) {
                $payment = InvoicePayment::find($request->payment_id);

                if ($payment && $payment->invoice_id == $invoice->id) {
                    $payment->update($request->only('amount_paid', 'due_amount', 'paid_at'));
                } else {
                    return back()->with('error', 'Invalid payment ID or invoice mismatch.');
                }
            } else {
                $invoice->payment()->create([
                    'amount_paid' => $request->amount_paid,
                    'due_amount' => $request->due_amount,
                    'paid_at' => now(),

                ]);
            }

            DB::commit();
            return redirect()->route('invoices.index')->withSuccess('Invoice updated successfully.');
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

    public function printLast()
    {
        $invoices = Invoice::with(['items', 'payment'])->latest()->first();
        if (!$invoices) {
            return redirect()->back()->withErrors('error', 'No invoice found!');
        }
        $pdf = Pdf::loadView('invoice.invoice_pdf', compact('invoices'));
        return $pdf->stream("invoice_{$invoices->id}.pdf");
    }

    public function change($locale = 'en')
    {
        session(['locale' => $locale]);
        return redirect()->back();
    }
}


