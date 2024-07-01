<?php
namespace App\Http\Controllers\front;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Chat;
use Auth,Blade,Config,Cache,Cookie,File,App,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image,Toast;
use Illuminate\Http\Request;

/**
* SupportController
*
* Add your methods in the class below
*
* This file will render views from views/api
*/
 
class SupportController extends BaseController {
	
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
        $result = DB::table('support_chat')->where('user_id',Auth::user()->id)->select('support_chat.*',DB::raw('(SELECT name FROM users WHERE id = support_chat.user_id) as receiver_name'),DB::raw('(SELECT photo_id FROM users WHERE id = support_chat.user_id) as receiver_photo_id'),DB::raw('(SELECT name FROM admins WHERE id = support_chat.support_id) as sender_name'))->get();
		return View::make('front.support.inbox',compact('result'));
	}
    
}//end
	