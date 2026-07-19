<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada</title>
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
        .number { font-size: 8rem; font-weight: 900; line-height: 1; background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899); border: none; border-radius: 0.875rem; color: white; font-weight: 700; font-size: 0.875rem; padding: 0.75rem 1.75rem; cursor: pointer; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 20px rgba(168,85,247,0.35); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(168,85,247,0.5); }
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div class="glow-top"></div>
    <div class="glow-bottom"></div>
    <div class="card">
        <div class="number">404</div>
        <h1 style="font-size:1.5rem;font-weight:800;color:white;margin:0.5rem 0;">Página no encontrada</h1>
        <p style="color:rgba(148,163,184,0.7);font-size:0.9rem;line-height:1.6;margin-bottom:2rem;">El recurso que buscas no existe o fue movido a otra ubicación. Verifica la URL e intenta de nuevo.</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Regresar
        </a>
    </div>
</body>
</html>
