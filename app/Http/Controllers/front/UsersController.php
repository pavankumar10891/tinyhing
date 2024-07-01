<?php

/**
 * User Controller
 */
namespace App\Http\Controllers\front;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Redirect;
use View;
use Input;
use Validator;
use Hash;
use Session;
use App\Model\User;
use App\Model\Testimonial;
use App\Model\WhyChooseUs;
use App\Model\OurCoreValues;
use App\Model\Cms;
use App\Model\Block;
use App\Model\Partners;
use App\Model\Banner;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\NewsLettersubscriber;
use App\Model\UserPlans;
use App\Model\UserQuotations;
use App\Model\Package;
use App\Model\ScheduleInterview;
use App\Model\NewsletterContact;
use Auth,Blade,Config,Cache,Cookie,DB,File,Mail,Response,URL,CustomHelper;
use Socialite;
use App\Model\FacebookReview;
use App\Model\Holiday;
use App\Model\Notification;
use App\Model\Earning;
use App\Model\Booking;

use Stripe;
use Stripe\Plan;


class UsersController extends BaseController {
	
/** 
* Function to redirect website on main page
*
* @param null
* 
* @return
*/
public function index(){
	$aboutUs 				= Block::where('slug', 'about-us')->first();
	$banners 				= Banner::where('is_active',1)->orderBy('order_number', 'asc')->get();
	$whychooseUs 			= WhyChooseUs::where('is_deleted',0)->orderBy('order_number', 'asc')->get();
	$whychooseUsHeading 	= Block::where('slug', 'why-choose-us')->first();
	$testimomials 			= Testimonial::orderBy('id', 'desc')->limit(2)->get();
	$testimomialsHeading 	= Block::where('slug', 'what-parents-say')->first();
	$corevalues 			= OurCoreValues::where('is_deleted',0)->orderBy('order_number', 'asc')->get();
	$corevaluesCenterImage 	= Block::where('slug', 'our-core-values')->first();
	$liveoutnanny1 	        = Block::where('slug', 'live-out-nanny-1')->first();
	$liveoutnanny2 	        = Block::where('slug', 'live-out-nanny-2')->first();
	$liveoutnanny3 	        = Block::where('slug', 'live-out-nanny-3')->first();
	$liveoutnanny4 	        = Block::where('slug', 'live-out-nanny-4')->first();
	$newsletterHeading 		= Block::where('slug', 'subscribe-our-newsletter')->first();
	$partners 				= Partners::where('is_deleted',0)->orderBy('id', 'desc')->get();
	$facebookRevies			= FacebookReview::orderBy('created_at', 'desc')->get();


	
	return View::make('front.users.index', compact('aboutUs', 'whychooseUs','whychooseUsHeading', 'testimomials','testimomialsHeading', 'corevalues', 'corevaluesCenterImage', 'newsletterHeading', 'partners','banners','liveoutnanny1','liveoutnanny2','liveoutnanny3', 'facebookRevies', 'liveoutnanny4'));
}

public function loginForm(){
	if(!empty(Auth::user())){
		Session::flash('success',trans("Your are already login"));
		return Redirect::to('/');
	}else{
		return View::make('front.users.login');
	}

}

public function clientloginForm(){
	if(!empty(Auth::user())){
		Session::flash('success',trans("Your are already login"));
		return Redirect::to('/');
	}else{
		return View::make('front.users.client_login');
	}

}


public function clientSignUpform(){

	if(!empty(Auth::user())){
		Session::flash('success',trans("Your are already login"));
		return Redirect::to('/');
	}else {
		return View::make('front.users.client_signup');

	}
}

public function login(Request $request){

	if(!empty(Auth::user())){
		return Redirect::to('/');
	}

	if($request->isMethod('post')){
		$request->replace($this->arrayStripTags($request->all()));
		$formData	=	$request->all();

		if(!empty($formData)){
			Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
			$validator = Validator::make(
				$request->all(),
				array(
					'password' => 'required|min:8',
					'email'    => 'required|email',
					//'g-recaptcha-response' => 'required|recaptcha'

				),
				array(
					'email.required' => 'Please enter the email',
					'email.email' => 'Please enter the valid email',
					'password.required' => 'Please enter the password',
					'password.min' => 'Password must be 8 characters long.',
					//'g-recaptcha-response.recaptcha' => 'Captcha verification failed',
					//'g-recaptcha-response.required'  =>'Please complete the captcha',
					)
			);

			if ($validator->fails()){
				$errors = [];

				$msgArr = (array) $validator->messages();

				$msgArr = array_shift($msgArr);

				$count = 0;

				foreach($msgArr as $key=>$val) {
					$errors[$key."_error"] = array_shift($val);
					$count++;
				}
				return response()->json(['success' => false, 'errors' => $errors]);
			}else{
				$email = $request->input('email');

				$user = User::where('email', $email)->first();

				if(!empty($user)) {
					if( Hash::check($request->input('password'), $user->password)) {
						if($user->user_role_id == NANNY_ROLE_ID) { 
							if($user->verified == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not verified.']);
							}elseif($user->is_active == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not active.']);
							}elseif($user->is_approved == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account approval is pending.']);
							}elseif($user->is_approved == 2) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account has been rejected.']);
							}else{
								$userdata = array(
									'email' 		=> $request->input('email'),
									'password' 		=> $request->input('password')
								);
								Auth::login($user);
								if(! empty($request->input('Remembercheck'))) {
									Session::flash('success', 'You are now logged in!');
									$userId = Auth::user()->id;
									return response()->json(['success' => true, 'page_redirect' => url('/dashboard')])->withCookie(cookie('remember', $request->input('Remembercheck'), 45000));
								}else{
									Session::flash('success', 'You are now logged in!');
										///$userId = Auth::user()->id;
									return response()->json(['success' => true, 'page_redirect' => url('/dashboard')]);
								}

							}
						} 
						elseif($user->user_role_id == SUBSCRIBER_ROLE_ID) { 
							if($user->verified == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not verified.']);
							}elseif($user->is_active == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not active.']);
							}else{
								$userdata = array(
									'email' 		=> $request->input('email'),
									'password' 		=> $request->input('password')
								);
								Auth::login($user);
								$location=$this->getGeoLocation();
								
								if(!empty($location['city_name'])){
							     	User::where('id', Auth::user()->id)->update(['city' => $location['city_name']]);
								}
								if(! empty($request->input('Remembercheck'))) {
									Session::flash('success', 'You are now logged in!');
									$userId = Auth::user()->id;
									return response()->json(['success' => true, 'page_redirect' => url('/dashboard')])->withCookie(cookie('remember', $request->input('Remembercheck'), 45000));
								}else{
									Session::flash('success', 'You are now logged in!');
										///$userId = Auth::user()->id;
									return response()->json(['success' => true, 'page_redirect' => url('/dashboard')]);
								}

							}
						} 
						else {
							return response()->json(['success' => 2, 'page_redirect' => 'login','message'=> "You don't have the rights to access this page.!"]);
						}
					} else {
						return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'The login credentials are invalid. Please enter again!' ]);
					}
				} else {
					return response()->json(['success' => 2, 'page_redirect' => 'login','message'=> 'The email is not registered in our system.!']);
				}
			}
		}
	}

}

