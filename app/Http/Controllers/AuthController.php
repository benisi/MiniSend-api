<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if ($token = Auth::attempt($credentials)) {
                return response(['token' => $token], Response::HTTP_OK);
            } else {
                return response(__('invalid credentials'), Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response(__('something went wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
