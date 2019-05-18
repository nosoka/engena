<?php

namespace App\Api\Middleware;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class Authenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->checkForToken($request);
        $expired = false;

        try {
            $this->auth->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $expired = true;
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage());
        }

        if ($expired) {
            try {
                $refreshToken = $this->auth->parseToken()->refresh();
                return response($refreshToken, 401)
                    ->header('Authorization', 'Bearer ' . $refreshToken);
            } catch (JWTException $e) {
                throw new UnauthorizedHttpException('jwt-auth', $e->getMessage());
            }
        }

        return $next($request);
    }
}