public function nannyDashboard(){

	if(Auth::user()){
		if(Auth::user()->user_role_id == NANNY_ROLE_ID){
			$totalNannies  		= DB::table('bookings')->leftJoin('users', 'bookings.nanny_id', '=', 'users.id')->where('users.user_role_id',NANNY_ROLE_ID)->where('bookings.nanny_id',Auth::user()->id)->where('bookings.status',1)->where('bookings.is_deleted',0)->count();

			$totalInterviews    = DB::table('schedule_interview')->leftJoin('users', 'schedule_interview.nanny_id', '=', 'users.id')->where('users.user_role_id',NANNY_ROLE_ID)->where('schedule_interview.status',1)->where('schedule_interview.nanny_id',Auth::user()->id)->where('schedule_interview.is_deleted',0)->count();

			$data = [];
			// dd(Auth::user()->id);
			$data = DB::table('bookings')->leftJoin('users', 'bookings.nanny_id', '=', 'users.id')->where('users.user_role_id',NANNY_ROLE_ID)->where('bookings.nanny_id',Auth::user()->id)->where('bookings.status',1)->where('bookings.is_deleted',0)->where('bookings.booking_date', '>=', date('Y-m-d'))->select(['bookings.start_date as start','bookings.end_date as end'])->get();

			if(!empty($data)){
				foreach ($data as $key => &$value) {
					$value->title 	= 'Confirmed Bookings';
					$value->color 	= '#b0c8c1';
					$stop_date 		= date('Y-m-d', strtotime($value->end . ' +1 day'));
					$value->end 	= $stop_date;
				}
			}
			

			$schedule_interview = DB::table('schedule_interview')->leftJoin('users', 'schedule_interview.nanny_id', '=', 'users.id')->leftJoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('users.user_role_id',NANNY_ROLE_ID)->where('schedule_interview.status',1)->where('schedule_interview.nanny_id',Auth::user()->id)->where('schedule_interview.is_deleted',0)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->get(['schedule_interview.interview_date as start','day_availabilities.time_slot as time_slot']);
			// dd($schedule_interview);

			$holidays = DB::table('holidays')->leftJoin('users', 'holidays.user_id', '=', 'users.id')->where('holidays.user_id', Auth::user()->id)->where('users.user_role_id',NANNY_ROLE_ID)->get(['holidays.holiday_date as start']);

			if(!empty($holidays)){
				foreach ($holidays as $key1 => &$value1) {
					$value1->title = 'Holiday Dates';
					$value1->color = '#fce7b3';
					$data[] 	   = $value1;
				}
			}

			if(!empty($schedule_interview)){
				foreach ($schedule_interview as $keys => &$values) {
					$values->title = 'Scheduled Interviews';
					$values->color = '#7ce4ff';
					if($values->time_slot != ''){
						$values->description = 'Time Slot :' . ' '. $values->time_slot;

					}
					else{
						$values->description = 'No Time Slot';

					}
					$data[] = $values;
				}
			}
			$bookings = $data->toJson();
			$totalEarnings = Earning::where('nanny_id',Auth::user()->id)->sum('amount');

			$notifications = Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset(0)->limit(3)->get();

			return View::make('front.dashboard.nanny_dashboard', compact('bookings','totalNannies','totalInterviews','notifications','totalEarnings'));
			
		}elseif(Auth::user()->user_role_id == SUBSCRIBER_ROLE_ID){
			$totalNannies  		= DB::table('bookings')->leftJoin('users', 'bookings.user_id', '=', 'users.id')->where('users.user_role_id',SUBSCRIBER_ROLE_ID)->where('bookings.user_id',Auth::user()->id)->where('bookings.status',1)->where('bookings.is_deleted',0)->count();

			$totalInterviews    = DB::table('schedule_interview')->leftJoin('users', 'schedule_interview.user_id', '=', 'users.id')->where('users.user_role_id',SUBSCRIBER_ROLE_ID)->where('schedule_interview.status',1)->where('schedule_interview.user_id',Auth::user()->id)->where('schedule_interview.is_deleted',0)->count();
			$data = [];
			// dd(Auth::user()->id);
			$data = DB::table('bookings')->leftJoin('users', 'bookings.user_id', '=', 'users.id')->where('users.user_role_id',SUBSCRIBER_ROLE_ID)->where('bookings.user_id',Auth::user()->id)->where('bookings.is_deleted',0)->where('bookings.booking_date', '>=', date('Y-m-d'))->select(['bookings.start_date as start','bookings.end_date as end'])->get();

			if(!empty($data)){
				foreach ($data as $key => &$value) {
					$value->title = 'Booked Nanny';
					$value->color = '#d6dfff';
					$stop_date 		= date('Y-m-d', strtotime($value->end . ' +1 day'));
					$value->end 	= $stop_date;
				}
			}
			

			$schedule_interview = DB::table('schedule_interview')->leftJoin('users', 'schedule_interview.user_id', '=', 'users.id')->leftJoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('users.user_role_id',SUBSCRIBER_ROLE_ID)->where('schedule_interview.status',1)->where('schedule_interview.user_id',Auth::user()->id)->where('schedule_interview.is_deleted',0)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->get(['schedule_interview.interview_date as start','day_availabilities.time_slot as time_slot']);

			if(!empty($schedule_interview)){
				foreach ($schedule_interview as $keys => &$values) {
					$values->title = 'Scheduled Interviews';
					$values->color = '#7ce4ff';

					if($values->time_slot != ''){
						$values->description = 'Time Slot :' . ' '. $values->time_slot;

					}
					else{
						$values->description = 'No Time Slot';

					}
					$data[] = $values;
				}
			}
			$bookings = $data->toJson();


			$users   = Booking::leftJoin('users', 'bookings.user_id', '=', 'users.id')->where('users.user_role_id',SUBSCRIBER_ROLE_ID)->where('bookings.user_id',Auth::user()->id)->pluck('bookings.nanny_id')->toArray();
			 // dd($users);

			if(!empty($users)){
				$myNannies  = User::whereIn('id',$users)->where('is_active',1)->where('is_deleted',0)->get()->toArray();
			}
			else{
				$myNannies  = [];
			}
			$notifications = Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset(0)->limit(3)->get();
			return View::make('front.dashboard.cutomer_dashboard',compact('bookings','totalNannies','totalInterviews','myNannies','notifications')); 

		}else{
			return Redirect::to('/');
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
		}

	}else{
		return Redirect::to('/');
		Session::flash('error', 'Somthing went wrong. Please again after some time.');
	}
}

public function redirecttoSocial($type, $provider)
{   
	if(!empty($type == 'client') && !empty($provider)){
		Session::put('role_id', SUBSCRIBER_ROLE_ID);
	}
	if(!empty($type == 'nanny') && !empty($provider)){
		Session::put('role_id', NANNY_ROLE_ID);
	}
	return Socialite::driver($provider)->with(['type'=>$type])->redirect();
}

public function redirecttoSocialByClient($provider)
{
	return Socialite::driver($provider)->redirect();
}

public function Callback($provider,Request $request){
	$state = $request->input('state');
	parse_str($state, $result);
	$userSocial = Socialite::driver($provider)->stateless()->user();
	$role = Session::get('role_id');

	$users       =   User::where('is_deleted',0)->where('provider_id', $userSocial->getId())->first();
		//role 1 = client
		//role 2 = nannnay
	if($role == SUBSCRIBER_ROLE_ID){
		if($users){
			Session::flash('success', 'You are now logged in!');
			Auth::login($users);
			session()->forget('role_id');
			return redirect('/dashboard');
		}else{

			$name 			= $userSocial->getName();
			$email 			= $userSocial->getEmail();
			$provider_id 	=  $userSocial->getId();


			//$image 			=  $userSocial->getAvatar();
	
			$userImage    				= 	file_get_contents($userSocial->getAvatar());
			$userImageName     			= 	$userSocial->getId() ."_$provider.jpg";
			$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;	  
			$folderPath					=	USER_IMAGE_ROOT_PATH.$newFolder;

			$image  = $newFolder.$userImageName;
			if(!File::exists($folderPath)) {
				File::makeDirectory($folderPath, $mode = 0777,true);
			}
			file_put_contents($folderPath.$userImageName,$userImage);
			Session::put('user_data', array('name' => $name, 'email' =>$email, 'provider_id' => $provider_id, 'image' => $image));


					// get profile image  from social url  
			
			return Redirect::to('/pricing');
			/*$obj 						=  new User;
			$name 					    =  $userSocial->getName();*/
			/*$obj->name 					=  $name;
			$obj->email 				=  $userSocial->getEmail();
			$obj->provider_id 			=  $userSocial->getId();
			$obj->user_role_id 			=  SUBSCRIBER_ROLE_ID;
			$obj->provider	 		    =  $provider;
			$userImage    				= 	file_get_contents($userSocial->getAvatar());
			$userImageName     			= 	$userSocial->getId() ."_$provider.jpg";
			$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;	  
			$folderPath					=	USER_IMAGE_ROOT_PATH.$newFolder;

					// get profile image  from social url  
			if(!File::exists($folderPath)) {
				File::makeDirectory($folderPath, $mode = 0777,true);
			}
			file_put_contents($folderPath.$userImageName,$userImage);
			$obj->photo_id = $newFolder.$userImageName;

			$obj->verified		        =  1;
			$obj->is_active			    =  1;
			$obj->is_approved			=  1;
					//$obj->save();
			if($obj->save()){
				Auth::login($obj);
				Session::flash('success', 'You are now logged in!');
				return redirect('/');
			}else{
				Session::flash('error', 'something went to wrong');
				return redirect()->to('/');
			}*/
			session()->forget('role_id');	
		}	
	}else{
		if($users){
			if($users->verified == 0) {
				Session::flash('error', 'It seems your account is not verified.');
				return redirect('/');
			}elseif($users->is_active == 0) {
				Session::flash('error', 'It seems your account is not active.');
				return redirect('/');
			}elseif($users->is_approved == 0) {
				Session::flash('error', 'It seems your account approval is pending.');
				return redirect('/');
			}elseif($users->is_approved == 2) {
				Session::flash('error', 'It seems your account has been rejected.');
				return redirect('/');
			}else{
				Session::flash('success', 'You are now logged in!');
				Auth::login($users);
				session()->forget('role_id');
				return redirect('/dashboard');
			}
		}else{
			$name 			= $userSocial->getName();
			$email 			= $userSocial->getEmail();
			$provider_id 	=  $userSocial->getId();
			$image 			=  $userSocial->getAvatar();
			return View::make('front.users.signup', compact('name', 'email', 'provider', 'provider_id','image'));
		}
	}


}

public function signUpForm(){
	if(!empty(Auth::user())){
		Session::flash('success',trans("Your are already login"));
		return Redirect::to('/');
	}else {
		return View::make('front.users.signup');

	}
}

