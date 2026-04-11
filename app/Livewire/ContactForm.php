<?php

namespace App\Livewire;

use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|min:3|max:150')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $message = '';

    public bool $sent = false;
    public ?string $error = null;

    public function send(): void
    {
        $this->validate();

        $key = 'contact:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->error = "Trop de messages envoyés. Réessayez dans {$seconds} secondes.";
            return;
        }

        RateLimiter::hit($key, 3600);

        try {
            Mail::to(config('mail.from.address', 'hello@postelio.fr'))
                ->send(new ContactMail(
                    senderName: $this->name,
                    senderEmail: $this->email,
                    subject: $this->subject,
                    messageBody: $this->message,
                ));

            $this->reset(['name', 'email', 'subject', 'message']);
            $this->error = null;
            $this->sent = true;
        } catch (\Exception $e) {
            $this->error = "Une erreur est survenue. Veuillez réessayer ou nous écrire directement à hello@postelio.fr";
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
