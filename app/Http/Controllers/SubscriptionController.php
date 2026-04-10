<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;

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
     * Show the billing management page.
     */
    public function billing()
    {
        $user = Auth::user();
        $plans = config('subscription.plans');
        $currentPlan = $user->currentPlanKey();
        $subscription = $user->subscription('default');

        return view('subscription.billing', compact('user', 'plans', 'currentPlan', 'subscription'));
    }

    /**
     * Create a Stripe Checkout session and redirect.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:free,basic,pro',
            'period' => 'nullable|string|in:monthly,yearly',
        ]);

        $plan = config("subscription.plans.{$request->plan}");
        $period = $request->input('period', 'monthly');
        $priceId = $period === 'yearly' ? ($plan['yearly_stripe_price_id'] ?? $plan['stripe_price_id']) : $plan['stripe_price_id'];

        if (!$plan || !$priceId) {
            return back()->with('error', 'Plan invalide ou non configuré.');
        }

        $user = Auth::user();

        // If already subscribed, redirect to billing page
        if ($user->subscribed('default')) {
            return redirect()->route('subscription.billing');
        }

        return $user->newSubscription('default', $priceId)
            ->allowPromotionCodes()
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

    public function portal(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasStripeId()) {
            return redirect()->route('subscription.plans')->with('error', 'Aucun abonnement actif.');
        }

        return $user->redirectToBillingPortal(route('subscription.billing'));
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
            return redirect()->route('subscription.billing')->with('success', 'Abonnement réactivé !');
        }

        return redirect()->route('subscription.billing')->with('error', 'Impossible de réactiver cet abonnement.');
    }

    /**
     * Intercept cancellation: redirect to the retention offer page (first attempt)
     * or proceed directly if the offer was already presented.
     */
    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->valid() || $subscription->onGracePeriod()) {
            return redirect()->route('subscription.billing')->with('error', 'Impossible d\'annuler cet abonnement.');
        }

        // Only show the retention offer on monthly subscriptions and if not already shown
        $isMonthly = $this->isMonthlySubscription($subscription);
        if ($isMonthly && !$user->hasSeenRetentionOffer()) {
            return redirect()->route('subscription.cancel-offer');
        }

        // Offer already shown or yearly plan: cancel directly
        $subscription->cancel();
        return redirect()->route('subscription.billing')
            ->with('success', 'Abonnement annulé. Vous conservez l\'accès jusqu\'à la fin de la période en cours.');
    }

    /**
     * Show the retention offer page (-50% on next billing).
     */
    public function cancelOffer()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->valid() || $subscription->onGracePeriod()) {
            return redirect()->route('subscription.billing');
        }

        $plans = config('subscription.plans');
        $currentPlan = $user->currentPlanKey();
        $currentPlanData = $currentPlan ? ($plans[$currentPlan] ?? null) : null;

        return view('subscription.cancel-offer', compact('subscription', 'plans', 'currentPlan', 'currentPlanData', 'user'));
    }

    /**
     * User accepts the -50% retention offer.
     * Apply a one-time 50% coupon on the next invoice and keep the subscription.
     */
    public function acceptRetentionOffer()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->valid() || $subscription->onGracePeriod()) {
            return redirect()->route('subscription.billing')->with('error', 'Aucun abonnement actif.');
        }

        // Create a one-time 50% coupon via Stripe and apply it to the subscription
        try {
            $stripe = Cashier::stripe();

            $coupon = $stripe->coupons->create([
                'percent_off' => 50,
                'duration'    => 'once',
                'name'        => 'Offre fidélité -50%',
            ]);

            $stripe->subscriptions->update($subscription->stripe_id, [
                'coupon' => $coupon->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('subscription.billing')
                ->with('error', 'Une erreur est survenue lors de l\'application de la réduction. Veuillez réessayer.');
        }

        // Mark offer as used so it won't be shown again
        $user->update(['retention_offer_used_at' => now()]);

        return redirect()->route('subscription.billing')
            ->with('success', 'Super ! 🎉 Votre réduction de -50% a bien été appliquée sur votre prochaine facture. Merci pour votre fidélité !');
    }

    /**
     * User declines the retention offer and confirms cancellation.
     */
    public function declineRetentionOffer()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->valid() || $subscription->onGracePeriod()) {
            return redirect()->route('subscription.billing')->with('error', 'Impossible d\'annuler cet abonnement.');
        }

        // Mark offer as seen so it won't be shown again
        $user->update(['retention_offer_used_at' => now()]);

        $subscription->cancel();

        return redirect()->route('subscription.billing')
            ->with('success', 'Abonnement annulé. Vous conservez l\'accès jusqu\'à la fin de la période en cours.');
    }

    /**
     * Change to a different plan (upgrade/downgrade).
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:free,basic,pro',
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

    /**
     * Determine if the current subscription is on a monthly price.
     */
    private function isMonthlySubscription($subscription): bool
    {
        $plans = config('subscription.plans');

        foreach ($plans as $plan) {
            if (!empty($plan['yearly_stripe_price_id']) && $subscription->hasPrice($plan['yearly_stripe_price_id'])) {
                return false;
            }
        }

        return true;
    }
}
