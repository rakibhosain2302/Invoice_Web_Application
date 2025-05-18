<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('invoices', InvoiceController::class);
Route::get('/invoice/print-last', [InvoiceController::class, 'printLast'])->name('invoices.printLast');
Route::get('/invoice/{id}/repayment', [InvoiceController::class, 'repayment'])->name('invoice.repayment');
Route::get('/locale-change/{locale?}', [InvoiceController::class, 'change']);



Route::resource('products', ProductController::class);


// Route::get('/products/getProducts', function () {
//     return response()->json([
//         ['id' => 1, 'text' => 'Test Product', 'unit_price' => 123]
//     ]);
// });




