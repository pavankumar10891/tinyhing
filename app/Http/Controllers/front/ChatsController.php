<?php
namespace App\Http\Controllers\front;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Chat;
use App\Model\Booking;

use App\Model\JobRequest;
use Auth,Blade,Config,Cache,Cookie,File,App,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image,Toast;
use Illuminate\Http\Request;

/**
* ChatsController
*
* Add your methods in the class below
*
* This file will render views from views/api
*/
 
class ChatsController extends BaseController {
	
	public function __construct(Request $request) {
		parent::__construct();
		$this->request = $request;

	}
	
	
	public function unreadNotificationCount(Request $request){
		$unread_notificaiton	=	DB::table("chats")->where("receiver_id",Auth::guard('api')->user()->parent_id)->where("is_read",0)->count("id");
		$response["status"]			=	"success"; 
		$response["msg"]			=	"";
		$response["data"]			=	$unread_notificaiton;
		return response()->json($response,200);
	}

	public function clientInboxList(){
		$userData = Booking::leftJoin('users as u', 'u.id', 'bookings.user_id')->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->select('u.name as customer', 'n.name as nanny', 'n.photo_id', 'bookings.id','bookings.nanny_id', 'bookings.user_id')->where('bookings.user_id', Auth::user()->id)->groupBy('bookings.nanny_id')->get();
		return View::make('front.dashboard.client_inbox', compact('userData'));
	}

	public function nannyInboxList(Request $request){
		$userData = Booking::leftJoin('users as u', 'u.id', 'bookings.user_id')->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->select('u.name as customer', 'n.name as nanny', 'u.photo_id', 'bookings.id','bookings.nanny_id', 'bookings.user_id')->where('bookings.nanny_id', Auth::user()->id)->groupBy('bookings.user_id')->get();
		return View::make('front.dashboard.nanny_inbox', compact('userData'));
	}
	public function chatHistory(Request $request){
		$DB				=	DB::table("chats");
		$sender_id		=	(!empty($request->input("sender_id"))) ? $request->input("sender_id") : 0;
		$receiver_id	=	(!empty($request->input("receiver_id"))) ? $request->input("receiver_id") : 0;
		
		$result			=	$DB
							->where(function ($query) use($sender_id,$receiver_id){
								$query->Orwhere(function ($query) use($sender_id,$receiver_id){
									$query->where("sender_id",$receiver_id);
									$query->where("receiver_id",$sender_id);
								});
								$query->Orwhere(function ($query) use($sender_id,$receiver_id){
									$query->where("receiver_id",$receiver_id);
									$query->where("sender_id",$sender_id);
								});
							})
							->select("chats.id","chats.sender_id","chats.receiver_id","chats.message","chats.is_media","chats.is_read","chats.created_at")->orderBy("id","ASC")->get()->toArray();
	
		DB::table("chats")
		->where("is_read",0)
		->where(function ($query) use($sender_id,$receiver_id){
			$query->Orwhere(function ($query) use($sender_id,$receiver_id){
				$query->where("sender_id",$receiver_id);
				$query->where("receiver_id",$sender_id);
			});
			$query->Orwhere(function ($query) use($sender_id,$receiver_id){
				$query->where("receiver_id",$receiver_id);
				$query->where("sender_id",$sender_id);
			});
		})
		->update(array("is_read"=>1));
		return View::make('front.dashboard.chat_history',compact('result'));
	}
}//end ChatsController
	