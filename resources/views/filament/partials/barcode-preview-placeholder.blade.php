@if($stockId && filled($codigo))
    <div class="flex justify-center mb-4">
        <img 
            src="{{ $url }}"
            alt="Código de barras {{ e($codigo) }}"
            class="max-h-48 w-auto object-contain bg-white p-4 border border-gray-300 rounded-lg shadow-sm"
            loading="lazy"
            onerror="this.src='https://placehold.co/400x150?text=Error+al+cargar+Barcode';"
        />
    </div>
    <p class="text-center text-sm text-gray-600">
        Código: <strong>{{ e($codigo) }}</strong>
    </p>
@else
    <div class="text-center text-gray-500 italic py-6">
        Selecciona un ítem de stock con código válido para ver la vista previa.
    </div>
@endif