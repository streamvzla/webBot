<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta tu Código - Sistema de Verificación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-bg {
            background-image: url('{{ asset('assets/2VQ6A9a.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .login-overlay {
            background: rgba(15, 23, 42, 0.85);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .platform-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .loading-spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid #fff;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="login-bg min-h-screen text-white relative">
    <div class="login-overlay absolute inset-0"></div>
    <div class="container mx-auto px-4 py-8 max-w-4xl relative z-10">
        <!-- Header -->
        <header class="text-center mb-10">
            <div class="mb-6">
                <svg class="w-16 h-16 mx-auto text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-2">Consulta tu Código</h1>
            <p class="text-gray-400">Ingresa tu correo electrónico y selecciona la plataforma para obtener tu código de verificación</p>
        </header>

        <!-- Contact Buttons -->
        <div class="flex justify-center gap-4 mb-8 flex-wrap">
            @if($contactTelegram)
            <a href="{{ $contactTelegram }}" target="_blank" class="bg-[#0088cc] hover:bg-[#0077b3] text-white px-6 py-2 rounded-full font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.696.064-1.225-.46-1.901-.903-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                Telegram
            </a>
            @endif

            @if($contactWhatsapp)
            <a href="{{ $contactWhatsapp }}?text={{ urlencode($whatsappMessage) }}" target="_blank" class="bg-[#25D366] hover:bg-[#128C7E] text-white px-6 py-2 rounded-full font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            @endif

            @if($webUrl)
            <a href="{{ $webUrl }}" target="_blank" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-full font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                Web
            </a>
            @endif
        </div>

        <!-- Query Form -->
        <div class="glass-card rounded-2xl p-8 mb-8">
            <form id="queryForm" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition"
                        placeholder="tu@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Plataforma</label>
                    <div id="platformsGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Platforms will be loaded here -->
                        <div class="text-center text-gray-400 col-span-full py-8">
                            Cargando plataformas...
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn" disabled
                    class="w-full bg-yellow-400 hover:bg-yellow-500 disabled:bg-gray-600 disabled:cursor-not-allowed text-black font-bold py-4 rounded-lg transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Buscar Código
                </button>
            </form>
        </div>

        <!-- Result -->
        <div id="resultContainer" class="hidden glass-card rounded-2xl p-8 text-center">
            <div id="resultContent"></div>
        </div>

        <!-- Error -->
        <div id="errorContainer" class="hidden glass-card rounded-2xl p-8 text-center bg-red-500/20 border-red-500/30">
            <div id="errorContent"></div>
        </div>

        <!-- Footer -->
        <footer class="text-center text-gray-500 text-sm mt-8">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Tu Código') }} (tu-codigo.com). Todos los derechos reservados.</p>
            @if($vendorId)
            <p class="mt-1">ID Vendedor: {{ $vendorId }}</p>
            @endif
        </footer>
    </div>
    <div class="fixed bottom-0 right-0 text-right py-4 pr-4 relative z-10">
        <small class="text-gray-500">Desarrollado Por WinicSistem www.winic.es | Versión 2.0</small>
    </div>

    <script>
        // Load platforms on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPlatforms();
        });

        let selectedPlatformId = null;

        async function loadPlatforms() {
            try {
                const response = await fetch('/api/platforms');
                const data = await response.json();

                if (data.success && data.data.length > 0) {
                    const grid = document.getElementById('platformsGrid');
                    grid.innerHTML = '';

                    data.data.forEach(platform => {
                        const card = document.createElement('div');
                        card.className = 'platform-card bg-white/5 border border-white/10 rounded-xl p-4 cursor-pointer transition-all duration-300';
                        card.onclick = () => selectPlatform(platform.id, card);
                        card.innerHTML = `
                            <div class="text-center">
                                <div class="text-3xl mb-2">${platform.logo || '📱'}</div>
                                <div class="font-medium text-sm">${platform.name}</div>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                }
            } catch (error) {
                console.error('Error loading platforms:', error);
            }
        }

        function selectPlatform(platformId, element) {
            selectedPlatformId = platformId;
            document.querySelectorAll('.platform-card').forEach(card => {
                card.classList.remove('border-yellow-400', 'bg-white/20');
                card.classList.add('border-white/10', 'bg-white/5');
            });
            element.classList.remove('border-white/10', 'bg-white/5');
            element.classList.add('border-yellow-400', 'bg-white/20');
            document.getElementById('submitBtn').disabled = false;
        }

        document.getElementById('queryForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');

            if (!email || !selectedPlatformId) {
                showError('Por favor completa todos los campos');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="loading-spinner"></div> Buscando...';

            hideResult();
            hideError();

            try {
                const response = await fetch('/api/query', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        email: email,
                        platform_id: selectedPlatformId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showResult(data.data);
                } else {
                    if (data.retry_after) {
                        showError(`Por favor espera ${data.retry_after} minutos antes de consultar nuevamente`);
                    } else {
                        showError(data.message || 'Error al buscar el código');
                    }
                }
            } catch (error) {
                showError('Error de conexión. Por favor intenta nuevamente.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Buscar Código`;
            }
        });

        function showResult(data) {
            const container = document.getElementById('resultContainer');
            const content = document.getElementById('resultContent');

            content.innerHTML = `
                <div class="mb-6">
                    <div class="text-6xl mb-4">✅</div>
                    <h2 class="text-2xl font-bold text-green-400 mb-2">¡Código Encontrado!</h2>
                    <p class="text-gray-300">Tu código para ${data.platform}</p>
                </div>
                <div class="bg-white/10 rounded-xl p-6 mb-6">
                    <p class="text-sm text-gray-400 mb-2">Tu código es:</p>
                    <p class="text-4xl font-mono font-bold text-yellow-400 tracking-wider">${data.code}</p>
                </div>
                <p class="text-gray-400 text-sm">Enviado a: ${data.email}</p>
            `;
            container.classList.remove('hidden');
            container.scrollIntoView({ behavior: 'smooth' });
        }

        function showError(message) {
            const container = document.getElementById('errorContainer');
            const content = document.getElementById('errorContent');

            content.innerHTML = `
                <div class="text-4xl mb-4">❌</div>
                <h2 class="text-xl font-bold text-red-400 mb-2">Error</h2>
                <p class="text-gray-300">${message}</p>
            `;
            container.classList.remove('hidden');
            container.scrollIntoView({ behavior: 'smooth' });
        }

        function hideResult() {
            document.getElementById('resultContainer').classList.add('hidden');
        }

        function hideError() {
            document.getElementById('errorContainer').classList.add('hidden');
        }
    </script>
</body>
</html>
