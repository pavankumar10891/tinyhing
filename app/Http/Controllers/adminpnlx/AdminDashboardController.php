<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Admin;
use App\Model\User;
use Illuminate\Http\Request;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
/**
* AdminDashBoard Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class AdminDashboardController extends BaseController {
		
		public function __construct(Request $request){
			$this->request = $request;
		}
		
/**
* Function for display adminpnlx dashboard
*
* @param null
*
* @return view page. 
*/
	public function showdashboard(Request $request){
		$totalSubscribers		=	User::where('user_role_id',SUBSCRIBER_ROLE_ID)->where("is_deleted",0)->count();
		$totalNannies			=	User::where('user_role_id',NANNY_ROLE_ID)->where("is_deleted",0)->count();
		$month							=	date('m');
		$year							=	date('Y');
		for ($i = 0; $i < 12; $i++) {
			$months[] 					=	date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
		}
		$months							=	array_reverse($months);
		$num							=	0;
		$allUsers						=	array();
		foreach($months as $month){
			$month_start_date			=	date('Y-m-01 00:00:00', strtotime($month));
			$month_end_date				=	date('Y-m-t 23:59:59', strtotime($month));
			$allUsers[$num]['month']	=	strtotime($month_start_date)*1000;
			$allUsers[$num]['subscribers']	=	DB::table('users')->where('user_role_id',SUBSCRIBER_ROLE_ID)->where('created_at','>=',$month_start_date)->where('created_at','<=',$month_end_date)->where('is_deleted',0)->count();
			$allUsers[$num]['nannies']=	DB::table('users')->where('user_role_id',NANNY_ROLE_ID)->where('created_at','>=',$month_start_date)->where('created_at','<=',$month_end_date)->where('is_deleted',0)->count();
			$num ++;
		}
		return  View::make('admin.dashboard.dashboard',compact("totalSubscribers","allUsers","totalNannies"));
	}
/**
* Function for display admin account detail
*
* @param null
*
* @return view page. 
*/
	public function myaccount(Request $request){
		return  View::make('admin.dashboard.myaccount');
	}// end myaccount()
/**
* Function for change_password
*
* @param null
*
* @return view page. 
*/	
	public function change_password(Request $request){
		return  View::make('admin.dashboard.change_password');
	}// end myaccount()
/**
* Function for update admin account update
*
* @param null
*
* @return redirect page. 
*/
	public function myaccountUpdate(Request $request){
		$thisData				=	$request->all(); 
		$this->request->replace($this->arrayStripTags($request->all()));
		$ValidationRule = array(
            'email' 			=> 'required|email',
            'name' 			=> 'required',
        );
        $validator 				= 	Validator::make($request->all(), $ValidationRule);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/myaccount')
				->withErrors($validator)->withInput();
		}else{
			$user 				= 	Admin::find(Auth::guard('admin')->user()->id);
			$user->name	 	= 	$request->input('name');
			$user->email	 	= 	$request->input('email');
			if($user->save()) {
				return Redirect::intended('adminpnlx/myaccount')
					->with('success', 'Information updated successfully.');
			}
		}
	}// end myaccountUpdate()
/**
* Function for changedPassword
*
* @param null
*
* @return redirect page. 
*/	
	public function changedPassword(Request $request){
		$thisData				=	$request->all(); 
		$this->request->replace($this->arrayStripTags($request->all()));
		$old_password    		= 	$request->input('old_password');
        $password         		= 	$request->input('new_password');
        $confirm_password 		= 	$request->input('confirm_password');
		Validator::extend('custom_password', function($attribute, $value, $parameters) {
			if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value) && preg_match('#[\W]#', $value)) {
				return true;
			} else {
				return false;
			}
		});
		$rules        		  	= 	array(
			'old_password' 		=>	'required',
			'new_password'		=>	'required|min:8|custom_password',
			'confirm_password'  =>	'required|same:new_password'
		);
		$validator 				= 	Validator::make($request->all(), $rules,
		array(
			"new_password.custom_password"	=>	"Password must have combination of numeric, alphabet and special characters.",
		));
		if ($validator->fails()){
			return Redirect::to('adminpnlx/change-password')
				->withErrors($validator)->withInput();
		}else{
			$user 				= Admin::find(Auth::guard('admin')->user()->id);
			$old_password 		= $request->input('old_password'); 
			$password 			= $request->input('new_password');
			$confirm_password 	= $request->input('confirm_password');
			if($old_password !=''){
				if(!Hash::check($old_password, $user->getAuthPassword())){
					/* return Redirect::intended('change-password')
						->with('error', 'Your old password is incorrect.');
						 */
					Session::flash('error',trans("Your old password is incorrect."));
					return Redirect::to('adminpnlx/change-password');
				}
			}
			if(!empty($old_password) && !empty($password ) && !empty($confirm_password )){
				if(Hash::check($old_password, $user->getAuthPassword())){
					$user->password = Hash::make($password);
				// save the new password
					if($user->save()) {
						Session::flash('success',trans("Password changed successfully."));
						return Redirect::to('adminpnlx/change-password');
					}
				} else {
					/* return Redirect::intended('change-password')
						->with('error', 'Your old password is incorrect.'); */
					Session::flash('error',trans("Your old password is incorrect."));
					return Redirect::to('adminpnlx/change-password');
				}
			}else{
				$user->username = $username;
				if($user->save()) {
					Session::flash('success',trans("Password changed successfully."));
					return Redirect::to('adminpnlx/change-password');
					/* return Redirect::intended('change-password')
						->with('success', 'Password changed successfully.'); */
				}
			}
		}
	}// end myaccountUpdate()
/* 
* For User Listing Demo 
*/
	public function usersListing(Request $request){
		return View::make('admin.user.user');
	}
} //end AdminDashBoardController()