<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $isAdmin = false)
    {
        $this->order = $order;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope. (Subject Logic Yahan Aayega)
     */
    public function envelope(): Envelope
    {
        // Subject Logic
        $subject = $this->isAdmin
            ? 'New Order Received #' . $this->order->order_number
            : 'Order Confirmation #' . $this->order->order_number;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition. (View File Yahan Aayegi)
     */
    public function content(): Content
    {
        return new Content(
            // 🔥 Error Yahan Tha: 'view.name' ko hata kar sahi path dalein
            view: 'emails.order_confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
