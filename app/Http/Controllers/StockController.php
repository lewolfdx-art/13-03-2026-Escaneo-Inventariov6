<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Devuelve las herramientas/stock disponibles para la vista pública del trabajador
     */
    public function available()
{
    $stocks = Stock::query()
        ->select('id', 'codigo', 'descripcion', 'codificacion_id', 'marca_id', 'observaciones', 'estado', 'condicion')
        ->with([
            'codificacion:id,codificacion',
            'marca:id,nombre',
        ])
        ->get();

    $result = $stocks->map(function ($stock) {
        // Stock real viene de Kardex
        $stockReal = (int) $stock->stock_actual;

        $ultimaRecal = $stock->recalibraciones()->latest('fecha_recalibracion')->first();

        return [
            'id'                => $stock->id,
            'codigo'            => $stock->codigo ?: '—',
            'descripcion'       => $stock->descripcion ?: 'Sin descripción',
            'familia'           => $stock->codificacion?->codificacion ?: '—',
            'marca'             => $stock->marca?->nombre ?: '—',
            'stock'             => $stockReal,
            'estado'            => $stock->estado ?: $stock->condicion ?: 'sin definir',
            'observaciones'     => trim($stock->observaciones) ?: '—',
            'fecha_calibracion' => $ultimaRecal 
                ? $ultimaRecal->proxima_recalibracion?->format('d/m/Y') 
                : '—',
            'foto'              => null,
        ];
    })
    ->filter(function ($item) {
        // Solo mostramos los que tienen stock positivo
        return $item['stock'] > 0;
    })
    ->values();

    // === DEBUG TEMPORAL (muy útil ahora) ===
    // Descomenta las líneas que necesites para ver qué pasa
    return response()->json([
        'debug_total_items_en_stock' => Stock::count(),
        'debug_items_con_stock_positivo' => $result->count(),
        'debug_primeros_3_para_ver' => $result->take(3)->toArray(),
        'data' => $result,
    ]);

    // Cuando ya funcione, deja solo:
    // return response()->json($result);
}
}