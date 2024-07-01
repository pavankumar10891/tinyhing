<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect,Session,App,Config;

class GuestFront 
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Session::has('applocale')) {
            App::setLocale(Session::get('applocale'));
        }else {
            App::setLocale(Config::get('app.fallback_locale'));
        }
		return $next($request);
    }
}
