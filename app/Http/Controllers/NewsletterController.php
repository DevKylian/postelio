<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Unsubscribe a user via a signed URL (no login required).
     * Link format: /newsletter/unsubscribe/{user}?signature=...
     */
    public function unsubscribe(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Ce lien de désabonnement est invalide ou expiré.');
        }

        $user->update(['newsletter_opt_in' => false]);

        return view('newsletter.unsubscribed', ['user' => $user]);
    }
}
