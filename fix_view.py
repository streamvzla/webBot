import re

with open('resources/views/admin/query/index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Extends
content = content.replace("@extends('client.layouts.app')", "@extends('admin.layouts.app')")

# 2. Session keys
content = content.replace("session('email_body')", "session('reseller_email_body')")
content = content.replace("session('temp_code_expiry')", "session('reseller_temp_code_expiry')")
content = content.replace("session('email_received_at')", "session('reseller_email_received_at')")
content = content.replace("session('email_is_html')", "session('reseller_email_is_html')")

# 3. Route links
content = content.replace("route('client.query.post')", "route('admin.query.post')")
content = content.replace("route('client.query.limit')", "route('admin.query.limit')")
content = content.replace("route('client.query.clear')", "route('admin.query.clear')")
content = content.replace("route('client.query.code')", "route('admin.query.code')")

# 4. Foreach loop for emails
old_loop = "foreach($client->allowedEmails()->where('is_active', true)->orderBy('email')->get() as $email)"
new_loop = "foreach($allowedEmails as $email)"
content = content.replace(old_loop, new_loop)

# 5. Hero stats block
old_stats = '''<div style="text-align:right;">
                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.8);margin-bottom:0.25rem;">Consultas restantes</div>
                <div style="font-size:1.75rem;font-weight:900;color:#fff;line-height:1;">{{ auth('client')->user()->query_limit - auth('client')->user()->query_count }}</div>
                <div style="font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.15rem;">de {{ auth('client')->user()->query_limit }} hoy</div>
            </div>'''
new_stats = '''<div style="text-align:right;">
                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.8);margin-bottom:0.25rem;">Estado</div>
                <div style="font-size:1.25rem;font-weight:900;color:#34d399;line-height:1;">Ilimitadas</div>
                <div style="font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.15rem;">Modo Administrador</div>
            </div>'''
content = content.replace(old_stats, new_stats)

# 6. Auth references in widget
content = content.replace("$client = auth('client')->user();", "$user = auth()->user();")

# 7. Disable limit logic in JS
content = content.replace("updateLimits(); // Cargar límites reales al iniciar", "// Límite removido para admin")

# Override updateLimits JS function to do nothing
content = re.sub(r"function updateLimits\(\) \{.*?\n    \}", "function updateLimits() { /* Nada para admin */ }", content, flags=re.DOTALL)

with open('resources/views/admin/query/index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('File updated successfully.')
