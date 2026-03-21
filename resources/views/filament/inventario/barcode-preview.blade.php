<!-- filament/inventario/barcode-preview.blade.php -->
@if($stock && filled($stock->codigo))
    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm font-medium mb-2">Código de barras del ítem:</p>
        
        <div class="bg-white dark:bg-white p-2 rounded-md inline-block">  <!-- Capa extra: fuerza fondo blanco en dark mode -->
            <img 
                src="{{ route('barcode.stock', $stock) }}" 
                alt="Código de barras {{ $stock->codigo }}" 
                class="max-w-full h-auto mx-auto border rounded invert-0 dark:invert-0 !invert-0 !filter-none dark:!filter-none object-contain"
                style="max-height: 140px; filter: none !important; -webkit-filter: none !important; backdrop-filter: none !important;"
            >
        </div>
        
        <p class="text-xs text-center mt-2 font-mono text-gray-600 dark:text-gray-400">
            {{ $stock->codigo }}
        </p>
    </div>
@else
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 italic">
        No hay código de barras disponible.
    </p>
@endif