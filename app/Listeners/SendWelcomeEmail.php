<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(Registered $event): void
    {
        // Email is now handled by MustVerifyEmail trait in User model
        // which calls sendEmailVerificationNotification()
        // Mail::to($event->user)->send(new WelcomeMail($event->user));
    }
}
