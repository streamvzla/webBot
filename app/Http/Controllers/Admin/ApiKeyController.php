<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $token = $user->createToken($request->token_name);

        return back()->with('success', 'API Key generada exitosamente. Guárdala ahora, no se volverá a mostrar: ' . $token->plainTextToken);
    }

    public function revoke(Request $request, $tokenId)
    {
        $user = $request->user();
        $user->tokens()->where('id', $tokenId)->delete();

        return back()->with('success', 'API Key revocada exitosamente.');
    }
}
