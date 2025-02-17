<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendSpecification extends Mailable
{
    use Queueable, SerializesModels;

    protected string $year;
    protected string $month;
    protected $vendor;

    /**
     * Create a new message instance.
     */
    public function __construct($year, $month, $vendor)
    {
        $this->year = $year;
        $this->month = $month;
        $this->vendor = $vendor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = "[{$this->year}년 {$this->month}월] 지급액 명세서";

        return new Envelope(
            subject: $title,
            from: new Address("pst@flasystem.com", "(주)플라시스템 사업지원팀")
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.specification-email',
            with: [
                'vendor' => $this->vendor,
                'year' => $this->year,
                'month' => $this->month,
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
