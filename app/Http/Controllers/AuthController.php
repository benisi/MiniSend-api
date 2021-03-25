<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

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

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->only(['name', 'email', 'password']);
            $data['password'] = Hash::make($data['password']);
            $data['email'] = trim($data['email']);
            $user = User::create($data);
            $token = JWTAuth::fromUser($user);
            return response(['message' => __('user was created successfully'), 'token' => $token, 'data' => $user], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response(__('something went wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
