<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\SendApiResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    use SendApiResponse;

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if ($token = Auth::attempt($credentials)) {
                $data = ['token' => $token, 'user' => Auth::User()];
                return $this->sendApiResponse(Response::HTTP_OK, __('login was successful'), $data);
            } else {
                return $this->sendApiResponse(Response::HTTP_UNAUTHORIZED, __('invalid credentials'));
            }
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->only(['name', 'email', 'password']);
            $data['password'] = Hash::make($data['password']);
            $data['email'] = trim($data['email']);
            $user = User::create($data);
            $token = JWTAuth::fromUser($user);
            return $this->sendApiResponse(Response::HTTP_CREATED, __('user was created successfully'), ['token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }
}
