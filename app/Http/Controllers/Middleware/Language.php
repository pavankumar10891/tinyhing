<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Language
{
    public function handle($request, Closure $next) {
		//echo Session::get('applocale');die;
        if (Session::has('applocale')) { 
        //if (Session::has('applocale') AND array_key_exists(Session::get('applocale'), Config::get('languages'))) {
            App::setLocale(Session::get('applocale'));
			
        }else {
            App::setLocale(Config::get('app.fallback_locale'));
        }
        return $next($request);
    }
}