<?php

namespace App\Http\Middleware;

use App\Traits\SendApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    use SendApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $this->sendApiResponse(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        } catch (TokenInvalidException $e) {
            return $this->sendApiResponse(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        } catch (JWTException $e) {
            return $this->sendApiResponse(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
        return $next($request);
    }
}
