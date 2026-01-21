<?php

use App\Models\Sale;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para imprimir ticket
Route::get('/tickets/sale/{sale}', function (Sale $sale) {
    return view('tickets.sale', compact('sale'));
})->name('tickets.sale');
