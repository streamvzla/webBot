<?php

namespace App\Http\Controllers;

use App\Models\AllowedEmail;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\PlatformSubject;
use App\Models\Query;
use App\Models\Setting;
use App\Services\ImapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class QueryController extends Controller
{
    /**
     * Get available platforms for querying
     */
    public function platforms(): JsonResponse
    {
        $platforms = Platform::where('is_active', true)
            ->with('subjects')
            ->get()
            ->map(function ($platform) {
                return [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'slug' => $platform->slug,
                    'color' => $platform->color,
                    'logo' => $platform->logo,
                    'subjects' => $platform->subjects->where('is_active', true)->pluck('subject'),
                ];
            });

        return Response::json([
            'success' => true,
            'data' => $platforms,
        ]);
    }

    /**
     * Process a code query request
     */
    public function query(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $platformId = $request->platform_id;
        $userId = $request->user()?->id;

        // Check if email filter is enabled
        $emailFilterEnabled = Setting::get(Setting::KEY_EMAIL_FILTER_ENABLED, false);

        if ($emailFilterEnabled) {
            $allowedEmail = AllowedEmail::where('email', $email)
                ->where('is_active', true)
                ->first();

            if (!$allowedEmail) {
                return Response::json([
                    'success' => false,
                    'message' => 'This email is not authorized for queries',
                ], 403);
            }
        }

        // Check cooldown
        $cooldownMinutes = (int) Setting::get(Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);

        if ($userId) {
            $lastQuery = Query::where('user_id', $userId)
                ->where('email', $email)
                ->where('created_at', '>=', now()->subMinutes($cooldownMinutes))
                ->first();

            if ($lastQuery) {
                $remainingMinutes = now()->diffInMinutes($lastQuery->created_at->addMinutes($cooldownMinutes));

                return Response::json([
                    'success' => false,
                    'message' => "Please wait {$remainingMinutes} minutes before querying again",
                    'retry_after' => $remainingMinutes,
                ], 429);
            }
        }

        // Get platform and subjects
        $platform = Platform::findOrFail($platformId);
        $subjects = PlatformSubject::where('platform_id', $platformId)
            ->where('is_active', true)
            ->get();

        if ($subjects->isEmpty()) {
            return Response::json([
                'success' => false,
                'message' => 'No email subjects configured for this platform',
            ], 400);
        }

        // Get user's email account
        $emailAccount = EmailAccount::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$emailAccount) {
            return Response::json([
                'success' => false,
                'message' => 'Email account not found or not active',
            ], 404);
        }

        // Create query record
        $query = Query::create([
            'user_id' => $userId,
            'platform_id' => $platformId,
            'email' => $email,
            'status' => Query::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            // Create IMAP service and connect
            $imapService = new ImapService(
                $emailAccount->imap_host,
                $emailAccount->imap_port,
                $emailAccount->imap_encryption,
                $emailAccount->username,
                $emailAccount->password
            );

            if (!$imapService->connect()) {
                $query->update([
                    'status' => Query::STATUS_ERROR,
                    'error_message' => $imapService->getLastError(),
                    'processed_at' => now(),
                ]);

                return Response::json([
                    'success' => false,
                    'message' => 'Failed to connect to email server',
                    'error' => $imapService->getLastError(),
                ], 500);
            }

            // Search for codes in emails
            $code = null;
            $errorMessage = null;

            foreach ($subjects as $subject) {
                $foundCode = $imapService->searchCodes($subject);
                if ($foundCode) {
                    $code = $foundCode;
                    break;
                }
            }

            $imapService->disconnect();

            // Update query record
            if ($code) {
                $query->update([
                    'status' => Query::STATUS_FOUND,
                    'code_found' => $code,
                    'processed_at' => now(),
                ]);

                return Response::json([
                    'success' => true,
                    'message' => 'Code found successfully',
                    'data' => [
                        'code' => $code,
                        'platform' => $platform->name,
                        'email' => $email,
                    ],
                ]);
            } else {
                $query->update([
                    'status' => Query::STATUS_NOT_FOUND,
                    'error_message' => 'No verification code found in recent emails',
                    'processed_at' => now(),
                ]);

                return Response::json([
                    'success' => false,
                    'message' => 'No verification code found',
                ], 404);
            }

        } catch (\Exception $e) {
            $query->update([
                'status' => Query::STATUS_ERROR,
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return Response::json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get query history for the authenticated user
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        $queries = Query::where('user_id', $user->id)
            ->with('platform')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Response::json([
            'success' => true,
            'data' => $queries,
        ]);
    }
}
