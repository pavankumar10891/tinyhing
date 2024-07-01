<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Auth,Blade,Config,Cache,Cookie,File,App,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image,Toast;
use Illuminate\Http\Request;

 
class ChatController extends BaseController {
	
	public function __construct(Request $request) {
		parent::__construct();
		$this->request = $request;

	}
	
	/**
	* Function use for get restaurant chat users listing
	*
	* @param null
	*
	* @return response
	*/
	public function chatlist(Request $request){
        $result = DB::table('support_chat')->pluck('user_id','user_id');
        $result = DB::table('users')->whereIn('id',$result)->select('id','name','photo_id','user_role_id')->get();
		return View::make('admin.chat.inbox',compact('result'));
	}
	
	/**
	* Function use for get  chat users listing
	*
	* @param null
	*
	* @return response
	*/
	public function getChatHistory(Request $request){
		$result = DB::table('support_chat')->where('user_id',$request->input('user_id'))->select('support_chat.*',DB::raw('(SELECT name FROM users WHERE id = support_chat.user_id) as receiver_name'),DB::raw('(SELECT photo_id FROM users WHERE id = support_chat.user_id) as receiver_photo_id'),DB::raw('(SELECT name FROM admins WHERE id = support_chat.support_id) as sender_name'))->get();
        return View::make('admin.chat.chat_history',compact('result'));
	}
	
	
}//end 
	