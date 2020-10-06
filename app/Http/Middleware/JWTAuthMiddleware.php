<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTAuthMiddleware
{
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
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return Response::json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
        } catch (TokenExpiredException $e) {
            return Response::json([
                'message' => 'Unauthenticated'
            ], 401);
        } catch (TokenInvalidException $e) {
            return Response::json([
                'message' => 'Unauthenticated'
            ], 401);
        } catch (JWTException $e) {
            return Response::json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return $next($request);
    }
}
