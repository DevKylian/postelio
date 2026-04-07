<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $unsubscribeUrl;

    public function __construct(
        public readonly string $newsletterSubject,
        public readonly string $newsletterContent,
        public readonly string $recipientName,
        User $recipient,
    ) {
        // Signed URL valid 30 days, no auth required
        $this->unsubscribeUrl = URL::signedRoute(
            'newsletter.unsubscribe',
            ['user' => $recipient->id],
            now()->addDays(30),
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletterSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'subject'        => $this->newsletterSubject,
                'htmlContent'    => $this->newsletterContent,
                'recipientName'  => $this->recipientName,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }
}