public function signUp(Request $request){
	$request->replace($this->arrayStripTags($request->all()));
	$formData						=	$request->all();

	if(!empty($formData)){	
		Validator::extend('custom_password', function($attribute, $value, $parameters) {
			if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value)  && preg_match('#[\W]#', $value)) {
				return true;
			} else {
				return false;
			}
		});
		if(!empty($request->input('user_image'))){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		    		=> 'required',
					'email' 				=> 'required|email|unique:users,email',
					'postcode'				=> 'required|numeric',
					'resume'				=> 'mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'cpr_certificate'		=> 'mimes:'.CAREER_FORM_DOCUMENTS,
					'other_certificates'	=> 'mimes:'.OTHER_CERTIFICATES_EXTENSION,
					'identification_type'	=> 'required',
					'identification_file'	=> 'required|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'nanny_type'		    => 'required',
				),	
				array(
					"name.required"					=>	trans("The name field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email must be unique."),
					"postcode.required" 			=>  trans("The postcode field is required"),
					"postcode.numeric" 				=>  trans("The postcode field must be numeric"),
					"resume.mimes"					=>	trans("The resume must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					//"resume.required" 			    =>  trans("The resume field is required"),
					"cpr_certificate.mimes"			=>	trans("The cpr certificate must be in: 'pdf, docx, doc formats'"),
					"other_certificates.custom_other_certificate"		=>	trans("The other certificates must be in: 'pdf, docx, doc formats'"),
					"identification_file.mimes"					=>	trans("The uploaded file must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"identification_file.required"					=>	trans("This field is required."),
					"identification_type.required"					=>	trans("The Identification Type field is required."),
					"nanny_type.required"					=> trans("The Nanny Type field is required."),
				)
			);
		}else{
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		    		=> 'required',
					'email' 				=> 'required|email|unique:users,email',
					'postcode'				=> 'required|regex:/^[a-zA-Z0-9 ]+$/',
					'photo_id'				=> 'required|mimes:'.IMAGE_EXTENSION,
					'resume'				=> 'mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'cpr_certificate'		=> 'mimes:'.CAREER_FORM_DOCUMENTS,
					'other_certificates'	=> 'mimes:'.OTHER_CERTIFICATES_EXTENSION,
					'identification_type'	=> 'required',
					'identification_file'	=> 'required|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'nanny_type'		    => 'required',
				),	
				array(
					"name.required"					=>	trans("The name field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email must be unique."),
					"postcode.required" 			=>  trans("The postcode field is required"),
					"postcode.regex" 				=>  trans("The postcode field must be numeric or alphabets "),
					"photo_id.required"				=>  trans("The photo field is required"),
					"photo_id.mimes"				=>  trans("The photo must be in: 'jpeg, jpg, png, gif, bmp formats'"),
					"resume.mimes"					=>	trans("The resume must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					//"resume.required" 			    =>  trans("The resume field is required"),
					"cpr_certificate.mimes"			=>	trans("The cpr certificate must be in: 'pdf, docx, doc formats'"),
					"other_certificates.custom_other_certificate"		=>	trans("The other certificates must be in: 'pdf, docx, doc formats'"),
					"identification_file.mimes"					=>	trans("The uploaded file must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"identification_file.required"					=>	trans("This field is required."),
					"identification_type.required"					=>	trans("The Identification Type field is required."),
					"nanny_type"					=> trans("The Nanny Type field is required."),
					"nanny_type"					=> trans("The Nanny Type field is required."),
				)
			);
		}

		if ($validator->fails()){
			$errors = [];

			$msgArr = (array) $validator->messages();

			$msgArr = array_shift($msgArr);

			$count = 0;

			foreach($msgArr as $key=>$val) {
				$errors[$key."_error"] = array_shift($val);

				$count++;
			}
			return response()->json(['success' => false, 'errors' => $errors]);
		}else{ 

			$ziplatlong = $this->getLatLngFromZipCode( $request->input('postcode'));
			$validateString		     	=  md5(time().$request->input('email'));
			$obj 						=  new User;

			$nameLength = strlen($request->input('name'));
			$name = $request->input('name');
			if($nameLength >= 3){
				$randomCode = $this->generateRandomString(3);
				$refferCode = substr($request->input('name'), 0, 3);
				$name = $refferCode.$randomCode;
			}
			else if($nameLength == 2){
				$randomCode = $this->generateRandomString(4);
				$refferCode = substr($request->input('name'), 0, 2);
				$name = $refferCode.$randomCode;
				
			}
			else if($nameLength == 1){
				$randomCode = $this->generateRandomString(5);
				$refferCode = substr($request->input('name'), 0, 1);
				$name = $refferCode.$randomCode;
				
			}

			$obj->name 			=  		   !empty($request->input('name')) ? ucfirst($request->input('name')) :'';
			$obj->provider	 		    =  !empty($request->input('provider')) ? $request->input('provider'):'';
			$obj->provider_id 			=  !empty($request->input('provider_id')) ? $request->input('provider_id'):'';
				//$obj->last_name 			=  ucfirst($request->input('last_name'));
				//$name 					    =  $obj->first_name." ".$obj->last_name;
				//$obj->name 					=  $name;
				//$obj->slug 					=  $this->getSlug($name,'name','User');
			$obj->email 				=  $request->input('email');
			$obj->nanny_type 		    =  $request->input('nanny_type');
			
				//$obj->phone_number 			=  $request->input('phone_number');
			$obj->user_role_id 			=  NANNY_ROLE_ID;
				//$obj->password	 		    =  Hash::make($request->input('password'));
			$obj->validate_string	    =  $validateString;
			// try{
			// 	$zoom = new \MacsiDigital\Zoom\Support\Entry;
			// 	$user = new \MacsiDigital\Zoom\User($zoom);
			// 	$userRecord = $user->create([
			// 		'first_name' => !empty($request->input('name')) ? ucfirst($request->input('name')) :'',
			// 		'last_name' => '',
			// 		'email' => $request->input('email'),
			// 		'password' => rand(10000000,99999900)
			// 	]);
			// 	$obj->zoom_id	=  	!empty($userRecord) ? $userRecord->id : '';	
			// }catch (Exception $e) {
			// 	$obj->zoom_id	=  	'';
			// }
			if(!empty($obj->provider_id)){
				$obj->verified		        =  1;
			}else{
				$obj->verified		        =  0;
			}
			$obj->is_active			    =  1;
			$obj->postcode			    =  $request->input('postcode');
			$obj->latitude			    =  $ziplatlong['zipLat'];
			$obj->longitude			    =  $ziplatlong['ziplng'];
			$obj->referral_code			=  $name;

			if($request->hasFile('photo_id')){ 
				$extension 		=	$request->file('photo_id')->getClientOriginalExtension();
				$fileName		=	time().'-photo-id.'.$extension;
				$folderName     = 	strtoupper(date('M'). date('Y'))."/";
				$folderPath		=	USER_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('photo_id')->move($folderPath, $fileName)){
					$obj->photo_id =	$folderName.$fileName;
				}
			}elseif(!empty($request->input('user_image'))){
				$userImage    				= 	file_get_contents($request->input('user_image'));
				$userImageName     			= 	$obj->provider_id ."_$obj->provider.jpg";
				$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;	  
				$folderPath					=	USER_IMAGE_ROOT_PATH.$newFolder;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				file_put_contents($folderPath.$userImageName,$userImage);
				$obj->photo_id = $newFolder.$userImageName;
			}

			if($request->hasFile('resume')){ 
				$extension 		=	$request->file('resume')->getClientOriginalExtension();
				$fileName		=	time().'-resume.'.$extension;
				$folderName     = 	strtoupper(date('M'). date('Y'))."/";
				$folderPath		=	CERTIFICATES_AND_FILES_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('resume')->move($folderPath, $fileName)){
					$obj->resume =	$folderName.$fileName;
				}
			}

			if($request->hasFile('cpr_certificate')){ 
				$extension 		=	$request->file('cpr_certificate')->getClientOriginalExtension();
				$fileName		=	time().'-CPR-certificate.'.$extension;
				$folderName     = 	strtoupper(date('M'). date('Y'))."/";
				$folderPath		=	CERTIFICATES_AND_FILES_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('cpr_certificate')->move($folderPath, $fileName)){
					$obj->cpr_certificate =	$folderName.$fileName;
				}
			}
			$obj->identification_type    =   $request->input('identification_type'); 
			if($request->hasFile('identification_file')){ 
				$extension 		=	$request->file('identification_file')->getClientOriginalExtension();
				$fileName		=	($request->identification_type==1)?time().'-passport.'.$extension : time().'-drivinglicense.'.$extension;
				$folderName     = 	strtoupper(date('M'). date('Y'))."/";
				$folderPath		=	CERTIFICATES_AND_FILES_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('identification_file')->move($folderPath, $fileName)){
					$obj->identification_file =	$folderName.$fileName;
				}
			}

			$obj->save();
			$userId					    =  $obj->id;

			if ($request->hasFile('other_certificates')) {
				$image_count	=	1;
				foreach ($request->file('other_certificates') as $file) {
					if (!empty($file)) {
						$model				=   new userCertificates;
						$model->user_id		=   $userId;
						$extension  		=	$file->getClientOriginalExtension();
						$fileName			=	time(). $image_count .'-other-certificates.'.$extension;
						$folderName  		=	strtoupper(date('M'). date('Y'))."/";
						$folderPath			=	OTHER_CERTIFICATES_DOCUMENT_ROOT_PATH.$folderName;
						if (!File::exists($folderPath)) {
							File::makeDirectory($folderPath, $mode = 0777, true);
						}
						if ($file->move($folderPath, $fileName)) {
							$model->other_certificates	=	$folderName.$fileName;
						}
						$model->save();
					}
					$image_count++;
				}
			}		

			if(!$userId){
				Session::flash('error', trans("Something went wrong.")); 
				return response()->json(['success' => false, 'page_redirect' => url('/')]);

			}

			$settingsEmail 			=	Config::get('Site.from_email'); 
			$full_name				= 	$obj->name; 
			$email					= 	$obj->email;
			$emailActions			= 	EmailAction::where('action','=','thankyou_nanny_register')->get()->toArray();
			$emailTemplates			= 	EmailTemplate::where('action','=','thankyou_nanny_register')->get(array('name','subject','action','body'))->toArray();
			$cons 					= 	explode(',',$emailActions[0]['options']);
			
			$constants 				= 	array();
			foreach($cons as $key => $val){
				$constants[] 		= 	'{'.$val.'}';
			}
			
			$subject 				= 	$emailTemplates[0]['subject'];
			$rep_Array 				= 	array($obj->name); 
			$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
			$mailNanny				= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
			//Email Send to Nanny

			//Email Send to Nanny
			$settingsEmail 			=	Config::get('Site.from_email'); 
			$full_name				= 	'Admin'; 
			$adminEmail					= 	Config::get('Site.to_email');
			$emailActions2			= 	EmailAction::where('action','=','admin_site')->get()->toArray();
			$emailTemplates2			= 	EmailTemplate::where('action','=','admin_site')->get(array('name','subject','action','body'))->toArray();
			$cons2 					= 	explode(',',$emailActions2[0]['options']);
			
			$constants2 				= 	array();
			foreach($cons2 as $key => $val){
				$constants2[] 		= 	'{'.$val.'}';
			}
			
			$subject2 				= 	$emailTemplates2[0]['subject'];
			$rep_Array2 				= 	array($obj->email); 
			$messageBody2			= 	str_replace($constants2, $rep_Array2, $emailTemplates2[0]['body']);
			$mail					= 	$this->sendMail($adminEmail,'Admin',$subject2,$messageBody2,$settingsEmail);
			Session::flash('success',trans("Your account has been registered successfully and sent request to admin for approval."));
			return response()->json(['success' => true, 'page_redirect' => url('/')]);

		}
	}
}

