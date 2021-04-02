<?php

namespace App\Mail;

use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->mail->batch->sender_email, $this->mail->batch->sender_name)
            ->replyTo($this->mail->batch->sender_email, $this->mail->batch->sender_name)
            ->subject($this->mail->subject);
        if ($this->mail->html) {
            $mail->html($this->mail->html);
        } else {
            $mail->text($this->mail->text);
        }
        return $mail;
    }
}
