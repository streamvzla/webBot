<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WarrantyRequest;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WarrantyController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        $warranties = $client->warrantyRequests()->with('platform')->latest()->get();

        // Emails that already have an active (pending or approved) warranty — blocked from new requests
        $blockedEmails = $warranties->whereIn('status', ['pending', 'approved'])
            ->pluck('old_email')
            ->unique()
            ->toArray();

        // Only show emails that are NOT blocked
        $allowedEmails = $client->allowedEmails->filter(function ($email) use ($blockedEmails) {
            return !in_array($email->email, $blockedEmails);
        })->values();

        $platforms = Platform::all();

        return view('client.warranties.index', compact('warranties', 'allowedEmails', 'platforms', 'blockedEmails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'old_email'   => 'required|email',
            'platform_id' => 'nullable|exists:platforms,id',
            'type'        => 'required|in:replacement,minor_issue',
            'reason'      => 'required|string|max:500',
        ]);

        $client = Auth::guard('client')->user();

        // Verify the email belongs to the client
        $allowedEmail = $client->allowedEmails()->where('email', $request->old_email)->first();
        if (!$allowedEmail) {
            return back()->with('error', 'El correo especificado no pertenece a su cuenta.');
        }

        // Block if there is already a pending OR approved request for this email
        $existingActive = WarrantyRequest::where('client_id', $client->id)
            ->where('old_email', $request->old_email)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingActive) {
            return back()->with('error', 'Ya tienes una solicitud activa para este correo. Espera a que el administrador la gestione.');
        }

        // Create the warranty request
        WarrantyRequest::create([
            'client_id'   => $client->id,
            'old_email'   => $request->old_email,
            'platform_id' => $request->platform_id,
            'type'        => $request->type,
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        // Pause the account
        $allowedEmail->update(['paused_at' => Carbon::now()]);

        return back()->with('success', 'Solicitud de garantia enviada correctamente. El tiempo de la cuenta ha sido pausado.');
    }

    public function destroy($id)
    {
        $client = Auth::guard('client')->user();

        // Only the client who owns it and only if still pending
        $warranty = WarrantyRequest::where('id', $id)
            ->where('client_id', $client->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Unpause the account
        $allowedEmail = $client->allowedEmails()->where('email', $warranty->old_email)->first();
        if ($allowedEmail) {
            $allowedEmail->update(['paused_at' => null]);
        }

        $warranty->update([
            'status' => 'rejected',
            'cancelled_at' => Carbon::now()
        ]);

        return back()->with('success', 'Solicitud cancelada correctamente. Tu cuenta ha sido reactivada.');
    }
}
