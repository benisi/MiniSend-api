<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Models\Mail;
use App\Traits\SendApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    use SendApiResponse;

    public function send(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'from' => 'required|array',
            'from.email' => 'required|email',
            'from.name' => 'required|string',
            'to' => 'required|array|min:1',
            'to.*.name' => 'required|string',
            'to.*.email' => 'required|email',
            'subject' => 'required|string',
            'text' => [Rule::requiredIf(empty($request->html)), 'string'],
            'html' => [Rule::requiredIf(empty($request->text)), 'string'],
            'variables' => 'sometimes|required|array|min:1',
            'variables.*.email' => 'required|email',
            'variables.*.substitutions' => 'required|array|min:1',
            'variables.*.substitutions.*.var' => 'required|string',
            'variables.*.substitutions.*.value' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response(['message' => __('Validation error'), 'data' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Mail::processMailRequestData($request);
        $recipients = Mail::processRecipientsData($request);

        $mail = Mail::create($data);
        $mail->recipients()->createMany($recipients);

        $mail->recipients->each(function ($recipient) use ($mail) {
            ProcessMail::dispatch($mail, $recipient);
        });

        return response(['message' => __('email was queued')], Response::HTTP_ACCEPTED);
    }

    public function index(Request $request)
    {

        $data = Mail::fetch();
        return $this->sendApiResponse(Response::HTTP_OK, __('email was fetched successfully'), $data);
    }
}
