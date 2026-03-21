<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log; // opcional para debug

class StockBarcodeController extends Controller
{
    public function generate(Stock $stock)
    {
        $codigo = $stock->codigo;

        if (empty($codigo)) {
            abort(404, 'Este ítem no tiene código de barras.');
        }

        $generator = new BarcodeGeneratorPNG();

        // ────────────────────────────────────────────────
        // Dimensiones ajustadas para etiquetas 80mm ancho × 40mm alto
        // widthFactor: 3.0 → ancho ~60-75mm efectivo en impresión 203-300 dpi
        // height: 110 píxeles → ~20-22mm alto (legible y no desperdicia espacio)
        $widthFactor = 2;
        $height = 100;

        $rawImage = $generator->getBarcode(
            $codigo,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        // ────────────────────────────────────────────────
        // Agregamos márgenes limpios (quiet zone + visual bonito) como en tu BarcodeController
        // ~12-15 píxeles por lado → evita corte y mejora apariencia
        $im = imagecreatefromstring($rawImage);
        if ($im === false) {
            abort(500, 'Error al generar el código de barras.');
        }

        $bcWidth  = imagesx($im);
        $bcHeight = imagesy($im);

        $marginLeftRight = 15;   // quiet zone + espacio visual
        $marginTopBottom = 12;

        $totalWidth  = $bcWidth + (2 * $marginLeftRight);
        $totalHeight = $bcHeight + (2 * $marginTopBottom);

        $newImage = imagecreatetruecolor($totalWidth, $totalHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // Copia centrado
        imagecopy($newImage, $im, $marginLeftRight, $marginTopBottom, 0, 0, $bcWidth, $bcHeight);

        imagedestroy($im);

        // Salida optimizada
        ob_start();
        imagepng($newImage, null, 9); // compresión alta
        $finalImage = ob_get_clean();

        imagedestroy($newImage);

        return response($finalImage)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400') // cache 24h
            ->header('Content-Disposition', 'inline; filename="barcode-stock-' . $stock->id . '.png"');
    }
}