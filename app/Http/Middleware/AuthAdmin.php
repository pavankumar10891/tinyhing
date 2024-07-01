<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;
Use Session;
Use App;

class AuthAdmin 
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
		App::setLocale("en");
		if(!empty(Auth::guard('admin')->guest())){
			return Redirect::to('/adminpnlx/login');
		}
	
        return $next($request);
    }
}
