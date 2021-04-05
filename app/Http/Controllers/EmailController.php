<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Models\Batch;
use App\Models\Mail;
use App\Models\Token;
use App\Traits\SendApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
            $token = Token::getFromRequest();

            if (!$token) {
                return $this->sendApiResponse(Response::HTTP_UNAUTHORIZED, __('unauthorized'));
            }

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
                'variables.*.substitutions.*.value' => 'required|string',
                'attachments' => 'sometimes|required|array|min:1',
                'attachments.*.filename' => 'required|string',
                'attachments.*.content' => 'required|string'
            ]);

            $validator->after(function ($validator) use ($request) {
                if (is_array($request->attachments)) {
                    $message = 'the following attachment(s) are invalid ';
                    $thereWasAnError = false;
                    foreach ($request->attachments as $attachment) {
                        if (array_key_exists('filename', $attachment) && array_key_exists('content', $attachment)) {
                            $isValid = base64_encode(base64_decode($attachment['content'], true)) === $attachment['content'];
                            if (!$isValid) {
                                $thereWasAnError = true;
                                $message .= "{$attachment['filename']},";
                            }
                        }
                    }
                    if ($thereWasAnError) {
                        $validator->errors()->add('attachment', substr($message, 0, strlen($message) - 1));
                    }
                }
            });

            if ($validator->fails()) {
                return $this->sendApiResponse(Response::HTTP_UNPROCESSABLE_ENTITY, __('Validation error'), null, $validator->errors());
            }

            Auth::login($token->user);

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

    public function fetchBatch(Request $request)
    {
        try {
            $data = Batch::fetch();
            return $this->sendApiResponse(Response::HTTP_OK, __('batch was fetched successfully'), $data);
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
