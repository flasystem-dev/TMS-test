<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendTransaction extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this -> data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = '['.$this -> data['brand'] . '] ' . $this -> data['com_name'] . ' 거래내역서 (' . $this -> data['receipt_date'] . ')';

        return new Envelope(
            subject: $title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-email',
            with: [
                'brand'         => $this -> data['brand'],
                'receipt_date'  => $this -> data['receipt_date'],
                'brand_tel'     => $this -> data['brand_tel'],
                'link'          => $this -> data['link']
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
