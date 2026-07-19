<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Expirada — {{ \App\Models\Setting::get('site_name', 'Tu Código') }}</title>
    <meta name="description" content="Aviso de expiración de membresía">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #050510;
            display: flex;
            flex-direction: column;
            color: white;
            overflow-x: hidden;
        }

        /* ═══ GRID BACKGROUND ═══ */
        .bg-scene {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(239,68,68,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(239,68,68,0.03) 1px, transparent 1px);
            background-size: 56px 56px;
        }
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            animation: drift 10s ease-in-out infinite alternate;
        }
        .orb-1 { width:600px;height:600px;background:radial-gradient(circle,rgba(239,68,68,0.16) 0%,transparent 70%);top:-150px;left:-100px;animation-delay:0s; }
        .orb-2 { width:500px;height:500px;background:radial-gradient(circle,rgba(236,72,153,0.10) 0%,transparent 70%);bottom:-120px;right:-80px;animation-delay:-4s; }
        .orb-3 { width:350px;height:350px;background:radial-gradient(circle,rgba(220,38,38,0.08) 0%,transparent 70%);top:40%;left:55%;animation-delay:-7s; }
        @keyframes drift { from{transform:translate(0,0) scale(1);} to{transform:translate(25px,-18px) scale(1.04);} }

        /* ═══ LAYOUT ═══ */
        .page-wrapper {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .expired-container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            border-radius: 1.75rem;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(239,68,68,0.1), 0 0 80px rgba(239,68,68,0.06);
            background: linear-gradient(145deg, rgba(14,8,20,0.98) 0%, rgba(8,4,10,1) 100%);
            position: relative;
            text-align: center;
            padding: 4rem 3rem;
        }
        
        .expired-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ef4444, #f43f5e, #e11d48);
        }

        .expired-icon {
            width: 5rem; height: 5rem;
            background: linear-gradient(135deg, rgba(239,68,68,0.22), rgba(244,63,94,0.12));
            border: 1px solid rgba(239,68,68,0.35);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 30px rgba(239,68,68,0.2);
            margin: 0 auto 2rem auto;
            color: #f87171;
        }

        .expired-title {
            font-size: 2rem;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 1rem;
        }
        
        .expired-desc {
            font-size: 1rem;
            color: rgba(148,163,184,0.85);
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .contact-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .contact-text {
            font-size: 0.85rem;
            color: rgba(203,213,225,0.8);
            margin-bottom: 1rem;
        }

        .btn-whatsapp {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none; border-radius: 0.875rem;
            color: white; font-size: 0.95rem; font-weight: 700;
            font-family: 'Inter', sans-serif;
            padding: 0.9rem 2rem;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(16,185,129,0.38), 0 0 0 1px rgba(16,185,129,0.15);
            transition: all 0.25s ease;
        }
        .btn-whatsapp:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(16,185,129,0.45); }

        .btn-logout {
            background: transparent;
            border: none;
            color: rgba(148,163,184,0.6);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s;
            text-decoration: underline;
            text-decoration-color: rgba(148,163,184,0.3);
            text-underline-offset: 4px;
        }
        .btn-logout:hover { color: #f8fafc; }

    </style>
</head>
<body>
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Tu Código');
        $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
        // O usar alguna configuración específica si existe
    @endphp

    <!-- Background -->
    <div class="bg-scene"></div>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="page-wrapper">
        <div class="main-content">
            <div class="expired-container">
                
                <div class="expired-icon">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h1 class="expired-title">Suscripción Expirada</h1>
                
                <p class="expired-desc">
                    Tu acceso como Franquicia ha sido suspendido debido a que el período de tu suscripción y sus días de gracia han finalizado. 
                    Tus clientes e inventario están a salvo, pero necesitas renovar para recuperar el acceso.
                </p>

                <div class="contact-box">
                    <p class="contact-text">Para renovar tu suscripción y reactivar tu panel, por favor contacta a la administración.</p>
                    
                    @if($whatsappNumber)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text=Hola,%20necesito%20renovar%20mi%20suscripci%C3%B3n%20de%20franquicia." target="_blank" class="btn-whatsapp">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Contactar Soporte
                    </a>
                    @else
                    <div style="background: rgba(168,85,247,0.1); border: 1px solid rgba(168,85,247,0.2); border-radius: 0.5rem; padding: 1rem; color: #e2e8f0; font-size: 0.9rem;">
                        Por favor, contacta directamente con el Súper Administrador del sistema.
                    </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        Cerrar Sesión y Salir
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
