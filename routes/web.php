<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::resource('invoices', InvoiceController::class);
Route::get('/invoice/print-last', [InvoiceController::class, 'printLast'])->name('invoices.printLast');
Route::get('/invoice/{id}/repayment', [InvoiceController::class, 'repayment'])->name('invoice.repayment');
Route::get('/locale-change/{locale?}', [InvoiceController::class,'change']);


