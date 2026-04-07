<?php

namespace App\Livewire\Admin\Newsletter;

use App\Mail\NewsletterMail;
use App\Models\NewsletterLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NewsletterManager extends Component
{
    use WithPagination;

    public string $subject  = '';
    public string $content  = '';
    public string $audience = 'all';

    public bool   $showConfirmModal = false;
    public bool   $showPreviewModal = false;
    public bool   $showTestModal    = false;
    public bool   $isSending        = false;
    public string $testEmail        = '';
    public bool   $testSent         = false;

    /** Computed counts cached per request */
    protected array $audienceCounts = [];

    protected function rules(): array
    {
        return [
            'subject'  => 'required|string|max:255',
            'content'  => 'required|string|min:10',
            'audience' => 'required|in:all,free,basic,pro',
        ];
    }

    // ─────────────────────────────────────────────
    //  Audience helpers
    // ─────────────────────────────────────────────

    /**
     * Returns opted-in users filtered by audience segment.
     */
    public function getRecipientsQuery(string $audience = null)
    {
        $audience ??= $this->audience;

        $query = User::where('newsletter_opt_in', true)
                     ->whereNotNull('email_verified_at');

        return match ($audience) {
            'free'  => $query->where('is_admin', false)
                             ->whereNull('manual_plan')
                             ->whereDoesntHave('subscriptions', fn ($q) => $q->whereIn('stripe_status', ['active', 'trialing'])),
            'basic' => $query->where(fn ($q) =>
                            $q->where('manual_plan', 'basic')
                              ->orWhereHas('subscriptions', fn ($s) => $s->whereIn('stripe_status', ['active', 'trialing'])
                                                                          ->where('name', 'default')
                                                                          ->whereHas('items', fn ($i) => $i->whereIn('stripe_price', [
                                                                              config('subscription.plans.basic.stripe_price_id'),
                                                                              config('subscription.plans.basic.yearly_stripe_price_id'),
                                                                          ])))
                       ),
            'pro'   => $query->where(fn ($q) =>
                            $q->where('manual_plan', 'pro')
                              ->orWhereHas('subscriptions', fn ($s) => $s->whereIn('stripe_status', ['active', 'trialing'])
                                                                          ->where('name', 'default')
                                                                          ->whereHas('items', fn ($i) => $i->whereIn('stripe_price', [
                                                                              config('subscription.plans.pro.stripe_price_id'),
                                                                              config('subscription.plans.pro.yearly_stripe_price_id'),
                                                                          ])))
                       ),
            default => $query, // 'all'
        };
    }

    public function getCountFor(string $audience): int
    {
        if (!isset($this->audienceCounts[$audience])) {
            $this->audienceCounts[$audience] = $this->getRecipientsQuery($audience)->count();
        }
        return $this->audienceCounts[$audience];
    }

    public function getSelectedCountProperty(): int
    {
        return $this->getCountFor($this->audience);
    }

    // ─────────────────────────────────────────────
    //  Actions
    // ─────────────────────────────────────────────

    public function openPreview(): void
    {
        $this->validate();
        $this->showPreviewModal = true;
    }

    public function openTestModal(): void
    {
        $this->validate();
        $this->testEmail = Auth::user()->email;
        $this->testSent  = false;
        $this->showTestModal = true;
    }

    public function sendTest(): void
    {
        $this->validateOnly('testEmail', [
            'testEmail' => 'required|email',
        ]);

        Mail::to($this->testEmail)->send(
            new NewsletterMail(
                newsletterSubject: '[TEST] ' . $this->subject,
                newsletterContent: $this->content,
                recipientName:     Auth::user()->name,
            )
        );

        $this->testSent = true;
    }

    public function openConfirmModal(): void
    {
        $this->validate();
        $this->showConfirmModal = true;
    }

    public function send(): void
    {
        $this->validate();

        $this->isSending       = true;
        $this->showConfirmModal = false;

        $recipients = $this->getRecipientsQuery()->get(['name', 'email']);
        $count      = $recipients->count();

        foreach ($recipients as $user) {
            Mail::to($user->email)->queue(
                new NewsletterMail(
                    newsletterSubject: $this->subject,
                    newsletterContent: $this->content,
                    recipientName:     $user->name,
                )
            );
        }

        NewsletterLog::create([
            'subject'          => $this->subject,
            'content'          => $this->content,
            'audience'         => $this->audience,
            'recipients_count' => $count,
            'status'           => 'sent',
            'sent_by'          => Auth::id(),
        ]);

        $this->isSending = false;
        $this->resetForm();
        $this->dispatch('notify', "Newsletter envoyée à {$count} destinataire(s) !");
    }

    public function resetForm(): void
    {
        $this->subject  = '';
        $this->content  = '';
        $this->audience = 'all';
    }

    // ─────────────────────────────────────────────
    //  Render
    // ─────────────────────────────────────────────

    public function render()
    {
        try {
            $logs = NewsletterLog::with('sender')
                ->latest()
                ->paginate(10);

            $counts = [
                'all'   => $this->getCountFor('all'),
                'free'  => $this->getCountFor('free'),
                'basic' => $this->getCountFor('basic'),
                'pro'   => $this->getCountFor('pro'),
            ];
        } catch (\Exception $e) {
            // Table may not exist yet (migration pending)
            $logs   = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $counts = ['all' => 0, 'free' => 0, 'basic' => 0, 'pro' => 0];
        }

        return view('livewire.admin.newsletter.newsletter-manager', compact('logs', 'counts'));
    }
}