public function userSignUpform(Request $request){


	$plandata = '';
	$coupenData = '';
	if(!empty(Auth::user())){
		Session::flash('success',trans("Your are already login"));
		return Redirect::to('/');
	}else{
		   //dd($request->session());
		if(Session::has('quotation')){
			$sessionData = $request->session()->get('quotation');

			if($sessionData['plan_id'] != ''){
				/*SEND COUPEN DATA*/

				if($sessionData['coupen_code']!=''){

					$coupenId     =  base64_decode($sessionData['coupen_code']) ; 
					$coupenData   =  CustomHelper::getCoupenById($coupenId);

				}
				/*SEND COUPEN DATA*/

				$planId   = $sessionData['plan_id'];
				$planinfo = Package::where(['id' => $planId])->first();
				$features = '';
				if(!empty($planinfo)){
					if($planinfo->slug = 'standard'){
						$features 	= CustomHelper::getmasterByType('standard ');
					}
					if($planinfo->slug = 'pro'){
						$features 	= CustomHelper::getmasterByType('pro');
					}
					if($planinfo->slug = 'advanced'){
						$features 	= CustomHelper::getmasterByType('advanced');
					}

					return View::make('front.users.usersignup' , compact('plandata', 'planinfo', 'features', 'coupenData'));
				}else{
					Session::flash('success',trans("Something went wrong"));
					return Redirect::to('/');
				}

			}else{
				Session::flash('error',trans("Something went wrong")); 
				return Redirect::to('/pricing');
			}
			
		}else{
			Session::flash('success',trans("Something went wrong")); 
			return Redirect::to('/pricing');

		} 			
	}
}


