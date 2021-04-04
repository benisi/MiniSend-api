<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Traits\SendApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    use SendApiResponse;

    public function index()
    {
        try {
            $mail = Mail::getDashboardMailCount();
            $data = [
                'mail' => $mail
            ];
            return $this->sendApiResponse(Response::HTTP_OK, __('Dashboard fetch successfully'), $data);
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }
}
