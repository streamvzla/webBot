@extends('admin.layouts.app')

@section('title', 'Editar Cuenta de Correo - Panel de Administración')

@section('header', 'Editar Cuenta de Correo')
@section('description', 'Modifica la configuración de la cuenta')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        background-color: #1e293b !important;
        border: 1px solid #475569 !important;
        border-radius: 0.5rem !important;
        min-height: 48px !important;
        padding: 4px 8px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3b82f6 !important;
        border: none !important;
        border-radius: 9999px !important;
        color: white !important;
        padding: 4px 10px !important;
        margin: 3px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white !important;
        font-size: 14px !important;
        font-weight: bold !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 16px !important;
        height: 16px !important;
        border-radius: 50% !important;
        background-color: rgba(0,0,0,0.2) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: rgba(0,0,0,0.4) !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        color: white !important;
        min-width: 200px !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: #9ca3af !important;
    }
    .select2-dropdown {
        background-color: #1e293b !important;
        border: 1px solid #475569 !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-results__option {
        color: white !important;
        padding: 10px 12px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #eab308 !important;
        color: black !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #475569 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #0f172a !important;
        border: 1px solid #475569 !important;
        border-radius: 0.375rem !important;
        color: white !important;
        padding: 10px 12px !important;
        margin-bottom: 8px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        outline: none !important;
        border-color: #eab308 !important;
    }
    .select2-container--default .select2-selection__placeholder {
        color: #9ca3af !important;
    }
</style>
@endpush

@section('content')
<style>
    .form-card { background: linear-gradient(135deg, rgba(15,10,40,0.88) 0%, rgba(10,5,25,0.92) 100%); border: 1px solid rgba(168,85,247,0.15); border-radius: 1.5rem; padding: 2rem; position: relative; overflow: hidden; }
    .form-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent); }
    .form-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(148,163,184,0.7); margin-bottom: 0.5rem; }
    .form-input { width: 100%; padding: 0.75rem 1rem; background: rgba(255,255,255,0.04); border: 1.5px solid rgba(168,85,247,0.15); border-radius: 0.75rem; color: white; font-size: 0.9rem; outline: none; transition: all 0.2s; font-family: inherit; }
    .form-input:focus { border-color: rgba(168,85,247,0.5); background: rgba(168,85,247,0.05); box-shadow: 0 0 0 3px rgba(168,85,247,0.1); }
    .form-input::placeholder { color: rgba(100,116,139,0.5); }
    .form-input.is-error { border-color: rgba(239,68,68,0.5) !important; }
    .form-select { width: 100%; padding: 0.75rem 1rem; background: rgba(255,255,255,0.04); border: 1.5px solid rgba(168,85,247,0.15); border-radius: 0.75rem; color: white; font-size: 0.9rem; outline: none; cursor: pointer; transition: all 0.2s; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(168,85,247,0.6)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.875rem center; background-size: 1.1rem; padding-right: 2.5rem; }
    .form-select:focus { border-color: rgba(168,85,247,0.5); box-shadow: 0 0 0 3px rgba(168,85,247,0.1); }
    .form-select option { background: #0d0920; color: white; }
    .form-hint { font-size: 0.78rem; color: rgba(100,116,139,0.6); margin-top: 0.35rem; }
    .form-error { font-size: 0.78rem; color: #f87171; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.3rem; }
    .form-checkbox { width: 1.1rem; height: 1.1rem; border-radius: 0.3rem; cursor: pointer; accent-color: #a855f7; }
    .btn-submit { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899); border: none; border-radius: 0.875rem; color: white; font-weight: 700; font-size: 0.9rem; padding: 0.875rem 2rem; cursor: pointer; box-shadow: 0 4px 20px rgba(168,85,247,0.35); transition: all 0.25s; width: 100%; }
    .btn-submit:hover { box-shadow: 0 8px 30px rgba(168,85,247,0.5); transform: translateY(-2px); }
    .btn-cancel { display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.875rem; color: rgba(148,163,184,0.8); font-weight: 600; font-size: 0.9rem; padding: 0.875rem 2rem; text-decoration: none; transition: all 0.2s; width: 100%; text-align: center; }
    .btn-cancel:hover { background: rgba(255,255,255,0.09); color: white; }
</style>
<div class="max-w-2xl">
    <form action="{{ route('admin.email-accounts.update', $emailAccount) }}" method="POST" class="form-card" style="display:flex;flex-direction:column;gap:1.25rem;">
        @csrf
        @method('PUT')

        <div>
            <label for="email" class="form-label">Correo Electrónico *</label>
            <input type="email" id="email" name="email" required value="{{ old('email', $emailAccount->email) }}"
                class="form-input"
                placeholder="cuenta@servidor.com">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="username" class="form-label">Usuario IMAP *</label>
            <input type="text" id="username" name="username" required value="{{ old('username', $emailAccount->username) }}"
                class="form-input"
                placeholder="usuario o correo completo">
        </div>

        <div>
            <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" id="password" name="password"
                class="form-input"
                placeholder="••••••••">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="imap_host" class="form-label">Servidor IMAP *</label>
                <input type="text" id="imap_host" name="imap_host" required value="{{ old('imap_host', $emailAccount->imap_host) }}"
                    class="form-input"
                    placeholder="imap.servidor.com">
            </div>
            <div>
                <label for="imap_port" class="form-label">Puerto IMAP *</label>
                <input type="number" id="imap_port" name="imap_port" required value="{{ old('imap_port', $emailAccount->imap_port) }}"
                    class="form-input">
            </div>
        </div>

        <div>
            <label for="imap_encryption" class="form-label">Encriptación</label>
            <select id="imap_encryption" name="imap_encryption"
                class="form-select">
                <option value="ssl" {{ old('imap_encryption', $emailAccount->imap_encryption) === 'ssl' ? 'selected' : '' }}>SSL/TLS</option>
                <option value="tls" {{ old('imap_encryption', $emailAccount->imap_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="" {{ old('imap_encryption', $emailAccount->imap_encryption) === '' ? 'selected' : '' }}>Ninguna</option>
            </select>
        </div>

        <div>
            <label for="user_ids" class="form-label">Usuarios Asignados</label>
            <select id="user_ids" name="user_ids[]" multiple="multiple" class="w-full">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', $selectedUserIds)) ? 'selected' : '' }}>
                        {{ $user->username }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            <p class="form-hint">Busca y selecciona usuarios. Los seleccionados aparecerán como tags.</p>
        </div>

        <div class="flex items-center">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $emailAccount->is_active) ? 'checked' : '' }}
                class="form-checkbox">
            <label for="is_active" class="ml-2 text-sm text-gray-300 cursor-pointer">Cuenta activa</label>
        </div>

        <div class="flex gap-4 pt-4">
            <a href="{{ route('admin.email-accounts.index') }}" class="btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="btn-submit">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#user_ids').select2({
            theme: 'default',
            placeholder: 'Buscar usuarios...',
            allowClear: true,
            closeOnSelect: false,
            language: {
                searching: function() {
                    return 'Buscando usuarios...';
                },
                noResults: function() {
                    return 'No se encontraron usuarios';
                },
                inputTooShort: function() {
                    return 'Escribe para buscar...';
                },
                loadingMore: function() {
                    return 'Cargando más resultados...';
                }
            },
            minimumInputLength: 0
        });
    });
</script>
@endpush
@endsection
