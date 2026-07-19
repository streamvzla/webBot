<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Services\DomainValidator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizedDomainCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user('client');

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Debes iniciar sesión.',
            ], 401);
        }

        // Obtener el email del cliente
        $clientEmail = $client->email;

        // Validar dominio
        $validator = new DomainValidator();

        // Si el tiene un email account asociado, cliente usar ese email
        if ($client->emailAccount) {
            $clientEmail = $client->emailAccount->email;
        }

        if (!$validator->isDomainAllowed($clientEmail)) {
            return response()->json([
                'success' => false,
                'message' => 'Tu dominio de correo no está autorizado.',
                'domain_status' => $validator->getValidationStatus($clientEmail),
            ], 403);
        }

        return $next($request);
    }
}
