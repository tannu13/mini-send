<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\EmailActivity;

class UserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailActivity;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailActivity $ea)
    {
        $this->emailActivity = $ea;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this
            ->from($this->emailActivity->sender)
            ->subject($this->emailActivity->subject)
            ->view('emails.user')
            ->text('emails.user_plain');

        if (!is_null($this->emailActivity->attachments)) {
            $attachments = json_decode($this->emailActivity->attachments, true);
            foreach ($attachments as $attachment) {
                $email->attachFromStorage('/attachments/' . $attachment['saved_name'], $attachment['orig_name']);
            }
        }

        $this->emailActivity->status = 'sent';
        $this->emailActivity->save();

        return $email;
    }
}
