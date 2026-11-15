<?php

namespace App\Mail;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AccountRequest $accountRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.config('app.name').'] '.__('emails.account_request_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-request');
    }
}
