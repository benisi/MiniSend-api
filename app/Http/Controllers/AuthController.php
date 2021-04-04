<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TokenRequest;
use App\Models\Token;
use App\Models\User;
use App\Traits\SendApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    public function storeToken(TokenRequest $request)
    {
        try {
            $data = $request->only(['name']);
            $data['token'] = bin2hex(openssl_random_pseudo_bytes(16));
            $data['user_id'] = Auth::id();
            $token = Token::create($data);
            return $this->sendApiResponse(Response::HTTP_CREATED, __('token was created successfully'), ['token' => $token]);
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }

    public function getTokens()
    {
        try {
            $token = Auth::User()->tokens;
            return $this->sendApiResponse(Response::HTTP_OK, __('token was fetched successfully'), ['token' => $token]);
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }

    public function deleteTokens($id, Request $request)
    {
        $request->merge(['id' => $id]);
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->sendApiResponse(Response::HTTP_UNPROCESSABLE_ENTITY, __('Validation error'), null, $validator->errors());
            }
            $token = Auth::User()->tokens->where('id', $id)->first();
            if ($token) {
                $token->delete();
            } else {
                return $this->sendApiResponse(Response::HTTP_NOT_FOUND, __('token not found'));
            }
            return $this->sendApiResponse(Response::HTTP_OK, __('token was deleted successfully'));
        } catch (\Exception $e) {
            return $this->sendApiResponse(Response::HTTP_INTERNAL_SERVER_ERROR, __('something went wrong'));
        }
    }
}
