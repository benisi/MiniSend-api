<?php

namespace App\Mail;

use App\Models\Mail;
use App\Models\MailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $text;
    public $html;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailRecipient $recipient, string $subject, $text, $html)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->text = $text;
        $this->html = $html;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->recipient->mail->sender_email, $this->recipient->mail->sender_name)
            ->replyTo($this->recipient->mail->sender_email, $this->recipient->mail->sender_name)
            ->subject($this->subject);
        if ($mail->html) {
            $mail->html($this->html);
        } else {
            $mail->text($this->text);
        }
        return $mail;
    }
}
