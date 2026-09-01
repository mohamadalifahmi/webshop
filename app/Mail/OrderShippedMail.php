<?php

namespace App\Mail;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public OrderItem $item) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your order shipped! Tracking: {$this->item->tracking_number} - ASTRAGO MARKET",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.order-shipped');
    }
}
