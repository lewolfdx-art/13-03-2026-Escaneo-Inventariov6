<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - {{ $stock->descripcion }}</title>
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            font-family: Arial, Helvetica, sans-serif; 
            background: white;          /* importante para impresión */
        }
        .etiqueta {
            width: 80mm;
            height: 30mm;
            background: white;
            border: 1px dashed #ccc;    /* solo para vista previa, se quita al imprimir */
            box-sizing: border-box;
            padding: 4mm 6mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            margin: 0 auto;
        }
        .titulo {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .barcode-img {
            width: 100%;
            height: auto;
            max-height: 20mm;
            image-rendering: crisp-edges;     /* mejor nitidez al imprimir */
            filter: none !important;          /* evita cualquier filtro automático */
            -webkit-filter: none !important;
        }
        .info {
            font-size: 8pt;
            text-align: center;
            line-height: 1.3;
        }
        @media print {
            body { 
                margin: 0; 
                background: white !important; 
            }
            .etiqueta { 
                border: none !important; 
                box-shadow: none !important;
                margin: 0;
                padding: 4mm 6mm;
            }
            /* Fuerza que NO se invierta aunque el sistema esté en dark mode */
            .barcode-img {
                filter: none !important;
                -webkit-filter: none !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

<div class="etiqueta">
    <div class="titulo">{{ $stock->descripcion }}</div>

    <img class="barcode-img"
    src="{{ route('barcode.stock', $stock) }}"
    alt="Código de barras {{ $stock->codigo }}"
    style="max-height: 20mm; width: 100%; object-fit: contain;">

    <div class="info">
        Código: {{ $stock->codigo }}<br>

    </div>
</div>

</body>
</html>