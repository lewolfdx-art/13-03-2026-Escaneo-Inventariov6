<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockBarcodeController;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use App\Http\Controllers\StockController;

// Ruta principal (welcome) - landing con escáner
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Ruta para obtener herramientas disponibles (ya la tienes)
Route::get('/tools/available', [StockController::class, 'available'])
    ->name('tools.available');

// ← AGREGA ESTA LÍNEA (la que faltaba)
Route::post('/scan/process', [StockBarcodeController::class, 'processScan'])
    ->name('scan.process');

// Rutas para códigos de barras de Stock
Route::get('/barcode/stock/{stock}', [StockBarcodeController::class, 'generate'])
    ->name('barcode.stock');

// Opcional: ruta para etiqueta imprimible
Route::get('/etiqueta/stock/{stock}', function (App\Models\Stock $stock) {
    if (empty($stock->codigo)) {
        abort(404, 'Este ítem no tiene código de barras.');
    }
    return view('etiquetas.etiqueta-filamento', compact('stock'));
})->name('etiqueta.stock');

// Rutas de autenticación de Laravel (necesarias para Filament si usas login básico)

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');