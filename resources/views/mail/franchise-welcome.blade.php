<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {{ $siteName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f0f1a; color: #e2e8f0; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: linear-gradient(135deg, #1e1b4b 0%, #1a1a2e 100%); border-radius: 20px; border: 1px solid rgba(168,85,247,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #7c3aed, #4f46e5); padding: 40px 32px; text-align: center; }
        .header .icon { width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .header h1 { font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 18px; font-weight: 700; color: #c084fc; margin-bottom: 16px; }
        .message { color: rgba(203,213,225,0.9); line-height: 1.7; font-size: 15px; margin-bottom: 28px; }
        .credentials-box { background: rgba(0,0,0,0.3); border: 1px solid rgba(168,85,247,0.4); border-radius: 14px; padding: 24px; margin-bottom: 28px; }
        .credentials-box h3 { color: #a78bfa; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 16px; }
        .credential-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .credential-row:last-child { border-bottom: none; }
        .credential-label { color: rgba(148,163,184,0.8); font-size: 13px; }
        .credential-value { font-family: 'Courier New', monospace; color: #f1f5f9; font-weight: 700; font-size: 15px; background: rgba(168,85,247,0.15); padding: 4px 10px; border-radius: 6px; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff !important; text-decoration: none; padding: 16px 32px; border-radius: 12px; font-weight: 700; font-size: 16px; margin: 0 auto 28px; letter-spacing: 0.5px; }
        .warning { background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
        .warning p { color: #fbbf24; font-size: 13px; line-height: 1.6; }
        .footer { text-align: center; padding: 24px 32px; border-top: 1px solid rgba(255,255,255,0.07); }
        .footer p { color: rgba(148,163,184,0.5); font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="icon">🚀</div>
                <h1>¡Tu panel está listo!</h1>
                <p>Bienvenido al sistema de gestión de {{ $siteName }}</p>
            </div>

            <div class="body">
                <p class="greeting">Hola, {{ $clientName }} 👋</p>
                <p class="message">
                    Tu cuenta de administrador ha sido creada exitosamente. A continuación encontrarás tus credenciales de acceso para ingresar al panel de control. Guárdalas en un lugar seguro.
                </p>

                <div class="credentials-box">
                    <h3>🔐 Tus Credenciales de Acceso</h3>
                    <div class="credential-row">
                        <span class="credential-label">Usuario / Email</span>
                        <span class="credential-value">{{ $userEmail }}</span>
                    </div>
                    <div class="credential-row">
                        <span class="credential-label">Contraseña Temporal</span>
                        <span class="credential-value">{{ $userPassword }}</span>
                    </div>
                </div>

                <a href="{{ $panelUrl }}" class="btn">
                    → Ingresar al Panel Ahora
                </a>

                <div class="warning">
                    <p>⚠️ <strong>Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña la primera vez que ingreses al panel. Ve a <strong>Perfil → Cambiar Contraseña</strong>.</p>
                </div>

                <p class="message" style="font-size:13px; color:rgba(148,163,184,0.6);">
                    Si tienes algún problema para acceder, no dudes en contactarnos. Estamos aquí para ayudarte.
                </p>
            </div>

            <div class="footer">
                <p>Este correo fue generado automáticamente por <strong>{{ $siteName }}</strong>.<br>
                Si no realizaste esta compra, ignora este mensaje.</p>
            </div>
        </div>
    </div>
</body>
</html>
