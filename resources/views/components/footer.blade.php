@props(['type' => 'default'])

@php
    $siteName = \App\Models\Setting::get('site_name', 'Tu Código');
@endphp

@if($type === 'admin' || $type === 'public')
    <footer style="margin-top:auto;padding:1.5rem 1.75rem;border-top:1px solid rgba(168,85,247,0.08);background:linear-gradient(135deg,rgba(10,5,28,0.7),rgba(5,2,15,0.8));display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;width:100%;">
        <div>
            <p style="font-size:0.75rem;color:rgba(100,116,139,0.7);margin-bottom:0.25rem;">&copy; {{ date('Y') }} {{ $siteName }} — Todos los derechos reservados.</p>
            <p style="font-size:0.7rem;color:rgba(100,116,139,0.5);font-weight:500;">Desarrollado con <span style="color:#a855f7;">&#10084;</span> por <span style="background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;">Luis Martinez, desde Valencia-Venezuela</span><svg width="16" height="11" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:-2px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.3);margin-left:4px;"><rect width="36" height="8" fill="#FFCC00"/><rect y="8" width="36" height="8" fill="#00247D"/><rect y="16" width="36" height="8" fill="#CF142B"/><g fill="#fff"><circle cx="11.5" cy="13.5" r="0.8"/><circle cx="13" cy="11.5" r="0.8"/><circle cx="15" cy="10" r="0.8"/><circle cx="17" cy="9.2" r="0.8"/><circle cx="19" cy="9.2" r="0.8"/><circle cx="21" cy="10" r="0.8"/><circle cx="23" cy="11.5" r="0.8"/><circle cx="24.5" cy="13.5" r="0.8"/></g></svg></p>
        </div>
        <div style="display:flex;align-items:center;gap:0.35rem;">
            <span style="width:0.35rem;height:0.35rem;background:#34d399;border-radius:50%;box-shadow:0 0 4px rgba(52,211,153,0.6);"></span>
            <span style="font-size:0.65rem;color:rgba(100,116,139,0.45);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">v3.0 Enterprise</span>
        </div>
    </footer>
@elseif($type === 'client')
    <footer class="page-footer">
        <div class="footer-left">
            &copy; {{ date('Y') }} <strong>{{ $siteName }}</strong> &mdash; Todos los derechos reservados.
        </div>
        <div class="footer-center">
            <span>Desarrollado con <span style="color:#a855f7;">&#10084;</span> por</span>
            <strong style="background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;">Luis Martinez, desde Valencia-Venezuela</strong><svg width="16" height="11" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:-2px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.3);margin-left:4px;"><rect width="36" height="8" fill="#FFCC00"/><rect y="8" width="36" height="8" fill="#00247D"/><rect y="16" width="36" height="8" fill="#CF142B"/><g fill="#fff"><circle cx="11.5" cy="13.5" r="0.8"/><circle cx="13" cy="11.5" r="0.8"/><circle cx="15" cy="10" r="0.8"/><circle cx="17" cy="9.2" r="0.8"/><circle cx="19" cy="9.2" r="0.8"/><circle cx="21" cy="10" r="0.8"/><circle cx="23" cy="11.5" r="0.8"/><circle cx="24.5" cy="13.5" r="0.8"/></g></svg>
        </div>
        <div class="footer-right">
            <span class="footer-status"></span>
            <span class="footer-ver">Sistema Online &middot; v3.0 Enterprise</span>
        </div>
    </footer>
@elseif($type === 'login')
    <footer class="auth-footer" style="text-align:center; margin-top:2rem;">
        <p style="font-size:0.75rem;color:rgba(100,116,139,0.7);margin-bottom:0.25rem;">&copy; {{ date('Y') }} {{ $siteName }} — Todos los derechos reservados.</p>
        <p style="font-size:0.75rem;color:rgba(100,116,139,0.5);font-weight:500;">Desarrollado con <span style="color:#a855f7;">&#10084;</span> por <strong style="background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;">Luis Martinez, desde Valencia-Venezuela</strong><svg width="16" height="11" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:-2px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.3);margin-left:4px;"><rect width="36" height="8" fill="#FFCC00"/><rect y="8" width="36" height="8" fill="#00247D"/><rect y="16" width="36" height="8" fill="#CF142B"/><g fill="#fff"><circle cx="11.5" cy="13.5" r="0.8"/><circle cx="13" cy="11.5" r="0.8"/><circle cx="15" cy="10" r="0.8"/><circle cx="17" cy="9.2" r="0.8"/><circle cx="19" cy="9.2" r="0.8"/><circle cx="21" cy="10" r="0.8"/><circle cx="23" cy="11.5" r="0.8"/><circle cx="24.5" cy="13.5" r="0.8"/></g></svg></p>
    </footer>
@endif
