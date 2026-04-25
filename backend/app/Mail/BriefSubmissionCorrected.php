<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BriefSubmissionCorrected extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $briefTitle;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $briefTitle, $actionUrl)
    {
        $this->studentName = $studentName;
        $this->briefTitle = $briefTitle;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Fleet Command: Delivery Verified - Prepare for Logic Challenges',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.brief-submission-corrected',
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
