<?php

namespace App\Mail;

use App\Helpers\MessageParser;
use App\Models\Mail;
use App\Models\MailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mail $mail, MailRecipient $recipient)
    {
        $this->mail = $mail;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = "<div>{$this->getMessage()}</div>";
        return $this->from($this->mail->sender_email, $this->mail->sender_name)
            ->replyTo($this->mail->sender_email, $this->mail->sender_name)
            ->subject($this->getSubject())
            ->html($message);
    }

    private function getMessage()
    {
        $variables = $this->getVariables();
        if ($this->mail->text) {
            return MessageParser::substituteValues($this->mail->text, $variables);
        }

        if ($this->mail->html) {
            return MessageParser::substituteValues($this->mail->html, $variables);
        }
    }

    private function getSubject()
    {
        $variables = $this->getVariables();
        return MessageParser::substituteValues($this->mail->subject, $variables);
    }

    private function getVariables(): array
    {
        if (!$this->recipient->variables) {
            return [];
        }
        return $this->recipient->variables;
    }
}
