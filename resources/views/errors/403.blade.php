<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Acceso denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #050510; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .grid-bg { position: fixed; inset: 0; background-image: linear-gradient(rgba(168,85,247,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(168,85,247,0.04) 1px, transparent 1px); background-size: 50px 50px; z-index: 0; }
        .glow-top    { position: fixed; top: -100px; right: -100px;  width: 500px; height: 500px; background: radial-gradient(circle, rgba(124,58,237,0.15), transparent 70%); border-radius: 50%; z-index: 0; }
        .glow-bottom { position: fixed; bottom: -100px; left: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(236,72,153,0.1),  transparent 70%); border-radius: 50%; z-index: 0; }
        .card {
            position: relative; z-index: 1;
            background: linear-gradient(135deg, rgba(15,10,40,0.95), rgba(10,5,25,0.98));
            border: 1px solid rgba(168,85,247,0.2);
            border-radius: 2rem;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 480px; width: 90%;
            box-shadow: 0 0 0 1px rgba(168,85,247,0.05), 0 40px 100px rgba(0,0,0,0.8), 0 0 80px rgba(168,85,247,0.06);
            animation: cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent); border-radius: 2rem 2rem 0 0; }
        @keyframes cardIn { from { opacity: 0; transform: scale(0.9) translateY(30px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .lock-icon { width: 5rem; height: 5rem; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(168,85,247,0.1)); border: 1.5px solid rgba(239,68,68,0.3); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); } 50% { box-shadow: 0 0 0 12px rgba(239,68,68,0); } }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899); border: none; border-radius: 0.875rem; color: white; font-weight: 700; font-size: 0.875rem; padding: 0.75rem 1.75rem; cursor: pointer; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 20px rgba(168,85,247,0.35); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(168,85,247,0.5); }
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div class="glow-top"></div>
    <div class="glow-bottom"></div>
    <div class="card">
        <div class="lock-icon">
            <svg style="width:2.25rem;height:2.25rem;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#f87171;margin-bottom:0.5rem;">Acceso Denegado</p>
        <h1 style="font-size:1.5rem;font-weight:800;background:linear-gradient(135deg,#f1f5f9,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.75rem;">No tienes permiso</h1>
        <p style="color:rgba(148,163,184,0.7);font-size:0.9rem;line-height:1.6;margin-bottom:2rem;">No estás autorizado para acceder a este recurso. Si crees que es un error, contacta al administrador.</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Regresar
        </a>
    </div>
</body>
</html>
