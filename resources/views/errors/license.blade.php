<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error de Licencia' }} — Tu Código</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #050510;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(168,85,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168,85,247,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }
        .bg-orb-1 {
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, transparent 70%);
            top: -100px; left: -100px;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .bg-orb-2 {
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(236,72,153,0.1) 0%, transparent 70%);
            bottom: -100px; right: -50px;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .card {
            position: relative;
            z-index: 10;
            background: linear-gradient(135deg, rgba(15,10,40,0.97) 0%, rgba(10,5,30,0.98) 100%);
            border: 1px solid rgba(168,85,247,0.2);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6), 0 0 60px rgba(168,85,247,0.06);
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent);
        }
        .card::after {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(236,72,153,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .icon-wrap {
            width: 5rem; height: 5rem;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(236,72,153,0.15));
            border: 1.5px solid rgba(239,68,68,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(239,68,68,0.15);
            position: relative;
            z-index: 1;
        }
        .icon-wrap svg { width: 2.5rem; height: 2.5rem; color: #f87171; }
        .access-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 1.25rem;
            position: relative;
            z-index: 1;
        }
        .access-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #ef4444;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        h1 {
            font-size: 1.625rem;
            font-weight: 800;
            margin-bottom: 0.625rem;
            background: linear-gradient(135deg, #e2e8f0, #f87171);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }
        .message {
            color: rgba(148,163,184,0.8);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .hint-box {
            background: rgba(168,85,247,0.07);
            border: 1px solid rgba(168,85,247,0.2);
            border-radius: 0.875rem;
            padding: 1rem 1.25rem;
            font-size: 0.8rem;
            color: rgba(196,181,253,0.9);
            text-align: left;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }
        .hint-box strong { color: #c4b5fd; display: block; margin-bottom: 0.375rem; }
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.2), rgba(236,72,153,0.2), transparent);
            margin: 1.5rem 0;
        }
        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899);
            background-size: 200%;
            border: none;
            border-radius: 0.875rem;
            color: white;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.75rem 1.75rem;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(168,85,247,0.35);
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }
        .contact-btn:hover {
            box-shadow: 0 8px 30px rgba(168,85,247,0.55);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-orb-1"></div>
    <div class="bg-orb-2"></div>

    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>

        <div class="access-badge">Acceso Denegado</div>

        <h1>{{ $title ?? 'Licencia Inválida' }}</h1>
        <p class="message">{{ $message ?? 'La licencia de este sistema no es válida o ha expirado. Contacta al proveedor para renovarla.' }}</p>

        @if(!empty($hint))
        <div class="hint-box">
            <strong>💡 ¿Cómo resolverlo?</strong>
            {{ $hint }}
        </div>
        @endif

        <div class="divider"></div>

        <a href="mailto:soporte@tu-codigo.com" class="contact-btn">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Contactar Soporte
        </a>

        <p style="margin-top:1.5rem;font-size:0.7rem;color:rgba(100,116,139,0.5);">ID de Dominio: <code style="color:rgba(168,85,247,0.6);">{{ request()->getHost() }}</code></p>
    </div>
</body>
</html>
