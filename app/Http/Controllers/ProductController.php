<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $products = $query->latest()->paginate(15)->appends($request->all());

        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.name' => 'required|string|max:255',
                'products.*.price' => 'required|numeric|min:0',
            ]);

            foreach ($validated['products'] as $product) {
                Product::create([
                    'name' => $product['name'],
                    'price' => $product['price'],
                ]);
            }

            return redirect()->back()->withSuccess('Products created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors("Something went wrong: " . $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $products = Product::find($id);
        return view('product.edit', compact('products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0'
            ]);

            $product->update($validated);

            return redirect()->route('products.index')->withSuccess('Products Update successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            return redirect()->route('products.index')->withSuccess('Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Something went wrong: ' . $e->getMessage());
        }
    }





}
