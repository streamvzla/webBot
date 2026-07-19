@extends('admin.layouts.app')

@section('title', 'Cuentas de Correo - Panel de Administración')

@section('header', 'Cuentas de Correo')
@section('description', 'Gestiona las cuentas IMAP para verificar códigos')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.email-accounts.create') }}" class="btn-submit">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Nueva Cuenta
    </a>
</div>

<div class="glass-card rounded-xl overflow-hidden">
    @if($emailAccounts->count() > 0)
        <table class="w-full">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Correo</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Servidor IMAP</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuarios Asignados</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($emailAccounts as $account)
                    <tr class="hover:bg-white/5">
                        <td class="px-6 py-4">
                            <p class="font-medium text-white">{{ $account->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-300">{{ $account->imap_host }}:{{ $account->imap_port }}</p>
                            <p class="text-xs text-gray-500">{{ $account->imap_encryption }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($account->users->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($account->users as $user)
                                        <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">
                                            {{ $user->username }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500 text-sm">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($account->is_active)
                                <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Activo</span>
                            @else
                                <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.email-accounts.edit', $account) }}" class="text-gray-400 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.email-accounts.destroy', $account) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition" data-confirm="Esta acción eliminará el correo de forma permanente." data-confirm-title="Eliminar Correo" data-confirm-btn="Sí, eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="p-8 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <p>No hay cuentas de correo configuradas</p>
            <a href="{{ route('admin.email-accounts.create') }}" class="text-yellow-400 hover:text-yellow-300 mt-2 inline-block">Agregar primera cuenta</a>
        </div>
    @endif
</div>
@endsection
