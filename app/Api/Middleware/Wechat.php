<?php

namespace App\Api\Middleware;

use Closure;
use Dingo\Api\Auth\Auth;
use App\Api\Repositories\UserRepository;

class Wechat
{
    protected $auth;
    protected $user;

    public function __construct(Auth $auth, UserRepository $user)
    {
        $this->auth = $auth;
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($wechatId = $request->header(env('WECHAT_HEADER_PARAM', ''))) {
            $this->auth->setuser($this->user->findUserByWechatId($wechatId));
        }

        return $next($request);
    }
}
