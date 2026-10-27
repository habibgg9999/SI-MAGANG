<?php

namespace App\Mail;

use App\Models\InternshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public InternshipApplication $application)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = $this->application->company ? $this->application->company->name : 'Instansi';

        return new Envelope(
            subject: "[PENTING] Pengingat Status Pengajuan Magang: {$companyName} - SIMAGANG",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $group = $this->application->group;
        $leader = $group ? $group->leader : null;
        $company = $this->application->company;
        $daysWaiting = $this->application->applied_at 
            ? (int) $this->application->applied_at->diffInDays(now()) 
            : 14;

        return new Content(
            view: 'emails.application_reminder',
            with: [
                'application' => $this->application,
                'group' => $group,
                'leader' => $leader,
                'company' => $company,
                'daysWaiting' => $daysWaiting,
            ],
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
