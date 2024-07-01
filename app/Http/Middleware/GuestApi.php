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

class GuestApi
{
    /**
    * Run the request filter.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next){
		if(!empty($request->header('Accept-Language'))){
			App::setLocale($request->header('Accept-Language'));
		}else{
			App::setLocale("en");
		}
       // $request_by			    =  !empty($request->input("request_by")) ? $request->input("request_by") : "";
        /* if($request_by != "web"){
            $request_path   =   ltrim(urldecode($_SERVER["REQUEST_URI"]),"/");
            if(Config::get("Site.maintenance_mode") == 1){
                $response				=	array();
                $response["status"]		=	"error";
                $response["msg"]		=	"Service Unavailable -- We're temporarily offline for maintenance. Please try again later.";
                return response()->json($response,503);  
            }
    
            $st_api_key         = $request->header('SE-API-KEY');
            $st_api_signature   = $request->header('SE-API-SIGN');
            $st_api_timestamp   = $request->header('SE-API-TIMESTAMP');
    
            if($st_api_key == ""){
                $response				=	array();
                $response["status"]		=	"error";
                $response["msg"]		=	"Forbidden -- Api key required.";
                return response()->json($response,403);
            }else {
                $apiKey         =   PAYLOAD_API_KEY;
                $apiSecret      =   PAYLOAD_API_SECRET;
    
                if($st_api_timestamp == ""){
                    $response				=	array();
                    $response["status"]		=	"error";
                    $response["msg"]		=	"Forbidden -- Timestamp is required.";
                    return response()->json($response,403);
                }
                if($st_api_signature == ""){
                    $response				=	array();
                    $response["status"]		=	"error";
                    $response["msg"]		=	"Forbidden -- SIGN key is required.";
                    return response()->json($response,403);
                }
               if($st_api_timestamp > 0){
                   $get_timestamp_detail    =   MobileApiLog::where("api_timestamp",$st_api_timestamp)->where("request_path",$request->fullUrl())->where("request_type","request")->first();
                    if(!empty($get_timestamp_detail)){
                        $response				=	array();
                        $response["status"]		=	"error";
                        $response["msg"]		=	"Your request has been expired or rejected";
                        return response()->json($response,403);
                    }
                }else {
                    $response				=	array();
                    $response["status"]		=	"error";
                    $response["msg"]		=	"Forbidden -- Invalid timestamp.";
                    return response()->json($response,403);
                }
                $method = strtoupper($request->method());
                if($method == "GET" || $method == "DELETE"){
                    $body   =   "";
                }else {
                    $body   =   $request->all();
                }
                $convert_st_api_signature    =   $this->createSignature($request_path,$body,$st_api_timestamp,$method,$apiSecret);
                if($st_api_signature != $convert_st_api_signature){
                    $response				=	array();
                    $response["status"]		=	"error";
                    $response["msg"]		=	"Unauthorized -- Invalid signature.";
                    return response()->json($response,401);
                }
            }
        } */
        return $next($request);
    }

    public function createSignature($request_path = '', $body = '', $timestamp = false, $method = 'GET',$apiSecret) {

		$body = is_array($body) ? json_encode($body) : $body; // Body must be in json format

		$timestamp = $timestamp ? $timestamp : time() * 1000;

		$what = $timestamp . $method . $request_path . $body;
       
		return base64_encode(hash_hmac("sha256", $what, $apiSecret, true));
    }
    
}
