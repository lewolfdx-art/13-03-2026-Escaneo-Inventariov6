<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Log;

class StockBarcodeController extends Controller
{
    /**
     * Genera la imagen del código de barras para un stock específico
     */
    public function generate(Stock $stock)
    {
        $codigo = $stock->codigo;

        if (empty($codigo)) {
            abort(404, 'Este ítem no tiene código de barras.');
        }

        $generator = new BarcodeGeneratorPNG();

        $widthFactor = 2;
        $height = 100;

        $rawImage = $generator->getBarcode(
            $codigo,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        $im = imagecreatefromstring($rawImage);
        if ($im === false) {
            abort(500, 'Error al generar el código de barras.');
        }

        $bcWidth  = imagesx($im);
        $bcHeight = imagesy($im);

        $marginLeftRight = 15;
        $marginTopBottom = 12;

        $totalWidth  = $bcWidth + (2 * $marginLeftRight);
        $totalHeight = $bcHeight + (2 * $marginTopBottom);

        $newImage = imagecreatetruecolor($totalWidth, $totalHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        imagecopy($newImage, $im, $marginLeftRight, $marginTopBottom, 0, 0, $bcWidth, $bcHeight);

        imagedestroy($im);

        ob_start();
        imagepng($newImage, null, 9);
        $finalImage = ob_get_clean();

        imagedestroy($newImage);

        return response($finalImage)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Content-Disposition', 'inline; filename="barcode-stock-' . $stock->id . '.png"');
    }

    /**
     * Procesa el escaneo de código de barras (salida o devolución)
     * Espera POST con { "code": "EPP-001" }
     * Actualiza cantidad y disponible
     * Devuelve JSON compatible con tu frontend
     */
    public function processScan(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        $code = strtoupper(trim($request->code));

        $stock = Stock::where('codigo', $code)->first();

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Herramienta no encontrada: ' . $code
            ], 404);
        }

        // Determinar si es salida o devolución
        $isSalida = $stock->disponible ?? true;

        if ($isSalida) {
            // Salida: reducir cantidad en 1 (no bajar de 0)
            $stock->cantidad = max(0, ($stock->cantidad ?? 1) - 1);
            // Actualizar disponible según la nueva cantidad
            $stock->disponible = ($stock->cantidad > 0);
            $action = 'salida';
        } else {
            // Devolución: aumentar cantidad en 1
            $stock->cantidad = ($stock->cantidad ?? 0) + 1;
            // Devolución siempre pone disponible = true
            $stock->disponible = true;
            $action = 'devolucion';
        }

        $stock->save();

        // Registrar en log (útil para debug y auditoría)
        Log::info("Movimiento registrado: {$action} - Stock ID {$stock->id} ({$code}) - Cantidad nueva: {$stock->cantidad} - Disponible: " . ($stock->disponible ? 'Sí' : 'No') . " - Usuario: " . (auth()->id() ?? 'anónimo'));

        return response()->json([
            'success' => true,
            'action' => $action,
            'tool_name' => $stock->descripcion ?? 'Herramienta sin descripción',
            'code' => $code,
            'new_stock' => $stock->cantidad ?? 0,
        ]);
    }
}