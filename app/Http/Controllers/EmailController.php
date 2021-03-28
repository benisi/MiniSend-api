<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Models\Mail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    public function email(Request $request)
    {
        $data = Mail::processMailRequestData($request);
        $recipients = Mail::processRecipientsData($request);

        $mail = Mail::create($data);
        $mail->recipients()->createMany($recipients);

        $mail->recipients->each(function ($recipient) use ($mail) {
            ProcessMail::dispatch($mail, $recipient);
        });

        return response(__('email was queued'), Response::HTTP_ACCEPTED);
    }
}
