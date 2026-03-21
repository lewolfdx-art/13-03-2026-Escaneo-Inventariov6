<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inventario Huancayo - Bienvenido</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

    <style>
        :root {
            --cyan-neon: #A67B5B;
            --orange-neon: #8B5A2B;
            --orange-light: #A67B5B;
            --bg-dark: #3a2a1e;
            --card-dark: #2a1f14;
            --text-light: #f5e8d3;
            --gray-light: #D4A373;
        }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: radial-gradient(circle at top, #3a2a1e, #1f150e);
            color: var(--text-light);
        }
        .neon-title {
            text-shadow: 0 0 10px var(--cyan-neon), 0 0 20px var(--cyan-neon), 0 0 30px rgba(166,123,91,0.5);
        }
        .neon-button {
            box-shadow: 0 0 15px rgba(139,90,43,0.5), inset 0 0 10px rgba(139,90,43,0.3);
            transition: all 0.3s ease;
        }
        .neon-button:hover {
            box-shadow: 0 0 30px rgba(139,90,43,0.8), inset 0 0 15px rgba(139,90,43,0.5);
            transform: translateY(-3px);
        }
        .tool-card {
            perspective: 1000px;
            transition: transform 0.4s ease-out;
        }
        .tool-card:hover {
            transform: translateY(-12px) rotateX(6deg) rotateY(8deg);
            box-shadow: 0 25px 50px -12px rgba(166,123,91,0.5);
        }

        #scanner-container {
            position: relative;
            width: 100%;
            max-width: 420px;
            height: 280px;
            margin: 2rem auto;
            border: 4px solid #A67B5B;
            border-radius: 1rem;
            overflow: hidden;
            background: black;
            transition: all 0.3s ease;
        }
        #scanner-container.hidden {
            height: 0;
            margin: 0;
            border: none;
            padding: 0;
            overflow: hidden;
        }
        #live-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .guide-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 85%;
            height: 20%;
            border: 4px dashed #E3BC9A;
            border-radius: 0.5rem;
            pointer-events: none;
            box-shadow: 0 0 15px rgba(227,188,154,0.6);
        }
        .guide-text {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            color: #E3BC9A;
            font-size: 0.9rem;
            background: rgba(0,0,0,0.6);
            padding: 4px 12px;
            border-radius: 9999px;
            pointer-events: none;
        }

        /* Estilos para el historial */
        .scan-item {
            background: #2a1f14/80;
            border: 1px solid #A67B5B/30;
            border-radius: 1rem;
            padding: 1rem;
            backdrop-filter: blur(8px);
        }
        .scan-salida { border-left: 4px solid #ef4444; }
        .scan-devolucion { border-left: 4px solid #22c55e; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">

    <header class="w-full py-6 px-6 lg:px-12 flex justify-between items-center border-b border-[#A67B5B]/20 bg-[#3a2a1e]/70 backdrop-blur-md sticky top-0 z-50">
        <div class="text-3xl font-bold neon-title flex items-center gap-3">
            <i class="fas fa-tools text-[#8B5A2B]"></i>
            Inventario Huancayo
        </div>

        <div class="flex gap-4">
            <a href="{{ \Filament\Facades\Filament::getPanel('admin')->getLoginUrl() }}" class="px-6 py-3 bg-[#A67B5B] hover:bg-[#8f6648] text-[#1a1209] font-semibold rounded-lg transition">
                Iniciar Sesión
            </a>
        </div>
    </header>

    <main class="flex-grow py-12 px-6 lg:px-12">
        <div class="max-w-6xl mx-auto">

            <!-- Título principal -->
            <h1 class="text-4xl lg:text-6xl font-extrabold neon-title text-center mb-6">
                Escaneo de Inventario
            </h1>
            <p class="text-xl text-[#D4A373] text-center mb-10 max-w-3xl mx-auto">
                Registra salidas y devoluciones al instante. Usa lector físico o la cámara de tu dispositivo.
            </p>

            <!-- Controles del escáner -->
            <div class="text-center mb-8">
                <button id="toggle-mode" class="px-10 py-4 bg-[#8B5A2B] hover:bg-[#A67B5B] text-white font-bold rounded-xl transition-all duration-300 shadow-lg text-xl neon-button">
                    Modo: Escáner Físico <i class="fas fa-barcode ml-3"></i>
                </button>
            </div>

            <!-- Área del escáner -->
            <div id="scanner-container" class="hidden">
                <video id="live-video" autoplay playsinline muted></video>
                <div class="guide-box"></div>
                <div class="guide-text">Apunta SOLO al código de barras</div>
            </div>

            <!-- Input oculto para lector físico -->
            <input type="text" id="barcode-input" autofocus autocomplete="off" class="hidden absolute opacity-0 pointer-events-none" style="left: -9999px; top: -9999px;">

            <!-- Resultado del escaneo -->
            <div id="result" class="min-h-[100px] text-center text-2xl transition-all duration-300 my-8"></div>

            <!-- Historial de movimientos -->
            <div id="last-scans" class="mt-8">
                <h3 class="text-2xl text-[#D4A373] mb-4 text-center">Últimos movimientos</h3>
                <div id="scans-list" class="space-y-4 max-h-80 overflow-y-auto pr-2">
                    <!-- Los items se agregan aquí con JS -->
                    <p class="text-center text-gray-500 italic">Aún no hay movimientos registrados</p>
                </div>
            </div>

            <!-- Herramientas disponibles -->
            <div class="mt-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold neon-title text-center mb-12">
                    Herramientas Disponibles
                </h2>
                <div id="tools-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <!-- Cargadas por JS -->
                </div>
            </div>

        </div>
    </main>

    <footer class="py-10 text-center text-[#D4A373] border-t border-[#A67B5B]/20 bg-[#3a2a1e]/70 backdrop-blur-md">
        <p>© {{ now()->year }} Inventario Huancayo • Junín, Perú</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultDiv = document.getElementById('result');
            const scansList = document.getElementById('scans-list');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const toolsGrid = document.getElementById('tools-grid');
            const scannerContainer = document.getElementById('scanner-container');
            const barcodeInput = document.getElementById('barcode-input');
            const toggleButton = document.getElementById('toggle-mode');

            let quaggaStarted = false;
            let lastDetectedCode = null;
            let lastDetectionTime = 0;
            const cooldown = 5000;
            let usingCamera = false;

            // ── Historial local (persiste en navegador) ────────────────
            let movimientos = JSON.parse(localStorage.getItem('ultimosMovimientos')) || [];
            const MAX_MOVIMIENTOS = 10;

            function renderHistorial() {
                scansList.innerHTML = '';
                if (movimientos.length === 0) {
                    scansList.innerHTML = '<p class="text-center text-gray-500 italic">Aún no hay movimientos registrados</p>';
                    return;
                }

                movimientos.forEach(mov => {
                    const item = document.createElement('div');
                    item.className = `scan-item flex justify-between items-center ${mov.action === 'salida' ? 'scan-salida' : 'scan-devolucion'}`;
                    item.innerHTML = `
                        <div>
                            <p class="font-bold text-[#E3BC9A]">${mov.code} - ${mov.tool_name || 'Herramienta'}</p>
                            <p class="text-sm text-gray-400">${new Date(mov.timestamp).toLocaleString('es-PE')}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                            ${mov.action === 'salida' ? 'bg-red-600/80' : 'bg-green-600/80'} text-white">
                            ${mov.action === 'salida' ? 'SALIDA' : 'DEVOLUCIÓN'}
                        </span>
                    `;
                    scansList.prepend(item); // Los más nuevos arriba
                });
            }

            // Cargar historial al inicio
            renderHistorial();

            // ── Cargar herramientas ───────────────────────────────
            function loadTools() {
                fetch('/tools/available')
                    .then(response => response.json())
                    .then(data => {
                        const tools = data.data || [];
                        toolsGrid.innerHTML = '';
                        if (tools.length === 0) {
                            toolsGrid.innerHTML = '<p class="col-span-full text-center text-2xl text-[#D4A373]">No hay herramientas disponibles</p>';
                            return;
                        }
                        tools.forEach(tool => {
                            const card = document.createElement('div');
                            card.className = 'tool-card bg-[#2a1f14]/80 backdrop-blur-md rounded-2xl overflow-hidden border border-[#A67B5B]/20 hover:border-[#A67B5B]/60 transition-all duration-300';

                            card.innerHTML = `
                                <div class="p-5 space-y-4">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-lg font-bold text-[#E3BC9A] line-clamp-2">
                                            ${tool.codigo} - ${tool.descripcion || 'Sin descripción'}
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                            ${tool.estado?.toLowerCase().includes('activo') || tool.estado?.toLowerCase().includes('óptimo') ? 'bg-green-600/90 text-white' : 
                                              tool.estado?.toLowerCase().includes('mantenimiento') ? 'bg-yellow-600/90 text-white' : 
                                              'bg-red-600/90 text-white'}">
                                            ${tool.estado || '—'}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm text-[#D4A373]">
                                        <div><span class="text-[#A67B5B] font-semibold block">Familia</span>${tool.familia || '—'}</div>
                                        <div><span class="text-[#A67B5B] font-semibold block">Marca</span>${tool.marca || '—'}</div>
                                        <div><span class="text-[#A67B5B] font-semibold block">Stock</span>${tool.stock ?? 0}</div>
                                        <div><span class="text-[#A67B5B] font-semibold block">Calibración</span>${tool.fecha_calibracion || '—'}</div>
                                    </div>

                                    ${tool.observaciones && tool.observaciones !== '—' ? `
                                    <div class="pt-2 border-t border-[#A67B5B]/20">
                                        <p class="text-sm text-gray-300">
                                            <span class="text-[#A67B5B] font-semibold block mb-1">Observaciones:</span>
                                            ${tool.observaciones}
                                        </p>
                                    </div>` : ''}
                                </div>
                            `;

                            toolsGrid.appendChild(card);
                        });
                    })
                    .catch(err => {
                        console.error('Error cargando herramientas:', err);
                        toolsGrid.innerHTML = '<p class="col-span-full text-center text-2xl text-red-400">Error al cargar herramientas</p>';
                    });
            }

            loadTools();

            // ── Alternar modo ─────────────────────────────────────
            function updateMode() {
                if (usingCamera) {
                    toggleButton.innerHTML = 'Modo: Cámara <i class="fas fa-camera ml-3"></i>';
                    scannerContainer.classList.remove('hidden');
                    barcodeInput.blur();
                    if (!quaggaStarted) initCamera();
                } else {
                    toggleButton.innerHTML = 'Modo: Escáner Físico <i class="fas fa-barcode ml-3"></i>';
                    scannerContainer.classList.add('hidden');
                    barcodeInput.focus();
                    if (quaggaStarted) {
                        Quagga.stop();
                        quaggaStarted = false;
                    }
                }
            }

            toggleButton.addEventListener('click', () => {
                usingCamera = !usingCamera;
                updateMode();
            });

            updateMode();

            // ── Cámara + Quagga ───────────────────────────────────
            function initCamera() {
                if (quaggaStarted) return;

                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment', 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 }
                    } 
                })
                .then(stream => {
                    const video = document.getElementById('live-video');
                    video.srcObject = stream;
                    video.play()
                        .then(() => console.log("Video play OK"))
                        .catch(e => console.warn("Play warning (normal en algunos móviles):", e));
                    startQuagga();
                    quaggaStarted = true;
                })
                .catch(err => {
                    console.error("Error cámara:", err);
                    resultDiv.innerHTML = '<p class="text-yellow-400 mt-4 text-center">No se pudo abrir la cámara. Verifica permisos o usa lector físico.</p>';
                    usingCamera = false;
                    updateMode();
                });
            }

            function startQuagga() {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#scanner-container'),
                        constraints: {
                            facingMode: "environment"
                        }
                    },
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    },
                    numOfWorkers: navigator.hardwareConcurrency ? Math.min(navigator.hardwareConcurrency, 4) : 2,
                    frequency: 20,
                    decoder: {
                        readers: ["code_128_reader"],
                        multiple: false
                    },
                    locate: true
                }, err => {
                    if (err) {
                        console.error("Quagga init error:", err);
                        resultDiv.innerHTML = '<p class="text-red-400 mt-4">Error inicializando Quagga: ' + err + '</p>';
                        return;
                    }
                    console.log("Quagga iniciado correctamente");
                    Quagga.start();
                });

                Quagga.onProcessed(result => {
                    const drawingCtx = Quagga.canvas.ctx.overlay;
                    const drawingCanvas = Quagga.canvas.dom.overlay;

                    if (result) {
                        drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
                        if (result.boxes) {
                            result.boxes.forEach(box => {
                                Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "purple", lineWidth: 2});
                            });
                        }
                        if (result.box) {
                            Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                        }
                        if (result.codeResult) {
                            Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                        }
                    }
                });

                Quagga.onDetected(data => {
                    const now = Date.now();
                    let code = (data.codeResult?.code || '').trim().toUpperCase();
                    code = code.replace(/\s+/g, '');

                    console.log("[DEBUG] Código detectado crudo:", code, " - Formato:", data.codeResult?.format);

                    if (!code || (code === lastDetectedCode && now - lastDetectionTime < cooldown)) return;

                    if (code) {
                        lastDetectedCode = code;
                        lastDetectionTime = now;
                        processScan(code);

                        scannerContainer.classList.add('border-green-500', 'shadow-2xl', 'shadow-green-500/50');
                        setTimeout(() => scannerContainer.classList.remove('border-green-500', 'shadow-2xl', 'shadow-green-500/50'), 3000);
                    }
                });
            }

            // ── Procesar escaneo ──────────────────────────────────
            function processScan(code) {
                fetch('/scan/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code })
                })
                .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
                .then(data => {
                    if (data.success) {
                        const isSalida = data.action === 'salida';

                        // Mostrar resultado
                        resultDiv.innerHTML = `
                            <div class="text-6xl mb-4">${isSalida ? '🚪' : '🔙'}</div>
                            <div class="text-4xl font-bold ${isSalida ? 'text-red-400' : 'text-green-400'}">
                                ${isSalida ? 'SALIDA' : 'DEVOLUCIÓN'} REGISTRADA
                            </div>
                            <div class="text-2xl mt-4 text-[#D4A373]">${data.tool_name || 'Herramienta'}</div>
                            <div class="text-4xl font-mono mt-4 text-[#E3BC9A]">${data.code}</div>
                            <div class="text-xl mt-4 text-[#A67B5B]">
                                Stock actual: <span class="font-bold">${data.new_stock ?? '?'}</span>
                            </div>
                        `;

                        // Agregar al historial local
                        movimientos.unshift({
                            code: data.code,
                            tool_name: data.tool_name || 'Herramienta',
                            action: data.action,
                            timestamp: new Date().toISOString()
                        });

                        // Limitar a MAX_MOVIMIENTOS
                        if (movimientos.length > MAX_MOVIMIENTOS) {
                            movimientos = movimientos.slice(0, MAX_MOVIMIENTOS);
                        }

                        localStorage.setItem('ultimosMovimientos', JSON.stringify(movimientos));
                        renderHistorial();

                        loadTools();

// Limpia el mensaje después
setTimeout(() => {
    resultDiv.innerHTML = '';
}, 5000);
                    } else {
                        resultDiv.innerHTML = `<div class="text-red-400 text-3xl">❌ ${data.message || 'Error'}</div>`;
                    }
                })
                .catch(err => {
                    resultDiv.innerHTML = `<div class="text-red-400 text-3xl">Error: ${err}</div>`;
                });
            }

            // ── Lector físico ─────────────────────────────────────
            barcodeInput.addEventListener('input', e => {
                const code = barcodeInput.value.trim().toUpperCase();
                if (code) {  // Temporal: acepta cualquier código para pruebas
                    processScan(code);
                    barcodeInput.value = '';
                }
                if (barcodeInput.value.length > 15) barcodeInput.value = '';
            });

            setInterval(() => {
                if (!usingCamera && document.activeElement !== barcodeInput) {
                    barcodeInput.focus();
                }
            }, 600);

            setInterval(loadTools, 10000);
        });
    </script>

</body>
</html>