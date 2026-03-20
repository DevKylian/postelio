<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    /**
     * Handle customer subscription updated.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) return;

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) return;

        $this->syncUserCredits($user);

        Log::info('Stripe webhook: subscription updated', [
            'user_id' => $user->id,
            'plan' => $user->currentPlanKey(),
        ]);
    }

    /**
     * Handle customer subscription deleted (cancelled, expired).
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) return;

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) return;

        // Downgrade to free tier
        $user->update([
            'total_credits' => config('subscription.free_credits', 3),
            'used_credits' => 0,
        ]);

        Log::info('Stripe webhook: subscription deleted, downgraded to free', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Handle successful invoice payment (subscription renewal).
     */
    public function handleInvoicePaymentSucceeded(array $payload): void
    {
        parent::handleInvoicePaymentSucceeded($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) return;

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) return;

        // Reset monthly credits on successful payment
        $this->syncUserCredits($user);
        $user->update([
            'used_credits' => 0,
            'last_quota_reset_at' => now(),
        ]);

        Log::info('Stripe webhook: invoice paid, credits reset', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Handle failed invoice payment.
     */
    public function handleInvoicePaymentFailed(array $payload): void
    {
        parent::handleInvoicePaymentFailed($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) return;

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) return;

        Log::warning('Stripe webhook: invoice payment failed', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Sync user credits based on their current plan.
     */
    protected function syncUserCredits(User $user): void
    {
        $planKey = $user->currentPlanKey();
        $credits = $planKey
            ? config("subscription.plans.{$planKey}.credits", 0)
            : config('subscription.free_credits', 3);

        $user->update(['total_credits' => $credits]);
    }
}
