<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailAccount;
use App\Models\User;
use App\Services\ImapConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EmailAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emailAccounts = EmailAccount::with('users')->orderBy('email')->get();
        return view('admin.email-accounts.index', compact('emailAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('is_active', true)->orderBy('username')->get();
        return view('admin.email-accounts.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Cifrar contraseña IMAP antes de guardar
        $encryptedPassword = Crypt::encryptString($validated['password']);

        $emailAccount = EmailAccount::create([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'imap_password' => $encryptedPassword,
            'imap_host' => $validated['imap_host'],
            'imap_port' => $validated['imap_port'],
            'imap_encryption' => $validated['imap_encryption'] ?? 'ssl',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Attach selected users to the email account
        if (!empty($validated['user_ids'])) {
            $emailAccount->users()->attach($validated['user_ids']);
        }

        return redirect()->route('admin.email-accounts.index')
            ->with('success', 'Cuenta de correo creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailAccount $emailAccount)
    {
        $users = User::where('is_active', true)->orderBy('username')->get();
        $selectedUserIds = $emailAccount->users->pluck('id')->toArray();
        return view('admin.email-accounts.edit', compact('emailAccount', 'users', 'selectedUserIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailAccount $emailAccount)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Cifrar nueva contraseña si se proporciona
        $imapPassword = !empty($validated['password'])
            ? Crypt::encryptString($validated['password'])
            : $emailAccount->imap_password;

        $emailAccount->update([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'imap_password' => $imapPassword,
            'imap_host' => $validated['imap_host'],
            'imap_port' => $validated['imap_port'],
            'imap_encryption' => $validated['imap_encryption'] ?? 'ssl',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync users
        if (!empty($validated['user_ids'])) {
            $emailAccount->users()->sync($validated['user_ids']);
        } else {
            $emailAccount->users()->detach();
        }

        return redirect()->route('admin.email-accounts.index')
            ->with('success', 'Cuenta de correo actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailAccount $emailAccount)
    {
        // Detach all users first
        $emailAccount->users()->detach();

        $emailAccount->delete();

        return redirect()->route('admin.email-accounts.index')
            ->with('success', 'Cuenta de correo eliminada exitosamente.');
    }

    /**
     * Test IMAP connection for an email account.
     */
    public function testConnection(Request $request, EmailAccount $emailAccount)
    {
        try {
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            $status = $connector->getConnectionStatus();
            $mailboxes = $connector->getMailboxes();

            $connector->disconnect();

            // Update last_checked_at
            $emailAccount->update(['last_checked_at' => now()]);

            return redirect()->back()->with([
                'success' => 'Conexión IMAP exitosa.',
                'connection_status' => [
                    'connected' => true,
                    'messages_count' => $status['mailbox_info']['messages'] ?? 0,
                    'mailboxes_count' => count($mailboxes),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('IMAP connection test failed', [
                'email' => $emailAccount->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with([
                'error' => 'Error de conexión IMAP: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test IMAP connection via AJAX.
     */
    public function testConnectionAjax(Request $request, EmailAccount $emailAccount)
    {
        try {
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            $status = $connector->getConnectionStatus();
            $mailboxes = $connector->getMailboxes();

            $connector->disconnect();

            // Update last_checked_at
            $emailAccount->update(['last_checked_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Conexión IMAP exitosa',
                'data' => [
                    'connected' => true,
                    'messages_count' => $status['mailbox_info']['messages'] ?? 0,
                    'unread_count' => $status['mailbox_info']['unread'] ?? 0,
                    'mailboxes_count' => count($mailboxes),
                    'last_checked' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión IMAP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
