<?php

use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('billing.index');
});

Route::get('/billing', [BillingController::class, 'index'])
    ->name('billing.index');

Route::get('/customers/lookup', [BillingController::class, 'lookupCustomer'])
    ->name('customers.lookup');
