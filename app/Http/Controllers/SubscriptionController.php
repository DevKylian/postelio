<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show the pricing / plans page.
     */
    public function plans()
    {
        $plans = config('subscription.plans');
        $user = Auth::user();
        $currentPlan = $user->currentPlanKey();
        $subscription = $user->subscription('default');

        return view('subscription.plans', compact('plans', 'currentPlan', 'subscription'));
    }

    /**
     * Create a Stripe Checkout session and redirect.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:free,pro,premium',
            'period' => 'nullable|string|in:monthly,yearly',
        ]);

        $plan = config("subscription.plans.{$request->plan}");
        $period = $request->input('period', 'monthly');
        $priceId = $period === 'yearly' ? ($plan['yearly_stripe_price_id'] ?? $plan['stripe_price_id']) : $plan['stripe_price_id'];

        if (!$plan || !$priceId) {
            return back()->with('error', 'Plan invalide ou non configuré.');
        }

        $user = Auth::user();

        // If already subscribed, redirect to billing portal or plans
        if ($user->subscribed('default')) {
            return $user->redirectToBillingPortal(route('subscription.plans'));
        }

        return $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.plans'),
            ]);
    }

    /**
     * Handle successful checkout redirect.
     */
    public function success(Request $request)
    {
        $user = Auth::user();

        // Update credits based on the new plan
        $planKey = $user->currentPlanKey();
        if ($planKey) {
            $credits = config("subscription.plans.{$planKey}.credits", 0);
            $user->update([
                'total_credits' => $credits,
                'used_credits' => 0,
                'last_quota_reset_at' => now(),
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Abonnement activé avec succès !');
    }

    public function billing()
    {
        return redirect()->route('subscription.plans');
    }

    public function portal(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasStripeId()) {
            return redirect()->route('subscription.plans')->with('error', 'Aucun abonnement actif.');
        }

        return $user->redirectToBillingPortal(route('subscription.plans'));
    }

    /**
     * Resume a cancelled subscription (during grace period).
     */
    public function resume()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
            return back()->with('success', 'Abonnement réactivé !');
        }

        return back()->with('error', 'Impossible de réactiver cet abonnement.');
    }

    /**
     * Cancel the subscription (with grace period).
     */
    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->valid() && !$subscription->onGracePeriod()) {
            $subscription->cancel();
            return back()->with('success', 'Abonnement annulé. Vous conservez l\'accès jusqu\'à la fin de la période en cours.');
        }

        return back()->with('error', 'Impossible d\'annuler cet abonnement.');
    }

    /**
     * Change to a different plan (upgrade/downgrade).
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:free,pro,premium',
            'period' => 'nullable|string|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $plan = config("subscription.plans.{$request->plan}");
        $period = $request->input('period', 'monthly');
        $priceId = $period === 'yearly' ? ($plan['yearly_stripe_price_id'] ?? $plan['stripe_price_id']) : $plan['stripe_price_id'];

        if (!$plan || !$priceId) {
            return back()->with('error', 'Plan invalide.');
        }

        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->valid()) {
            return redirect()->route('subscription.checkout', ['plan' => $request->plan, 'period' => $period]);
        }

        $subscription->swap($priceId);

        // Update credits
        $user->update([
            'total_credits' => $plan['credits'],
            'used_credits' => 0,
            'last_quota_reset_at' => now(),
        ]);

        return back()->with('success', "Abonnement changé vers {$plan['name']} !");
    }
}
