<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirma tu nuevo correo</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{background:#060614;font-family:'Segoe UI',Arial,sans-serif;color:#e2e8f0;padding:2rem 1rem;}
  .wrap{max-width:560px;margin:0 auto;}
  .card{background:linear-gradient(135deg,#0f0a28 0%,#08040f 100%);border:1px solid rgba(168,85,247,0.25);border-radius:16px;overflow:hidden;}
  .top-bar{height:3px;background:linear-gradient(90deg,#7c3aed,#a855f7,#ec4899);}
  .body{padding:2.5rem 2rem;}
  .logo{font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;margin-bottom:1.5rem;}
  .logo span{background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  h1{font-size:1.25rem;font-weight:800;color:white;margin-bottom:0.5rem;}
  p{font-size:0.9rem;color:rgba(148,163,184,0.85);line-height:1.7;margin-bottom:1rem;}
  .email-box{background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);border-radius:10px;padding:0.875rem 1.25rem;font-size:0.95rem;color:#c4b5fd;font-weight:700;text-align:center;margin:1.25rem 0;letter-spacing:0.02em;}
  .btn{display:block;text-align:center;background:linear-gradient(135deg,#7c3aed,#a855f7,#ec4899);color:white;font-weight:800;font-size:1rem;padding:1rem 2rem;border-radius:12px;text-decoration:none;margin:1.5rem 0;box-shadow:0 8px 25px rgba(168,85,247,0.4);}
  .link-fallback{font-size:0.75rem;color:rgba(100,116,139,0.8);word-break:break-all;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:8px;padding:0.75rem;margin-top:1rem;}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(168,85,247,0.2),transparent);margin:1.5rem 0;}
  .warn{background:rgba(251,191,36,0.07);border:1px solid rgba(251,191,36,0.2);border-radius:10px;padding:0.875rem 1rem;font-size:0.8rem;color:rgba(251,191,36,0.85);}
  .footer{padding:1.25rem 2rem;text-align:center;font-size:0.72rem;color:rgba(100,116,139,0.5);}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="top-bar"></div>
    <div class="body">
      <div class="logo">Nexus<span>Code</span></div>
      <h1>Confirma tu nuevo correo</h1>
      <p>Hola <strong style="color:white;">{{ $clientName }}</strong>, recibimos una solicitud para cambiar el correo electrónico asociado a tu cuenta.</p>
      <p>El nuevo correo que deseas usar es:</p>
      <div class="email-box">{{ $newEmail }}</div>
      <p>Para confirmar el cambio, haz clic en el botón a continuación. Este enlace expirará en <strong style="color:white;">60 minutos</strong>.</p>
      <a href="{{ $verificationUrl }}" class="btn">✓ Confirmar cambio de correo</a>
      <div class="warn">
        ⚠️ <strong>¿No solicitaste este cambio?</strong> Ignora este correo. Tu email actual seguirá siendo el mismo y ningún cambio se realizará.
      </div>
      <div class="divider"></div>
      <p style="font-size:0.8rem;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
      <div class="link-fallback">{{ $verificationUrl }}</div>
    </div>
    <div class="footer">
      Este mensaje fue enviado automáticamente. Por favor no respondas a este correo.<br>
      © {{ date('Y') }} NexusCode — Todos los derechos reservados.
    </div>
  </div>
</div>
</body>
</html>
