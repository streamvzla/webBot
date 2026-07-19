<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailAccount;
use App\Services\ImapConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = EmailAccount::with('user')
            ->when($request->search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('imap_host', 'like', "%{$search}%");
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            })
            ->when($request->authorized !== null && $request->authorized !== '', function ($query) use ($request) {
                return $query->where('is_authorized', $request->boolean('authorized'));
            });

        // Los usuarios con rol 'user' solo ven sus propios servidores
        if ($user && $user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $emailAccounts = $query->orderBy('email')->paginate(20);

        return view('admin.servers.index', compact('emailAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.servers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:email_accounts,email',
            'imap_username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_authorized' => 'boolean',
        ]);

        // Cifrar contraseña IMAP antes de guardar
        $encryptedPassword = Crypt::encryptString($validated['password']);

        EmailAccount::create([
            'email' => $validated['email'],
            'username' => $validated['imap_username'],
            'imap_password' => $encryptedPassword,
            'imap_host' => $validated['imap_host'],
            'imap_port' => $validated['imap_port'],
            'imap_encryption' => $validated['imap_encryption'] ?? 'ssl',
            'is_active' => $validated['is_active'] ?? true,
            'is_authorized' => $validated['is_authorized'] ?? false, // Por defecto no autorizado
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.servers.index')
            ->with('success', 'Servidor IMAP creado exitosamente. Espera a que un administrador lo autorice para poder usarlo.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $emailAccount = EmailAccount::findOrFail($id);
        return view('admin.servers.edit', compact('emailAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $emailAccount = EmailAccount::findOrFail($id);
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $emailAccount->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este servidor.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:email_accounts,email,' . $id,
            'imap_username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_authorized' => 'boolean',
        ]);

        // Cifrar nueva contraseña si se proporciona, mantener la actual si no
        $password = !empty($validated['password'])
            ? Crypt::encryptString($validated['password'])
            : $emailAccount->imap_password;

        // Solo admin puede cambiar is_authorized
        if ($user->role !== 'admin') {
            unset($validated['is_authorized']);
        }

        $emailAccount->update([
            'email' => $validated['email'],
            'username' => $validated['imap_username'],
            'imap_password' => $password,
            'imap_host' => $validated['imap_host'],
            'imap_port' => $validated['imap_port'],
            'imap_encryption' => $validated['imap_encryption'] ?? 'ssl',
            'is_active' => $validated['is_active'] ?? true,
            'is_authorized' => isset($validated['is_authorized']) ? $validated['is_authorized'] : $emailAccount->is_authorized,
        ]);

        return redirect()->route('admin.servers.index')
            ->with('success', 'Servidor IMAP actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $emailAccount = EmailAccount::findOrFail($id);
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $emailAccount->user_id !== $user->id) {
            abort(403, 'No tienes autorización para eliminar este servidor.');
        }

        $emailAccount->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', 'Servidor IMAP eliminado exitosamente.');
    }

    /**
     * Autorizar o desautorizar un servidor (solo para admins).
     */
    public function toggleAuthorization($id)
    {
        $user = auth()->user();

        // Solo administradores pueden autorizar servidores
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Solo los administradores pueden autorizar servidores.');
        }

        $emailAccount = EmailAccount::findOrFail($id);
        $emailAccount->is_authorized = !$emailAccount->is_authorized;
        $emailAccount->save();

        $message = $emailAccount->is_authorized
            ? 'Servidor autorizado exitosamente. Los usuarios ahora pueden usarlo.'
            : 'Servidor desautorizado. Los usuarios ya no pueden usarlo.';

        return back()->with('success', $message);
    }

    /**
     * Test IMAP connection with form data
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'imap_username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $host = $validated['imap_host'];
        $port = $validated['imap_port'];
        $encryption = $validated['imap_encryption'] ?? 'ssl';
        $username = $validated['imap_username'];
        $password = $validated['password'];

        $diagnostics = [
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username,
        ];

        try {
            // Test port connectivity
            $portTest = ImapConnector::testConnection($host, $port, $encryption);
            $diagnostics['port_accessible'] = $portTest['success'];
            $diagnostics['port_message'] = $portTest['message'];

            if (!$portTest['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Puerto IMAP no accesible',
                    'diagnostics' => $diagnostics,
                ], 400);
            }

            // Build IMAP mailbox string
            $flags = match ($encryption) {
                'ssl' => '/imap/ssl/novalidate-cert',
                'tls' => '/imap/tls/novalidate-cert',
                default => '/imap/ssl/novalidate-cert',
            };

            $mailbox = "{{$host}:{$port}{$flags}}";

            // Clear previous IMAP errors
            @imap_errors();

            // Try to connect
            $connection = @imap_open(
                $mailbox,
                $username,
                $password,
                OP_READONLY,
                1
            );

            if (!$connection) {
                $error = @imap_last_error();
                return response()->json([
                    'success' => false,
                    'message' => 'Error de autenticación IMAP',
                    'diagnostics' => array_merge($diagnostics, [
                        'error' => $error ?: 'Error desconocido',
                    ]),
                ], 400);
            }

            // Get mailbox info
            $mailboxInfo = @imap_mailboxmsginfo($connection);
            $diagnostics['connection'] = [
                'success' => true,
                'messages_count' => $mailboxInfo->Nmsgs ?? 0,
            ];

            @imap_close($connection);

            return response()->json([
                'success' => true,
                'message' => 'Conexión IMAP exitosa',
                'diagnostics' => $diagnostics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar conexión IMAP',
                'diagnostics' => array_merge($diagnostics, [
                    'error' => $e->getMessage(),
                ]),
            ], 500);
        }
    }

    /**
     * Test IMAP connection for existing account by ID
     */
    public function testConnectionById($id)
    {
        $emailAccount = EmailAccount::findOrFail($id);
        $user = auth()->user();

        // Verificar permisos para usuarios
        if ($user && $user->role === 'user') {
            if ($emailAccount->user_id !== $user->id && !$emailAccount->is_authorized) {
                abort(403, 'No tienes autorización para usar este servidor.');
            }
        }

        $diagnostics = [
            'email' => $emailAccount->email,
            'host' => $emailAccount->imap_host,
            'port' => $emailAccount->imap_port,
            'encryption' => $emailAccount->imap_encryption ?? 'ssl',
            'username' => $emailAccount->username,
        ];

        try {
            // Get password - try to decrypt first, then use as plain text
            $password = $emailAccount->imap_password;
            try {
                $password = Crypt::decryptString($emailAccount->imap_password);
            } catch (\Exception $e) {
                // Password is not encrypted, use as plain text
            }

            // Test port connectivity
            $portTest = ImapConnector::testConnection(
                $emailAccount->imap_host,
                $emailAccount->imap_port,
                $emailAccount->imap_encryption ?? 'ssl'
            );
            $diagnostics['port_accessible'] = $portTest['success'];
            $diagnostics['port_message'] = $portTest['message'];

            if (!$portTest['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Puerto IMAP no accesible',
                    'diagnostics' => $diagnostics,
                ], 400);
            }

            // Build IMAP mailbox string
            $encryption = $emailAccount->imap_encryption ?? 'ssl';
            $flags = match ($encryption) {
                'ssl' => '/imap/ssl/novalidate-cert',
                'tls' => '/imap/tls/novalidate-cert',
                default => '/imap/ssl/novalidate-cert',
            };

            $mailbox = "{{$emailAccount->imap_host}:{$emailAccount->imap_port}{$flags}}";

            // Clear previous IMAP errors
            @imap_errors();

            // Try to connect
            $connection = @imap_open(
                $mailbox,
                $emailAccount->username,
                $password,
                OP_READONLY,
                1
            );

            if (!$connection) {
                $error = @imap_last_error();
                return response()->json([
                    'success' => false,
                    'message' => 'Error de autenticación IMAP',
                    'diagnostics' => array_merge($diagnostics, [
                        'error' => $error ?: 'Error desconocido',
                    ]),
                ], 400);
            }

            // Get mailbox info
            $mailboxInfo = @imap_mailboxmsginfo($connection);
            $diagnostics['connection'] = [
                'success' => true,
                'messages_count' => $mailboxInfo->Nmsgs ?? 0,
            ];

            @imap_close($connection);

            return response()->json([
                'success' => true,
                'message' => 'Conexión IMAP exitosa',
                'diagnostics' => $diagnostics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar conexión IMAP',
                'diagnostics' => array_merge($diagnostics, [
                    'error' => $e->getMessage(),
                ]),
            ], 500);
        }
    }
}
