<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authenticate
{
    public function __construct()
    {
        if(empty(session_id())){
            session_start();
        }
    }

    public function forbidden()
    {
        if(!empty(session_id())){
            session_destroy();
        }

        abort(403);
    }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @param  string|null $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if(!array_key_exists('token', $_SESSION)){
            $this->forbidden();
        }

        $authorization = $request->header('Authorization');
	    if($authorization !== $_SESSION['token']){
            $this->forbidden();
        }

        if(!array_key_exists('expires_at', $_SESSION)){
            $this->forbidden();
        }

        if(intval($_SESSION['expires_at']) < time()){
            $this->forbidden();
        }

		return $next($request);
	}
}
