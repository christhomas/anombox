<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
	public function handle($request, Closure $next)
	{
		$response = $next($request);

		// only allow particular domains and protocols, if information is available
		$origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : '';
		$allowedOrigin = strpos($origin, config("cors.allowed")) !== false ? $origin : config("cors.default");

		// create CORS headers
		$response->header("Access-Control-Allow-Origin", $allowedOrigin);
		$response->header("Access-Control-Allow-Methods", config("cors.methods"));
		$response->header("Access-Control-Allow-Credentials", "true");
		$response->header("Access-Control-Allow-Headers", join(",",[config("cors.headers"),"Content-Type,Accept,Authorization,Cache-Control,X-REQUESTED-WITH"]));

		return $response;
	}
}
