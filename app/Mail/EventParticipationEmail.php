<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// QR Code will be generated client-side

class EventParticipationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $participantId;
    public $qrData;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, User $user, string $participantId)
    {
        $this->event = $event;
        $this->user = $user;
        $this->participantId = $participantId;
        
        // Prepare QR code data for client-side generation
        $this->qrData = json_encode([
            'event_id' => $event->id,
            'participant_id' => $participantId,
            'user_id' => $user->id,
            'event_title' => $event->title,
            'event_date' => $event->date->format('Y-m-d'),
            'event_time' => $event->time->format('H:i')
        ]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Event Participation Confirmation - ' . $this->event->title)
                    ->view('emails.event-participation')
                    ->with([
                        'event' => $this->event,
                        'user' => $this->user,
                        'participantId' => $this->participantId,
                        'qrData' => $this->qrData
                    ]);
    }
}
