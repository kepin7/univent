<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function build()
    {
        return $this->subject('New Feedback Received')
            ->view('emails.feedback')
            ->with([
                'name' => $this->feedback->name,
                'email' => $this->feedback->email,
                'messageContent' => $this->feedback->message,
            ]);
    }
}