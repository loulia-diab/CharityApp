<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-receipt', function () {
    $transaction = Transaction::latest()->first(); // أو أي ID تجريبية

    return view('pdf.transaction_receipt', [
        'transaction' => $transaction
    ]);
});