public function planFormSubmit(Request $request){
	if(!empty(Auth::user())){
		$coupon_id = '';
		$userid = Auth::user()->id; 

		if( $request->planid &&  $request->planid!='' ){
			$Plan = CustomHelper::getPlanById($request->planid);
			$planmonth = !empty($Plan->no_of_month) ? $Plan->no_of_month:0;

			if( $request->coupen_code &&  $request->coupen_code!='' ){

				$coupon_id = base64_decode($request->coupen_code);

			}

				
				$stripe = new \Stripe\StripeClient(
					env('STRIPE_SECRET')
				);
				$response = $stripe->customers->retrieveSource(
					Auth::user()->customer_id,
					$request->card_id
				);

            			$planRetrive = \Stripe\Plan::retrieve($Plan->slug);
						$PriceData = \Stripe\Price::create([
							'unit_amount' => $planRetrive['amount'],
							'currency' => $planRetrive['currency'],
							'recurring' => ['interval' => 'month', 'interval_count'=>$Plan->no_of_month],
							'product' => $planRetrive['product'],
						]);

					$SubscriptionData =  \Stripe\Subscription::create([	
						'customer' => Auth::user()->customer_id,
						'items' => [
							['price' => $PriceData['id']],
						],
					]);

					//echo "<pre>";print_r($SubscriptionData);

				/* 	if($sessionData['coupen_code'] &&  $sessionData['coupen_code']!=''){

						$coupenId     =  base64_decode($sessionData['coupen_code']) ; 
						DB::table('coupon_codes')->where('id',$coupenId)->update(['is_active' =>'0']);

					  }


 */					  $checkUserPlan = UserPlans::where('user_id',Auth::user()->id)->where('status',1)->orderBY('id', 'desc')->first();
					  if(!empty($checkUserPlan)){
					  	Session::flash('error',trans("Please Cancel the plan first, After the retry"));
					  	return response()->json(['success' => true, 'page_redirect' => url('/pricing')]); 
					  }else{

						  $obj 					 =  new UserPlans;
						  $obj->user_id		     =  $userid; 
						  $obj->plan_id		     =  $request->planid; 
						  $obj->coupon_code_id     =  $coupon_id; 
						  $obj->plan_start_date    =  date('Y-m-d'); 
						  $obj->plan_end_date		 =  date('Y-m-d', strtotime("+".$planmonth." months", strtotime(date('Y-m-d'))));
						  $obj->save();


						    if(Session::has('quotation')) {
							  	$sessionData = $request->session()->get('quotation');
							  	if(!empty($sessionData['quotation_data'])){

							  		$quotationData = $sessionData['quotation_data']; 
							  		$quotationobj 					 =  new UserQuotations;
							  		$quotationobj->user_id		     =  $userid; 
							  		$quotationobj->plan_id		     =  $request->planid ;
							  		$quotationobj->week		         =  $quotationData['weeks']; 
							  		$quotationobj->children		     =  $quotationData['children']; 
							  		$quotationobj->price		     =  $quotationData['price']; 
							  		$quotationobj->save();

							  	}

							}


						  Session::forget('quotation');
						  Session::flash('success',trans("Your plan has been activated successfully"));
						  return response()->json(['success' => true, 'page_redirect' => url('/')]);




					  }
					  

					   

					}else{

						Session::flash('success',trans("There is some problem please try again"));
						return response()->json(['success' => false, 'page_redirect' => url('/')]); 

					}

				}else{

					if( $request->planid &&  $request->planid!='' ){

						if(Session::has('quotation')) {

							Session::put('quotation.plan_id',$request->planid);

						}else{ 

							Session::put('quotation.plan_id', $request->planid);

						}

						if($request->coupen_code && $request->coupen_code!='' ){
							/* //$coupon_id = base64_decode($request->coupen_code); */

							Session::put('quotation.coupen_code',$request->coupen_code );
						}else{
							Session::put('quotation.coupen_code', '');

						}


						return response()->json(['success' => true, 'page_redirect' => url('/user-signup')]);

					}else{

						Session::flash('success',trans("There is some problem please try again"));
						return response()->json(['success' => false, 'page_redirect' => url('/')]);

					}
				}
			}



			public function userSignUp(Request $request){

				$request->replace($this->arrayStripTags($request->all()));
				$formData						=	$request->all();
				$this_year = date("y");
				if(Session::has('quotation')){
					$sessionData = $request->session()->get('quotation');
					if(empty($sessionData['plan_id'])){
						Session::flash('error','Something went to wrong');
						$response = 'Something went to wrong';
						return response()->json(['success' => true, 'page_redirect' => url('/'),'response' => $response]);
					}
				}

				if(!empty($formData)){
					$validator 					=	Validator::make(
						$request->all(),
						array(
							'first_name'		    	=> 'required',
							'email' 					=> 'required|email|unique:users,email',
							'phone_number' 				=> 'digits_between:10,15|numeric',
							'name'						=> 'required',
							'card-number'				=> 'required|numeric',
							'cvc'						=> 'required|numeric',
							'card-expiry-month'			=> 'required|numeric',
							'card-expiry-year'			=> "required|numeric|min:$this_year|max:$this_year+10",
						),	
						array(
							"first_name.required"			=>	trans("The name field is required."),
							"email.required"				=>	trans("The email field is required."),
							"email.email"					=>	trans("The email is not valid email address."),
							"email.unique"					=>	trans("The email already exist."),
							"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
							"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
							"name.required"						=>	trans("The name field is required."),
							"card-number.required"				=>	trans("The card number field is required."),
							"card-number.numeric"				=>	trans("The card number must be a number."),
							"cvc.required"						=>	trans("The cvc field is required."),
							"cvc.numeric"						=>	trans("The cvc must be a number."),
							"card-expiry-month.required"		=>	trans("The card expiry month field is required."),
							"card-expiry-month.numeric"			=>	trans("The card expiry month must be a number."),
							"card-expiry-year.required"			=>	trans("The card expiry year field is required."),
							"card-expiry-year.numeric"			=>	trans("The card expiry year must be a number."),
							"card-expiry-year.min"				=>	trans("The card expiry year is invalid."),
							"card-expiry-year.max"				=>	trans("The card expiry year is invalid."),
						)
					);
					if ($validator->fails()){
						$errors = [];
						$msgArr = (array) $validator->messages();
						$msgArr = array_shift($msgArr);
						$count = 0;
						foreach($msgArr as $key=>$val) {
							$errors[$key."_error"] = array_shift($val);
							$count++;
						}
						return response()->json(['success' => false, 'errors' => $errors]);
					}else{ 
						if(!empty($request->input('referral_code'))){
							$checkCode = User::where('referral_code',$request->input('referral_code'))->first();
							if(!$checkCode){
								$errors = [];
								$errors['referral_code_error'] = 'No such Referral Code';
								return response()->json(['success' => false, 'errors' => $errors]);
							}
						}
						$validateString		     	=  md5(time().$request->input('email'));
						$obj 						=  new User;
						$obj->first_name 			=  !empty($request->input('first_name')) ? ucfirst($request->input('first_name')) : '';
						$obj->last_name 			=  !empty($request->input('last_name')) ? ucfirst($request->input('last_name')) :'';
						$name 					    =  $obj->first_name." ".$obj->last_name;
						$obj->name 					=  ucfirst($name);
						$obj->slug 					=  $this->getSlug($name,'name','User');
						$obj->email 				=  $request->input('email');
						$obj->phone_number 			=  !empty($request->input('phone_number')) ? $request->input('phone_number'):'';
						$obj->user_role_id 			=  SUBSCRIBER_ROLE_ID;
						//$obj->password	 		    =  Hash::make($request->input('password'));
						$obj->validate_string	    =  $validateString;
						$obj->is_active			    =  1;
						$obj->provider_id			=  !empty($request->input('provider_id')) ? $request->input('provider_id'):'';
						$obj->photo_id				=  !empty($request->input('image')) ? $request->input('image'):'';
						if(!empty($request->input('provider_id'))){
							$obj->verified		        =  1;
							$obj->is_approved			=  1; 
						}else{
							$obj->verified		        =  0;
						}
						
						$obj->save();
						$userId					    =  $obj->id;
						if(!$userId){
							Session::flash('error', trans("Something went wrong.")); 
							return response()->json(['success' => false, 'page_redirect' => url('/')]);
						}
						session()->forget('user_data');

						if(!empty($request->input('referral_code'))){
							$checkCode = User::where('referral_code',$request->input('referral_code'))->first();
							if($checkCode){

								$nameLength = strlen($checkCode->name);
								$name 		= $checkCode->name;
								if($nameLength >= 3){
									$randomCode = $this->generateRandomString(3);
									$refferCode = substr($checkCode->name, 0, 3);
									$name = $refferCode.$randomCode;
								}
								else if($nameLength == 2){
									$randomCode = $this->generateRandomString(4);
									$refferCode = substr($checkCode->name, 0, 2);
									$name = $refferCode.$randomCode;

								}
								else if($nameLength == 1){
									$randomCode = $this->generateRandomString(5);
									$refferCode = substr($checkCode->name, 0, 1);
									$name = $refferCode.$randomCode;
								}
								User::where('id',$checkCode->id)->update(array('referral_code'=>$name));
								$amount = 0;

								/*if(!empty($sessionData['plan_id'])){
									$sessionData['plan_id']

								}*/
								$price = 0;
								if(!empty($request->coupen_code)){
								  	$checkCoupanwithTotalPrice =  CustomHelper::checkCoupenCode($request->coupen_code,$sessionData['plan_id']);
								  	if($checkCoupanwithTotalPrice['final_price'] > 0){
								  		$price = $checkCoupanwithTotalPrice['final_price'];
								  	}
								}else{
									$plan = Package::where('id', $sessionData['plan_id'])->first();
									$price = $plan->price;
								}
								

								 if(!empty($sessionData['plan_id'])){ 
									$amount                     = ($price * Config::get('Nanny.referral_code')) / 100;
									$earning 					= new Earning;
									$earning->user_id 			= $userId;
									$earning->nanny_id 			= $checkCode->id;
									$earning->amount 			= $amount;
									$earning->total_amount		= $amount;
									$earning->status 			= 1;
									$earning->type 				= 4;
									$earning->save();
								 }
								
							}
						}
						

						if(Session::has('quotation')){
							$sessionData = $request->session()->get('quotation');
							
							if($sessionData['plan_id']!=''){
								$coupon_id  =0;
								$checkCoupanwithTotalPrice =  CustomHelper::checkCoupenCode($request->coupen_code,$sessionData['plan_id']);
								if(!empty($checkCoupanwithTotalPrice['coupendata'])){
									$coupon_id     =  $checkCoupanwithTotalPrice['coupendata']->id; 
								}	
								$Plan = CustomHelper::getPlanById($sessionData['plan_id']);
								$planmonth = !empty($Plan->no_of_month) ? $Plan->no_of_month:0;

								$objplan 					 =  new UserPlans;
								$objplan->user_id		     =  $userId; 
								$objplan->plan_id		     =  $sessionData['plan_id']; 
								$objplan->coupon_code_id     =  $coupon_id; 
								$objplan->plan_start_date    =  date('Y-m-d'); 
								$objplan->plan_end_date	=  date('Y-m-d', strtotime("+".$planmonth." months", strtotime(date('Y-m-d'))));;  
								$objplan->save();

								if(!empty($sessionData['quotation_data'])){
									$quotationData = $sessionData['quotation_data']; 
									$quotationobj 					 =  new UserQuotations;
									$quotationobj->user_id		     =  $userId; 
									$quotationobj->plan_id		     =  $sessionData['plan_id']; 
									$quotationobj->week		         =  $quotationData['weeks']; 
									$quotationobj->children		     =  $quotationData['children']; 
									$quotationobj->price		     =  $quotationData['price']; 
									$quotationobj->save();

								}
							}
						}


						$modelId = $userId;
						Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
						$customerId = User::where('id',$modelId)->value('customer_id');
						if(!empty($customerId)){
							$customer = Stripe\Customer::retrieve("$customerId");
							try{
								$customer->sources->create(array("source" => $request->input('stripe_token')));	
								$customerId = User::where('id',$modelId)->value('customer_id');
								$customer = array();
								if(!empty($customerId)){
									$customer = Stripe\Customer::retrieve("$customerId");
								}
								$response["status"]			=	"success";
								$response["message"]		=	'Card has been added.';
								$response["cardlist"]		=	$customer;
								if(!empty($objplan) && $objplan->plan_id > 0){
									$dataPlan = CustomHelper::getPlanById($objplan->plan_id);
									$email 			    		=	Auth::user()->email;
									$full_name					= 	Auth::user()->name;
									$plan_name					= 	$dataPlan->name;
									$settingsEmail		    	= 	Config::get('Site.to_email');
									$emailActions				= 	EmailAction::where('action','=','subscribe_plan')->get()->toArray();
									$emailTemplates	    		= 	EmailTemplate::where('action','=','subscribe_plan')->get(array('name','subject','action','body'))->toArray();
									$cons 						= 	explode(',',$emailActions[0]['options']);

									$constants 					= 	array();
									foreach($cons as $key => $val){
										$constants[] 		= 	'{'.$val.'}';
									}

									$subject 				= 	$emailTemplates[0]['subject'];
									$rep_Array 				= 	array($full_name,$plan_name);  
									$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
									$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
								}
							}catch(Stripe\Exception\CardException $e) {
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\RateLimitException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\InvalidRequestException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\AuthenticationException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\ApiConnectionException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\ApiErrorException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Exception $e) {
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							}
						}else{
							$customer = Stripe\Customer::create([
								'source' => $request->input('stripe_token'),
								'email' => $obj->email,
								'name' =>  $request->input('name'),
							]);


							User::where('id',$modelId)->update(array('customer_id'=>$customer->id));
							$customerId = $customer->id;
							$customer = Stripe\Customer::retrieve("$customerId");


							try{
						// $customer->sources->create(array("source" => $request->input('stripe_token')));



								$customerId = User::where('id',$modelId)->value('customer_id');
								$customer = array();
								if(!empty($customerId)){
									$customer = Stripe\Customer::retrieve("$customerId");
								}
								$response["status"]			=	"success";
								$response["message"]		=	trans("messages.card_has_been_added");
								$response["cardlist"]		=	$customer;

								if(!empty($objplan) && $objplan->plan_id > 0){
									$dataPlan = CustomHelper::getPlanById($objplan->plan_id);
									$email 			    		=	$obj->email;
									$full_name					= 	$name ;
									$plan_name					= 	$dataPlan->name;
									$settingsEmail		    	= 	Config::get('Site.to_email');
									$emailActions				= 	EmailAction::where('action','=','subscribe_plan')->get()->toArray();
									$emailTemplates	    		= 	EmailTemplate::where('action','=','subscribe_plan')->get(array('name','subject','action','body'))->toArray();
									$cons 						= 	explode(',',$emailActions[0]['options']);

									$constants 					= 	array();
									foreach($cons as $key => $val){
										$constants[] 		= 	'{'.$val.'}';
									}

									$subject 				= 	$emailTemplates[0]['subject'];
									$rep_Array 				= 	array($full_name,$plan_name);  
									$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
									$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
								}

							}catch(Stripe\Exception\CardException $e) {
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\RateLimitException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\InvalidRequestException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\AuthenticationException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\ApiConnectionException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Stripe\Exception\ApiErrorException $e){
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							} catch (Exception $e) {
								$response['status'] = 'card_error';
								$response['message'] = $e->getError()->message;
							}
						}


						if ($response['status'] == 'card_error'){
							Session::flash('error',$response['message']);
							return response()->json(['success' => false, 'err' => $response['message']]);
						}

						$customerId = User::where('id',$modelId)->value('customer_id');
						if(Session::has('quotation')){
							if($objplan){
								$dataPlan = CustomHelper::getPlanById($objplan->plan_id);
							}
						}
						

						if(!empty($dataPlan)){

							$planRetrive = \Stripe\Plan::retrieve($dataPlan->slug);
							$PriceData = \Stripe\Price::create([
								'unit_amount' => $planRetrive['amount'] * 100,
								'currency' => $planRetrive['currency'],
								'recurring' => ['interval' => 'month', 'interval_count'=>$dataPlan->no_of_month],
								'product' => $planRetrive['product'],
							]);

							$SubscriptionData =  \Stripe\Subscription::create([	
								'customer' => $customerId,
								'items' => [
									['price' => $PriceData['id']],
								],
							]);
							if(!empty($SubscriptionData) && $SubscriptionData['status'] == 'active'){
								$saveUserPlan =  UserPlans::where('user_id', $modelId)->where('plan_id', $dataPlan->id)->first();
								$saveUserPlan->plan_start_date  	= date('Y-m-d', $SubscriptionData['current_period_start']);
								$saveUserPlan->plan_end_date  		= date('Y-m-d', $SubscriptionData['current_period_end']);
								$saveUserPlan->status 				= 1;
								$saveUserPlan->subscription_id 	    = $SubscriptionData['id']; 
								$saveUserPlan->save();

							}

						}

						Session::forget('quotation');

						$email 			    	=	$obj->email;
						$full_name				= 	$name ; 
						$route_url     			=   WEBSITE_URL.'user-verificaion/'.$validateString;
						$click_link   			=   $route_url;
						$settingsEmail		    = 	Config::get('Site.to_email');
						$emailActions			= 	EmailAction::where('action','=','set_your_password')->get()->toArray();
						$emailTemplates	    	= 	EmailTemplate::where('action','=','set_your_password')->get(array('name','subject','action','body'))->toArray();
						$cons 					= 	explode(',',$emailActions[0]['options']);

						$constants 				= 	array();
						foreach($cons as $key => $val){
							$constants[] 		= 	'{'.$val.'}';
						}

						$subject 				= 	$emailTemplates[0]['subject'];
						$rep_Array 				= 	array($full_name,$route_url,$click_link);  
						$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
						if(empty($request->input('provider_id'))){
							 $mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
							}

				/* $email 			        =	$obj->email;
				$route_url     		    =   WEBSITE_URL.'account-verification/'.$validateString;
				$full_name				= 	$name ; 
				$route_url     		    =   WEBSITE_URL.'user-verificaion/'.$model->validate_string;
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','account_verification')->get()->toArray();
				$emailTemplates			= 	EmailTemplate::where('action','=','account_verification')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
			
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
			
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($name,$route_url,$route_url); 
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail); */
				Session::flash('success',trans("Your account has been registered successfully. We have sent a email to you for set your password."));
				return response()->json(['success' => true, 'page_redirect' => url('/thank-you'),'response' => $response]);
				
			}
		}
	}

	

	public function userVerificaion($validate_string ='')
	{
		//dd($validate_string);
		if($validate_string!="" && $validate_string!=null){
			$userDetail	=	User::where('validate_string',$validate_string)->first();
			if($userDetail->verified == 1) {

				Session::flash('error', 'Your are using wrong link.');
				return Redirect::to('/');

			}else{

				if(!empty($userDetail)){
					return View::make('front.users.generate_password',compact('userDetail'));
				}else{
					Session::flash('error', 'Somthing went wrong. Please again after some time.');
					return Redirect::to('/');
				}
			}

		}else{
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
			return Redirect::to('/reset-password-msg');
		}
	}

	public function generateNewPassword($validate_string=null,Request $request){
		$thisData				=	$request->all();; 
		$request->replace($this->arrayStripTags($thisData));
		$newPassword		=	$request->input('new_password');
		$validate_string	=	$request->input('validate_string');

		$userDetail	=	User::where('validate_string',$validate_string)->first();
		

		if($userDetail->verified == 1) {

			Session::flash('error', 'Your are using wrong link.');
			return response()->json(['success' => false, 'page_redirect' => url('/')]);

		}else{

			$messages = array(
				'new_password.required' 				=> trans('The New Password field is required.'),
				'new_password_confirmation.required' 	=> trans('The confirm password field is required.'),
				'new_password.confirmed' 				=> trans('The confirm password must be match to new password.'),
				'new_password.min' 						=> trans('The password must be at least 8 characters.'),
				'new_password_confirmation.min' 		=> trans('The confirm password must be at least 8 characters.'),
				"new_password.custom_password"			=>	"Password must have combination of numeric, alphabet and special characters.",
			);

			Validator::extend('custom_password', function($attribute, $value, $parameters) {
				if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value) && preg_match('#[\W]#', $value)) {
					return true;
				} else {
					return false;
				}
			});
			$validator = Validator::make(
				$request->all(),
				array(
					'new_password'			=> 'required|min:8|custom_password',
					'new_password_confirmation' => 'required|same:new_password', 

				),$messages
			);
			if ($validator->fails()){	
				$errors = [];

				$msgArr = (array) $validator->messages();

				$msgArr = array_shift($msgArr);

				$count = 0;

				foreach($msgArr as $key=>$val) {
					$errors[$key."_error"] = array_shift($val);

					$count++;
				}

				return response()->json(['success' => false, 'errors' => $errors]);
			}else{
				User::where('validate_string',$validate_string)
				->update(array(
					'password'	=>	Hash::make($newPassword),
					'verified' => 1
				));

				$user	=	User::where('validate_string',$validate_string)->first();
				//	dd($user);
				if($user->verified == 1) {
					Auth::login($user);
					Session::flash('success', 'Password has been set successfully.');
					return response()->json(['success' => true, 'page_redirect' => url('/dashboard')]);

						/* if (Auth::login($user)){
					
							echo 111; die;

							Session::flash('success', 'Thank you for generating new password . Please Update Your Profile.');
						    return response()->json(['success' => true, 'page_redirect' => url('/')]);

						}else{

							echo 222; die;

							Session::flash('erroe', 'There is some problem. Please try after some time.');
						    return response()->json(['success' => false, 'page_redirect' => url('/')]);

						} */
					}else{

						Session::flash('error', 'There is some problem. Please try after some time.');
						return response()->json(['success' => false, 'page_redirect' => url('/')]);
					}

				}
			}
		}//end saveResetPassword()


	// user varified by email account 
		public function accountVerification($validate_string ='')
		{
			if($validate_string!="" && $validate_string!=null){
				$userDetail	=	User::where('validate_string',$validate_string)->first();

				if(!empty($userDetail)){
					$userDetail->verified = 1;
					$userDetail->validate_string = '';
					$userDetail->save();
					Session::flash('success', 'Thank you for registarion');
					return Redirect::to('/');
				}else{
					Session::flash('error', 'Somthing went wrong. Please again after some time.');
					return Redirect::to('/');
				}

			}else{
				Session::flash('error', 'Somthing went wrong. Please again after some time.');
				return Redirect::to('/reset-password-msg');
			}
	}//end // user varified by email account 

	// forgot password
	public function forgotPassword(){
		if(!empty(Auth::user())){
			return Redirect::to('/');
		}
		return View::make('front.users.forgotpassword');
	}//end forgot passowrd

	//send forgot password email
	public function forgotPasswordSend(Request $request){
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'forgot_email'				=> 'required|email',
				),
				array(
					"forgot_email.required"			=>	trans("The email field is required."),
					"forgot_email.email"		    =>	trans("The email must be a valid email address."),
				)
			);

			if ($validator->fails()){
				$errors = [];

				$msgArr = (array) $validator->messages();

				$msgArr = array_shift($msgArr);

				$count = 0;

				foreach($msgArr as $key=>$val) {
					$errors[$key."_error"] = array_shift($val);

					$count++;
				}
				return response()->json(['success' => false, 'errors' => $errors]);
			}else{
				$userDetail = User::where('email', $request->input('forgot_email'))->first();
				if(!empty($userDetail)){
					if($userDetail->is_active == 1 ){
						$forgot_password_validate_string	= 	md5($userDetail->email.time().time());
						
						User::where('email',$userDetail->email)->update(array('forgot_password_validate_string'=>$forgot_password_validate_string));

						$settingsEmail 		=  Config::get('Site.email');
						$email 				=  $userDetail->email;
						if($userDetail->name !=''){
							$username			=  $userDetail->name;
							$full_name			=  $userDetail->name;

						}else{

							$username			= 'User';
							$full_name			=  'User';
						}

						$route_url      	=  URL::to('/reset-password/'.$forgot_password_validate_string);
						$varify_link   		=   $route_url;

						$emailActions		=	EmailAction::where('action','=','forgot_password')->get()->toArray();
						$emailTemplates		=	EmailTemplate::where('action','=','forgot_password')->get(array('name','subject','action','body'))->toArray();
						$cons = explode(',',$emailActions[0]['options']);
						$constants = array();

						foreach($cons as $key=>$val){
							$constants[] = '{'.$val.'}';
						}
						$subject 			=  $emailTemplates[0]['subject'];
						$rep_Array 			= array($full_name,$varify_link,$route_url); 
						$messageBody		=  str_replace($constants, $rep_Array, $emailTemplates[0]['body']);

						$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
						Session::flash('success', trans('An email has been sent to your inbox. To reset your password please follow the steps mentioned in the email.'));
						return response()->json(['success' => true, 'page_redirect' => '/']);
					}else{
						Session::flash('error', 'Your account has been temporarily disabled. Please contact administrator to unlock.');	
						return response()->json(['success' => true, 'page_redirect' => 'forgot-password']);
					}

				}else{
					Session::flash('error', 'email does not exists');

					return response()->json(['success' => true, 'page_redirect' => 'forgot-password']);
				}  
			}

		}
	}//end send forgot password email

	
	
	/** 
	* Function use for reset passowrd
	* @param null
	* 
	* @return void
	*/	
	public function resetPassword($validate_string ='' ){
		if($validate_string!="" && $validate_string!=null){
			$userDetail	=	User::where('forgot_password_validate_string',$validate_string)->first();
			if(!empty($userDetail)){
				return View::make('front.users.front_reset_password',compact('validate_string'));
			}else{
				return Redirect::to('/')->with('error', trans('Sorry, you are using wrong link.'));
			}
		}else{
			return Redirect::to('/')->with('error', trans('Sorry, you are using wrong link.'));
		}
	}//end resetPassword()

	/** 
	* Function use for save password
	*
	* @param null
	* 
	* @return void
	*/
	public function saveResetPassword($validate_string=null,Request $request){
		$thisData				=	$request->all(); 
		$request->replace($this->arrayStripTags($thisData));
		$newPassword		=	$request->input('new_password');
		$validate_string	=	$request->input('validate_string');

		$messages = array(
			'new_password.required' 				=> trans('The New Password field is required.'),
			'new_password_confirmation.required' 	=> trans('The confirm password field is required.'),
			'new_password.confirmed' 				=> trans('The confirm password must be match to new password.'),
			'new_password.min' 						=> trans('The password must be at least 8 characters.'),
			'new_password_confirmation.min' 		=> trans('The confirm password must be at least 8 characters.'),
			"new_password.custom_password"			=>	"Password must have combination of numeric, alphabet and special characters.",
		);
		
		Validator::extend('custom_password', function($attribute, $value, $parameters) {
			if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value) && preg_match('#[\W]#', $value)) {
				return true;
			} else {
				return false;
			}
		});
		$validator = Validator::make(
			$request->all(),
			array(
				'new_password'			=> 'required|min:8|custom_password',
				'new_password_confirmation' => 'required|same:new_password', 

			),$messages
		);
		if ($validator->fails()){	
			$errors = [];

			$msgArr = (array) $validator->messages();

			$msgArr = array_shift($msgArr);

			$count = 0;

			foreach($msgArr as $key=>$val) {
				$errors[$key."_error"] = array_shift($val);
				$count++;
			}
			return response()->json(['success' => false, 'errors' => $errors]);
		}else{
			$userInfo = User::where('forgot_password_validate_string',$validate_string)->first();	
			User::where('forgot_password_validate_string',$validate_string)
			->update(array(
				'password'							=>	Hash::make($newPassword),
				'forgot_password_validate_string'	=>	''
			));
			$settingsEmail 		= Config::get('Site.email');			
			$action				= "reset_password";
			
			$emailActions		=	EmailAction::where('action','=','reset_password')->get()->toArray();
			$emailTemplates		=	EmailTemplate::where('action','=','reset_password')->get(array('name','subject','action','body'))->toArray();
			$cons 				= 	explode(',',$emailActions[0]['options']);
			$constants 			= 	array();
			foreach($cons as $key=>$val){
				$constants[] = '{'.$val.'}';
			}

			if($userInfo->name !=''){

				$fullname  =$userInfo->name;
			}else{

				$fullname  ='User';
			}
			
			$subject 			=  $emailTemplates[0]['subject'];
			$rep_Array 			= array($fullname); 
			$messageBody		=  str_replace($constants, $rep_Array, $emailTemplates[0]['body']);

			$this->sendMail($userInfo->email,$fullname,$subject,$messageBody,$settingsEmail);
			$user = User::where('email', $userInfo->email)->first();
			//dd($user);
			//if(Auth::login($user) == true ){
			Auth::login($user);
			Session::flash('success', 'Thank you for resetting your password');
			return response()->json(['success' => true, 'page_redirect' =>url('/')]);	


			/* }else{

				Session::flash('success', 'Thank you for resetting your password. Please login to access your account.');

				return response()->json(['success' => true, 'page_redirect' => url('/login')]);	
			} */

			
			//Session::flash('success', trans('Thank you for resetting your password. Please login to access your account.')); 

		}
	}//end saveResetPassword()

	public function resetPasswordMsg(){
		$msg		=	(!empty(Session::get("show_msg"))) ? Session::get("show_msg") : "Somthing went wrong. Please again after some time.";
		return View::make('admin.login.front_reset_password_msg',compact('msg'));
	}

	public function newslettersend(Request $request){

		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){

			$validator = Validator::make(
				$request->all(),
				array(
					'email' 				=> 'required'

				   ),array(
					"email.required"				=>	trans("The email field is required."),
					"email.email"		    		=>	trans("The email is not valid email address."),
					"email.unique"			    =>	trans("The email must be unique."),
				)
			);

			if ($validator->fails()){	
				$errors = [];

				$msgArr = (array) $validator->messages();

				$msgArr = array_shift($msgArr);

				$count = 0;

				foreach($msgArr as $key=>$val) {
					$errors[$key."_error"] = array_shift($val);

					$count++;
				}

				return response()->json(['success' => 3, 'errors' => $errors]);
			}else{

				$accessToken = DB::table('constantcontact')->where('id',1)->value('access_token');
				$params = json_encode(array("email_address"=>array('address'=>$request->input('email'),'permission_to_send'=>"implicit"),"create_source"=>"Account"));
				$defaults = array(
					CURLOPT_URL => 'https://api.cc.email/v3/contacts',
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => $params,
				);
				$ch = curl_init();
				$header = array();
				$header[] = 'Content-type: application/json';
				$header[] = 'Authorization: Bearer '.$accessToken;
				curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt_array($ch, $defaults);
				$rest = curl_exec($ch);
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				/*echo "<pre>";
				print_r($httpcode);die;*/
				if($httpcode == '201'){
					$result = json_decode($rest,true);
					$obj  = new NewsletterContact;
					$obj->contact_id	= $result['contact_id'];
					$obj->save();
					Session::flash('success',trans("Thank you for subscribing to newsletter."));
					return response()->json(['success' => 1, 'page_redirect' => '/']);
				}else{
					return response()->json(['success' => 2, 'errors' =>'Email already Exist.']);
				}
				
				// $encId			    =	md5(time() . $request->input('email'));
				
				// $obj 				=  new NewsLettersubscriber;
				// $obj->email  	    =  $request->input('email');
				// $obj->is_verified	=  1;
				// $obj->status 		=  1;
				// $obj->enc_id 		=  $encId;
				// if(Auth::user()){
				// 	$login_user = Auth::user();
				// 	$obj->user_id	 = $login_user->id;
				// }else{
				// 	$obj->user_id	 = '';
				// }
				// $obj->save();
				// $userId					    =  $obj->id;
				
				// if(!$userId){
				// 	Session::flash('error', trans("Something went wrong.")); 
				// 	return response()->json(['success' => true, 'page_redirect' => '/']);
				// }
				// $settingsEmail 			=	Config::get('Site.email');
				// $full_name				= 	$obj->email; 
				// $email					= 	$obj->email;
				// $click_link   			=   URL::to('/unsubscribe-newsletter/'.$obj->id);
				// $emailActions			= 	EmailAction::where('action','=','subscribe_newsletter')->get()->toArray();
				// $emailTemplates			= 	EmailTemplate::where('action','=','subscribe_newsletter')->get(array('name','subject','action','body'))->toArray();
				// $cons 					= 	explode(',',$emailActions[0]['options']);
				// $constants 				= 	array();
				// foreach($cons as $key => $val){
				// 	$constants[] 		= 	'{'.$val.'}';
				// }
				// $subject 				= 	$emailTemplates[0]['subject'];
				// $rep_Array 				= 	array($click_link); 
				// $messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				// $mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail); 
				
			}
		}
	}
	public function unsubscribeNewsletter($id=0){
		$modal= NewsLettersubscriber::where('id',$id)->first();
		if(empty($modal)){
			return Redirect::to('/');
		}
		NewsLettersubscriber::where('id',$id)->update(['status'=>0]);
		Session::flash('success',trans("You have been unsubscribed successfully."));
		return Redirect::to('/');
	}

	public function quote(Request $request){
		/*echo "<pre>";
		print_r($request->all());die;*/
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if($request->type == 1 || $request->type == ''){	
			if(!empty($formData)){	
				$validator 					=	Validator::make(
					$request->all(),
					array(
						'children_value'		    => 'required',
						'week_value'			    => 'required',	
					),	
					array(
						"children_value.required"			=>	trans("The children field is required."),
						"week_value.required"			=>	trans("The weeks field is required."),
					)
				);
				if ($validator->fails()){
					$errors = [];

					$msgArr = (array) $validator->messages();

					$msgArr = array_shift($msgArr);

					$count = 0;

					foreach($msgArr as $key=>$val) {
						$errors[$key."_error"] = array_shift($val);

						$count++;
					}
					return response()->json(['success' => false, 'errors' => $errors]);
				}else{ 
					$children 			= $request->children_value;
					$totalPercentage	=  20 * ($request->children_value - 1);
					$weeks 				= $request->week_value;
					$location 			= $this->getGeoLocation();
					$counntry 			= !empty($location['country_name']) ? $location['country_name']:'';
					$data = array();
					
					//$counntry = 'Canada';
					if($counntry == 'Canada' || $counntry == 'canada'){
						$price = Config::get('Site.estimation_price_canada');
						/*if($totalPercentage > 0){
							$totalEstimation=$totalEstimation + ($totalEstimation*($totalPercentage/100));
						}*/

						for ($i=1; $i <= $request->children_value - 1; $i++) { 
				              $estimation 	=  $price * (20/100);
				              $price 		= $price + $estimation;	   
						}
						//$totalEstimation =  $price * $weeks;
						$totalEstimation = number_format($price * $weeks, 2);
						$currencyCode = Config::get('Site.CanadacurrencyCode');
						$data = array('price' => $currencyCode.$totalEstimation, 'country' => $counntry, 'currencyCode' => $currencyCode , 'children'=>$children , 'weeks'=>$weeks );
					}else{
						$price =  Config::get('Site.estimation_price_usa');
						/*$totalEstimation =  $price * $weeks;
						if($totalPercentage > 0){
							$totalEstimation=$totalEstimation + ($totalEstimation*($totalPercentage/100));
						}*/
						for ($i=1; $i <= $request->children_value - 1; $i++) { 
				              $estimation 	=  $price * (20/100);
				              $price 		= $price + $estimation;	   
						}
						$totalEstimation = number_format($price * $weeks, 2);
						$currencyCode = Config::get('Site.currencyCode');
						$data = array('price' => $currencyCode.$totalEstimation, 'country' => $counntry, 'currencyCode' => $currencyCode , 'children'=>$children , 'weeks'=>$weeks );
					}

					if(Session::has('quotation')) {
						Session::forget('quotation');
					}

					Session::put('quotation.quotation_data', $data);
					Session::put('quotation.plan_id' ,'');
					return response()->json(['success' => true , 'data' => $data]);
				}
			}
		}elseif($request->type == 2){

			if(!empty($formData)){	
				$validator 					=	Validator::make(
					$request->all(),
					array(
						'children_value'		    => 'required',
						'duration_value'			=> 'required',
						//'date_time'			=> 'required',	
					),	
					array(
						"children.required"			=>	trans("The children field is required."),
						"duration.required"			=>	trans("The duration field is required."),
						//"date_time.required"	    =>	trans("The Date field is required."),
					)
				);
				if ($validator->fails()){
					$errors = [];

					$msgArr = (array) $validator->messages();

					$msgArr = array_shift($msgArr);

					$count = 0;

					foreach($msgArr as $key=>$val) {
						$errors[$key."_error"] = array_shift($val);

						$count++;
					}
					return response()->json(['success' => false, 'errors' => $errors]);
				}else{ 
					$children 			= $request->children_value;
					$totalPercentage	=  20 * ($request->children_value - 1);
					$duration 				= $request->duration_value;
					$location 			= $this->getGeoLocation();
					$counntry 			= !empty($location['country_name']) ? $location['country_name']:'';
					$data = array();
					
					//$counntry = 'Canada';
					if($counntry == 'Canada' || $counntry == 'canada'){
						$price = Config::get('Site.estimation_price_canada');
						/*if($totalPercentage > 0){
							$totalEstimation=$totalEstimation + ($totalEstimation*($totalPercentage/100));
						}*/

						for ($i=1; $i <= $request->children_value - 1; $i++) { 
				              $estimation 	=  $price + 2 * (20/100);
				              $price 		=  $price + 2 + $estimation;	   
						}
						//$totalEstimation =  $price * $weeks;
						$totalEstimation = number_format($price * $duration, 2);
						$currencyCode = Config::get('Site.CanadacurrencyCode');
						$data = array('price' => $currencyCode.$totalEstimation, 'country' => $counntry, 'currencyCode' => $currencyCode , 'children'=>$children , 'duration'=>$duration );
					}else{
						$price =  Config::get('Site.estimation_price_usa');
						/*$totalEstimation =  $price * $weeks;
						if($totalPercentage > 0){
							$totalEstimation=$totalEstimation + ($totalEstimation*($totalPercentage/100));
						}*/
						for ($i=1; $i <= $request->children_value - 1; $i++) { 
				              $estimation 	=  $price +2 * (20/100);
				              $price 		= $price + 2 * $estimation;	   
						}
						$totalEstimation = number_format($price * $duration, 2);
						$currencyCode = Config::get('Site.currencyCode');
						$data = array('price' => $currencyCode.$totalEstimation, 'country' => $counntry, 'currencyCode' => $currencyCode , 'children'=>$children , 'duration'=>$duration, 'type' => 2);
					}

					if(Session::has('quotation')) {
						Session::forget('quotation');
					}

					Session::put('quotation.quotation_data', $data);
					Session::put('quotation.plan_id' ,'');
					return response()->json(['success' => true , 'data' => $data]);
				}
			}

		}
	}



	public function userCheckInfo(Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

	/* 	if(Session::has('quotation')){
			$sessionData = $request->session()->get('quotation');
			dd($sessionData);
		 }
     	 */
		 if(!empty($formData)){
		 	$validator 					=	Validator::make(
		 		$request->all(),
		 		array(
		 			'first_name'		    => 'required',
		 			'email' 				=> 'required|email|unique:users,email',
		 			'phone_number' 			=> 'digits_between:10,15|numeric',
		 		),	
		 		array(
		 			"first_name.required"			=>	trans("The name field is required."),
		 			"email.required"				=>	trans("The email field is required."),
		 			"email.email"					=>	trans("The email is not valid email address."),
		 			"email.unique"					=>	trans("The email must be unique."),
		 			"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
		 			"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
		 		)
		 	);
		 	if ($validator->fails()){
		 		$errors = [];
		 		$msgArr = (array) $validator->messages();
		 		$msgArr = array_shift($msgArr);
		 		$count = 0;
		 		foreach($msgArr as $key=>$val) {
		 			$errors[$key."_error"] = array_shift($val);
		 			$count++;
		 		}
		 		return response()->json(['success' => false, 'errors' => $errors]);
		 	}else{ 
				// $validateString		     	=  md5(time().$request->input('email'));
				// $obj 						=  new User;
				// $obj->first_name 			=  !empty($request->input('first_name')) ? ucfirst($request->input('first_name')) : '';
				// $name 					    =  $obj->first_name;
				// $obj->name 					=  ucfirst($name);
				// $obj->slug 					=  $this->getSlug($name,'name','User');
				// $obj->email 				=  $request->input('email');
				// $obj->phone_number 			=  !empty($request->input('phone_number')) ? $request->input('phone_number'):'';
				// $obj->user_role_id 			=  SUBSCRIBER_ROLE_ID;
				// $obj->validate_string	    =  $validateString;
				// $obj->verified		        =  0;
				// $obj->is_active			    =  1;
				// $obj->is_approved			=  1;
				// $obj->save();
				// $userId					    =  $obj->id;

				// if(!$userId){
				// 	Session::flash('error', trans("Something went wrong.")); 
				// 	return response()->json(['success' => false, 'page_redirect' => url('/')]);

				//  }



				//  $email 			    =	$obj->email;
				//  $full_name				= 	$name ; 
				//  $route_url     		=   WEBSITE_URL.'user-verificaion/'.$validateString;
				//  $click_link   			=   $route_url;
				//  $settingsEmail		    = 	Config::get('Site.to_email');
				//  $emailActions			= 	EmailAction::where('action','=','set_your_password')->get()->toArray();
				//  $emailTemplates	    = 	EmailTemplate::where('action','=','set_your_password')->get(array('name','subject','action','body'))->toArray();
				//  $cons 					= 	explode(',',$emailActions[0]['options']);

				// $constants 				= 	array();
				// foreach($cons as $key => $val){
				// 	$constants[] 		= 	'{'.$val.'}';
				// }

				// $subject 				= 	$emailTemplates[0]['subject'];
				// $rep_Array 				= 	array($full_name,$route_url,$click_link);  
				// $messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				// $mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

				//Session::flash('success',trans("Your account has been registered successfully. We have sent a email to you for set your password."));
		 		$data  = [];
		 		$data['first_name']= !empty($request->input('first_name')) ? ucfirst($request->input('first_name')) : '';
		 		$data['email']= !empty($request->input('email')) ? ($request->input('email')) : '';
		 		$data['phone_number']= !empty($request->input('email')) ? ($request->input('phone_number')) : '';
				//Session::put('userinfo',$data);
		 		return response()->json(['success' => true, 'page_redirect' => url('/'),'first_name'=>$data['first_name'],'email'=>$data['email'],'phone_number'=>$data['phone_number']]);

		 	}
		 }
		}

		public function paymentSettings(Request $request){


			$response	=	array();
			Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			$modelId = !empty(Auth::user()->id) ? Auth::user()->id:0;
			$customerId = User::where('id',$modelId)->value('customer_id');
			$customer = '';
			if(!empty($customerId)){

				

				try {
					$customer = Stripe\Customer::allSources(
						"$customerId",
						['object' => 'card']
					);
					$response["cardlist"]		=	$customer;
				// Use Stripe's library to make requests...
				} catch(\Stripe\Exception\CardException $e) {
					$response["cardlist"]	=	[];
				} catch (\Stripe\Exception\RateLimitException $e) {
					$response["cardlist"]	=	[];
				} catch (\Stripe\Exception\InvalidRequestException $e) {
					$response["cardlist"]	=	[];
				} catch (\Stripe\Exception\AuthenticationException $e) {
					$response["cardlist"]	=	[];
				} catch (\Stripe\Exception\ApiConnectionException $e) {
					$response["cardlist"]	=	[];
				} catch (\Stripe\Exception\ApiErrorException $e) {
					$response["cardlist"]	=	[];
				} catch (Exception $e) {
					$response["cardlist"]	=	[];
				}  
			}
			
			return View::make('front.users.payment_setting',compact('customer'));

		}
		public function weeklyRecurringStatus(Request $request){
			$userID 		= Auth::user()->id;
			if($request['status'] == 'true'){
				User::where('id',$userID)->update(array('weekly_recurring'=>1,'weekly_recurring_date'=> date('Y-m-d')));
			}
			else{
				User::where('id',$userID)->update(array('weekly_recurring'=>0,'weekly_recurring_date'=> date('Y-m-d')));
			}
			Session::flash('success', trans("Weekly Recurring Scheduled changed successfully.")); 
			return response()->json(['success' => true,'message'=>"Weekly Recurring Scheduled changed successfully."]);

		}

		public function addCard(Request $request){

			if(isset($request->card_id)){
				$users 		= Auth::user();
				Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
				$stripe->customers->createSource(
					$users->customer_id
				);
				return response()->json(['success' => true,'message'=>"Card Added successfully."]);
			}else{
				return response()->json(['success' => true,'message'=>"Card not Added"]);
			}
			
		}

		public function updateCard(Request $request){

			if(isset($request->card_id)){
				$users 		= Auth::user();
				Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
				$stripe->customers->updateSource(
					$users->customer_id,
					$request->card_id
				);
				return response()->json(['success' => true,'message'=>"Card updated successfully."]);
			}else{
				return response()->json(['success' => true,'message'=>"Card not updated"]);
			}
			
		}

		public function deleteCard(Request $request){

			if(isset($request->card_id)){
				$users 		= Auth::user();
				Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
				$stripe->customers->deleteSource(
					$users->customer_id,
					$request->card_id
				);
				return response()->json(['success' => true,'message'=>"Card delete successfully."]);
			}else{
				return response()->json(['success' => true,'message'=>"delete card something went to wrong"]);
			}
			
		}
		public function nannyPaymentSettings(Request $request){
			return View::make('front.users.nanny-payment-setting');
		}
		
}// end UsersController class
