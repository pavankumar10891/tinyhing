<?php
namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;
use Response;
use DB;
use Config;
use Input;
use Illuminate\Http\Request;
use App;
use App\Model\MobileApiLog;

class AuthCustomer
{
    /**
    * Run the request filter.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next){
		config(['auth.guards.api.provider' => 'customers']);
		if(!empty(Auth::guard('customer')->guest())){
			$response				=	array();
			$response["status"]		=	"error";
			$response["msg"]		=	"Unauthorized -- User login credentials is not valid.";
			return response()->json($response,401); 
		}
		if(!empty(Auth::guard('customer')->user()) && (Auth::guard('customer')->user()->is_active == 0 || Auth::guard('api')->user()->is_deleted == 1)){
			$response				=	array();
			$response["status"]		=	"error";
			$response["msg"]		=	"Unauthorized -- Invalid Access.";
			return response()->json($response,401); 
		}
		
		return $next($request);
    }
}
