<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $address;
    public $phone;
    public $service;
    public $clientMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->name = $data['name'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->service = $data['service'] ?? '';
        $this->clientMessage = $data['message'] ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(settings()->get('notify_email'), $this->name ?: 'Contact Form'),
            replyTo: [
                new Address($this->address, $this->name ?: 'Contact Form')
            ],
            subject: '[' . config('app.name') . '] ' . __('emails.contact_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact'
        );
    }
}
