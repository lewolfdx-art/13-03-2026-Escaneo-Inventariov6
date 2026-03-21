<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockBarcodeController;

// Ruta principal (welcome)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas para códigos de barras de Stock
Route::get('/barcode/stock/{stock}', [StockBarcodeController::class, 'generate'])
    ->name('barcode.stock');

// Opcional: ruta para etiqueta imprimible de stock (si la quieres implementar después)
Route::get('/etiqueta/stock/{stock}', function (App\Models\Stock $stock) {
    if (empty($stock->codigo)) {
        abort(404, 'Este ítem no tiene código de barras.');
    }
    return view('etiquetas.etiqueta-filamento', compact('stock'));
})->name('etiqueta.stock');

// Aquí puedes agregar otras rutas si las tienes (auth, dashboard, api, etc.)
// Por ejemplo:
// Auth::routes();
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });