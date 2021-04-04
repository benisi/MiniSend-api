<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Models\Batch;
use App\Models\Mail;
use App\Traits\SendApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EmailController extends Controller
{
    use SendApiResponse;

    public function send(Request $request)
    {
        try {
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
                return $this->sendApiResponse(Response::HTTP_UNPROCESSABLE_ENTITY, __('Validation error'), null, $validator->errors());
            }

            $data = Batch::processMailRequestData($request);
            $recipients = Mail::processRecipientsData($request);

            $batch = Batch::create($data);
            $batch->mails()->createMany($recipients);

            $batch->mails->each(function ($recipient) use ($batch) {
                ProcessMail::dispatch($batch, $recipient);
            });

            return $this->sendApiResponse(Response::HTTP_ACCEPTED, __('email was queued'));
        } catch (Throwable $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }

    public function index(Request $request)
    {
        try {
            $data = Mail::fetch();
            return $this->sendApiResponse(Response::HTTP_OK, __('email was fetched successfully'), $data);
        } catch (Throwable $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }

    public function show($id)
    {
        try {
            $mail = Mail::showSingleMail($id);
            return $this->sendApiResponse(Response::HTTP_OK, __('email was fetched successfully'), $mail);
        } catch (ModelNotFoundException $e) {
            return $this->sendApiResponse(Response::HTTP_NOT_FOUND, __('email not found'));
        } catch (Throwable $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }
}
