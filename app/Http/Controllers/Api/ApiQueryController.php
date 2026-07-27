<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Query;
use App\Models\AllowedEmail;
use App\Models\Platform;
use App\Models\EmailAccount;
use App\Models\Setting;
use App\Services\ImapConnector;
use App\Services\EmailCodeExtractor;
use Illuminate\Support\Facades\Log;

class ApiQueryController extends Controller
{
    /**
     * Obtiene el perfil de la franquicia y estadísticas de uso.
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total_clients' => $user->clients()->count(),
            'total_queries_today' => Query::where('user_id', $user->id)
                                          ->whereDate('created_at', today())
                                          ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'franchise_name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Obtiene las plataformas disponibles para esta franquicia.
     */
    public function getPlatforms(Request $request)
    {
        $user = $request->user();

        // Plataformas globales o propias de la franquicia
        $platforms = Platform::where(function ($query) use ($user) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
            })
            ->where('is_active', true)
            ->with(['subjects' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $platforms
        ]);
    }

    /**
     * Obtiene los buzones maestros configurados por la franquicia.
     */
    public function getEmails(Request $request)
    {
        $user = $request->user();
        
        $emails = EmailAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->get(['id', 'email', 'imap_host', 'last_checked_at']);

        return response()->json([
            'success' => true,
            'data' => $emails
        ]);
    }

    /**
     * Obtiene los últimos correos autorizados públicos (para reventa).
     */
    public function getRecentEmails(Request $request)
    {
        $limit = $request->query('limit', 50);
        $user = $request->user();
        
        $emails = AllowedEmail::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->take($limit)
            ->get(['email', 'platform_id', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $emails
        ]);
    }

    /**
     * Realiza una consulta directa por API usando IMAP real.
     */
    public function query(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        $user = $request->user();
        $targetEmail = strtolower($request->email);
        $platformId = $request->platform_id;

        // 1. Validar que la plataforma existe y le pertenece o es global
        $platform = Platform::where('id', $platformId)
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })->first();

        if (!$platform) {
            return response()->json(['success' => false, 'message' => 'Plataforma no encontrada o no autorizada.'], 403);
        }

        $subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();
        if (empty($subjects)) {
            return response()->json(['success' => false, 'message' => 'La plataforma no tiene reglas de Asuntos (Subjects) configuradas.'], 400);
        }

        // 2. Buscar si el correo targetEmail pertenece a algún EmailAccount (buzón maestro) de la franquicia
        // NOTA: Para APIs de reventa, usualmente buscan en todos los buzones activos de la franquicia
        $emailAccounts = EmailAccount::where('user_id', $user->id)->where('is_active', true)->get();
        if ($emailAccounts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No tienes buzones IMAP configurados en tu panel.'], 404);
        }

        // 3. Buscar el código en los buzones usando el motor On-Demand
        $codeFound  = null;
        $startTime  = microtime(true);

        $expectedRecipients = [strtolower($targetEmail)];
        $platformSubjects   = [$platform->name => $platform->subjects()->where('is_active', true)->pluck('subject')->toArray()];

        foreach ($emailAccounts as $account) {
            try {
                $connector = new ImapConnector($account);
                $connector->connect();

                // Traer los últimos correos recientes del buzón
                $messages = $connector->getRecentEmails(1);

                foreach ($messages as $message) {
                    $emailData = $connector->searchByTo($message, $expectedRecipients, $platformSubjects);

                    if ($emailData) {
                        $cleanText = strip_tags($emailData['body']);
                        $extracted = EmailCodeExtractor::extract($emailData['body'], $cleanText);
                        $val = is_array($extracted) ? ($extracted['value'] ?? null) : $extracted;

                        if ($val) {
                            $codeFound = [
                                'code'        => $val,
                                'platform'    => $platform->name,
                                'received_at' => now()->toDateTimeString(),
                            ];
                            break 2; // Salir de ambos foreach
                        }
                    }
                }

                $connector->disconnect();

            } catch (\Exception $e) {
                Log::error("API IMAP Error en buzón {$account->email}: " . $e->getMessage());
                continue;
            }
        }

        $queryTimeMs = round((microtime(true) - $startTime) * 1000);

        // 4. Registrar consulta
        $queryRecord = Query::create([
            'email'          => $targetEmail,
            'platform_id'    => $platform->id,
            'result'         => $codeFound ? 'success' : 'no_code',
            'code_hash'      => $codeFound ? Query::hashCode($codeFound['code']) : null,
            'code_status'    => $codeFound ? 'found' : 'not_found',
            'user_id'        => $user->id,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        if ($codeFound) {
            return response()->json([
                'success' => true,
                'message' => 'Código encontrado exitosamente.',
                'data'    => [
                    'email'    => $targetEmail,
                    'platform' => $platform->name,
                    'code'     => $codeFound['code'],
                    'query_id' => $queryRecord->id,
                    'time_ms'  => $queryTimeMs,
                ]
            ]);
        }

        return response()->json([
            'success'  => false,
            'message'  => 'No se encontró ningún código reciente para este correo y plataforma.',
            'query_id' => $queryRecord->id,
        ], 404);
    }
}


