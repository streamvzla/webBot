<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowedEmail;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;

class AllowedEmailController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = AllowedEmail::with(['platform', 'user'])
            ->withCount([
                // Total de clientes asignados
                'clients as total_clients_count',
                // Clientes con asignación ACTIVA (no vencida)
                'clients as active_clients_count' => function ($q) {
                    $q->where(function ($c) {
                        $c->whereNull('allowed_email_client.expires_at')
                          ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                    });
                },
                // Clientes con asignación VENCIDA
                'clients as expired_clients_count' => function ($q) {
                    $q->whereNotNull('allowed_email_client.expires_at')
                      ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                },
            ])
            ->when($request->search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            })
            ->when($request->public !== null && $request->public !== '', function ($query) use ($request) {
                return $query->where('is_public', $request->boolean('public'));
            })
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->where('platform_id', $platformId);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->assignment === 'free', function ($query) {
                // Libre: sin clientes O todos los clientes tienen asignación vencida
                return $query->where(function ($q) {
                    $q->doesntHave('clients')
                      ->orWhereDoesntHave('clients', function ($inner) {
                          // No tiene ningún cliente con asignación activa (no vencida)
                          $inner->where(function ($c) {
                              $c->whereNull('allowed_email_client.expires_at')
                                ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                          });
                      });
                });
            })
            ->when($request->assignment === 'assigned', function ($query) {
                // Ocupada: tiene al menos un cliente con asignación activa
                return $query->whereHas('clients', function ($inner) {
                    $inner->where(function ($c) {
                        $c->whereNull('allowed_email_client.expires_at')
                          ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                    });
                });
            })
            ->when($request->assignment === 'expired', function ($query) {
                // Con asignaciones vencidas (pero aún tiene el pivot)
                return $query->whereHas('clients', function ($inner) {
                    $inner->whereNotNull('allowed_email_client.expires_at')
                          ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                });
            });

        // En multi-tenancy, los usuarios normales ven solo lo suyo, Super Admin ve todo
        if ($user && $user->id !== 1) {
            $query->where('user_id', $user->id);
        }

        $allowedEmails = $query->orderBy('email')->paginate(20);

        // Estadísticas globales para el panel
        $baseQuery = AllowedEmail::when($user->id !== 1, function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'free'     => (clone $baseQuery)->whereDoesntHave('clients', function ($q) {
                              $q->where(function ($c) {
                                  $c->whereNull('allowed_email_client.expires_at')
                                    ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                              });
                          })->count(),
            'occupied' => (clone $baseQuery)->whereHas('clients', function ($q) {
                              $q->where(function ($c) {
                                  $c->whereNull('allowed_email_client.expires_at')
                                    ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                              });
                          })->count(),
            'expired'  => (clone $baseQuery)->whereHas('clients', function ($q) {
                              $q->whereNotNull('allowed_email_client.expires_at')
                                ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                          })->count(),
        ];

        // Obtener usuarios para el filtro (solo para admins, ven a sus revendedores)
        $users = [];
        if ($user->role === 'admin' || $user->id === 1) {
            $users = User::whereIn('id', $user->getDescendantsIds())->orderBy('name')->get();
        }

        // Obtener plataformas para el filtro
        $platformsFilter = Platform::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.allowed-emails.index', compact('allowedEmails', 'users', 'platformsFilter', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Obtener plataformas disponibles para el usuario
        $platforms = Platform::where('is_active', true)
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $emailAccounts = \App\Models\EmailAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('email')
            ->get();

        return view('admin.allowed-emails.create', compact('platforms', 'emailAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'emails' => 'required|string',
            'description' => 'nullable|string|max:255',
            'platform_id' => 'nullable|exists:platforms,id',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $isActive = $request->boolean('is_active', true);
        $isPublic = $request->boolean('is_public', false);
        $platformId = $request->input('platform_id');
        $emailAccountId = $request->input('email_account_id');

        // Split emails by comma, space, or newline
        $emailList = preg_split('/[\s,]+/', $validated['emails'], -1, PREG_SPLIT_NO_EMPTY);
        $addedCount = 0;
        $errors = [];

        foreach ($emailList as $emailStr) {
            $emailStr = trim($emailStr);
            if (!filter_var($emailStr, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "$emailStr no es un formato válido.";
                continue;
            }

            // Check if exists
            $query = AllowedEmail::where('email', $emailStr)
                ->where(function ($q) use ($platformId) {
                    if ($platformId === null || $platformId === '') {
                        $q->whereNull('platform_id');
                    } else {
                        $q->where('platform_id', $platformId);
                    }
                });

            $query->where('user_id', $user->id);

            if ($query->exists()) {
                $errors[] = "$emailStr ya está registrado.";
                continue;
            }

            AllowedEmail::create([
                'email' => $emailStr,
                'description' => $validated['description'] ?? null,
                'platform_id' => $platformId,
                'email_account_id' => $emailAccountId,
                'is_active' => $isActive,
                'is_public' => $isPublic,
                'user_id' => $user->id,
            ]);
            
            $addedCount++;
        }

        if ($addedCount > 0) {
            $msg = "Se agregaron $addedCount correos exitosamente.";
            if (count($errors) > 0) {
                $msg .= " Algunos correos no se agregaron: " . implode(" ", $errors);
            }
            return redirect()->route('admin.allowed-emails.index')->with('success', $msg);
        } else {
            return redirect()->back()->withInput()->with('error', 'No se agregó ningún correo. ' . implode(" ", $errors));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($allowedEmail->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este correo.');
        }

        // Obtener plataformas disponibles para el usuario
        $platforms = Platform::where('is_active', true)
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $emailAccounts = \App\Models\EmailAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('email')
            ->get();

        return view('admin.allowed-emails.edit', compact('allowedEmail', 'platforms', 'emailAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($allowedEmail->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este correo.');
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user, $allowedEmail) {
                    $platformId = $request->input('platform_id');

                    $query = AllowedEmail::where('email', $value)
                        ->where('id', '!=', $allowedEmail->id)
                        ->where(function ($q) use ($platformId) {
                            // Verificar tanto si platform_id es NULL como si coincide
                            if ($platformId === null || $platformId === '') {
                                $q->whereNull('platform_id');
                            } else {
                                $q->where('platform_id', $platformId);
                            }
                        });

                    $query->where('user_id', $user->id);

                    if ($query->exists()) {
                        $fail('Ya existe un correo autorizado con este email para la plataforma seleccionada.');
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
            'platform_id' => 'nullable|exists:platforms,id',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);

        $allowedEmail->update($validated);

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', 'Correo autorizado actualizado exitosamente.');
    }

    public function destroy(AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($allowedEmail->user_id !== $user->id) {
            abort(403, 'No tienes autorización para eliminar este correo.');
        }

        $allowedEmail->delete();

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', 'Correo autorizado eliminado exitosamente.');
    }

    /**
     * Show the mass upload form.
     */
    public function massUpload()
    {
        $user = auth()->user();

        $platforms = Platform::where('is_active', true)
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $emailAccounts = \App\Models\EmailAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('email')
            ->get();

        return view('admin.allowed-emails.mass-upload', compact('platforms', 'emailAccounts'));
    }

    /**
     * Process mass upload.
     */
    public function massStore(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'emails_text' => 'nullable|string',
            'file' => 'nullable|file|mimes:txt,csv',
            'platform_id' => 'nullable|exists:platforms,id',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $platformId = $request->input('platform_id');
        $isActive = $request->boolean('is_active', true);
        $isPublic = $request->boolean('is_public', false);
        $userId = $user->id;

        // Extraer texto del campo de texto
        $text = $request->input('emails_text') ?? '';
        
        // Extraer texto del archivo si fue subido
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($extension, ['txt', 'csv'])) {
                $text .= ' ' . file_get_contents($file->getRealPath());
            }
        }
        
        // Extract emails using regex
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);
        
        $emails = array_unique($matches[0]);
        
        if (empty($emails)) {
            return back()->withErrors(['emails_text' => 'No se detectó ningún correo válido en el texto ni en el archivo.'])->withInput();
        }

        $addedCount = 0;
        $duplicateCount = 0;

        foreach ($emails as $emailStr) {
            $emailStr = strtolower(trim($emailStr));
            
            // Check for duplicates
            $query = AllowedEmail::where('email', $emailStr)
                ->where(function ($q) use ($platformId) {
                    if ($platformId === null || $platformId === '') {
                        $q->whereNull('platform_id');
                    } else {
                        $q->where('platform_id', $platformId);
                    }
                });

            $query->where('user_id', $user->id);

            if ($query->exists()) {
                $duplicateCount++;
                continue;
            }

            // Create new
            AllowedEmail::create([
                'email' => $emailStr,
                'platform_id' => $platformId ?: null,
                'email_account_id' => $request->input('email_account_id'),
                'user_id' => $userId,
                'is_active' => $isActive,
                'is_public' => $isPublic,
                'description' => 'Carga masiva',
            ]);
            
            $addedCount++;
        }

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', "Carga masiva completada: {$addedCount} correos agregados, {$duplicateCount} omitidos (duplicados).");
    }
}
