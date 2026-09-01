<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderSellerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, OrderItem>  $items
     */
    public function __construct(public Order $order, public Collection $items) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You have a new order to ship ({$this->order->order_number}) - ASTRAGO MARKET",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.new-order-seller');
    }
}
