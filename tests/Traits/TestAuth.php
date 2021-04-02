<?php

namespace Tests\Traits;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TestAuth
{
    private function getJwt(User $user)
    {
        return JWTAuth::fromUser($user);
    }
}
