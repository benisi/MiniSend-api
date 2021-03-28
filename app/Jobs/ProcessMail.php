<?php

namespace App\Jobs;

use App\Mail\Mailer;
use App\Models\Mail;
use App\Models\MailRecipient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as FacadesMail;

class ProcessMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail;
    public $recipient;
    public $tries = 3;
    public $backoff = [60, 120];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Mail $mail, MailRecipient $recipient)
    {
        $this->mail = $mail;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        FacadesMail::to($this->recipient->email)->send(new Mailer($this->mail, $this->recipient));
        DB::transaction(function () {
            $this->recipient->status = MailRecipient::STATUS_SENT;
            $this->recipient->save();
            $this->mail->decrement('pending_mail');
            $this->mail->refresh();
            if ($this->mail->pending_mail === 0) {
                $this->mail->status = Mail::STATUS_COMPLETED;
                $this->mail->save();
            }
        });
    }

    public function failed(Exception $e)
    {
        Log::error($e->getMessage());
        $this->recipient->status = MailRecipient::STATUS_FAILED;
        $this->recipient->save();
    }
}
