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
use File;
use Validator;
use Hash;
use Session;
use Auth;
use Config;
use URL,Mail,DB,Response;
use App\Model\UserMeasurement;
use App\Model\User;
use App\Model\Chat;
use App\Model\UserPlan;
use App\Model\Package;
use App\Model\Block;
use App\Model\WebinarUser;
use App\Model\Webinar;
use App\Model\Contact;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\Setting;
use App\Model\Notification;
use App\Model\Holiday;
use App\Model\SetAvailability;
use App\Model\ScheduleInterview;
use Carbon\Carbon;
use App\Model\Booking;
use App\Model\BookingDetails;
use App\Model\ClientReview;
use App\Model\NannyReview;
use App\Model\userCertificates;

use App\Model\UserPlans;
use App\Model\Earning;
use CustomHelper;
use Stripe, Crypt;
use App\Model\Siterating;


class GlobalUsersController extends BaseController {
  

  function __construct(){
  	date_default_timezone_set('Asia/Kolkata');
  }

	public function inquiry(Request $request){
		$formData						=	$request->all();
		if(!empty($formData)){
			$response	=	$request->input('g-recaptcha-response');
			Validator::extend('unique_validation', function($attribute,$value,$parameters){
				$secret	=	env('GOOGLE_CAPCHA_SECRET');
				$response=$parameters[0];
				$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
				$captcha_success=json_decode($verify);
				if ($captcha_success->success==false){
					return false;
				}else{
					return true;
				}
			});
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'fname'					=> 'required',
					'lname'					=> 'required',
					'email'					=> 'required|email',
					'message'		    	=> 'required',
					'g-recaptcha-response' 	=> 	"required|unique_validation:$response"
				),
				array(
					"fname.required"			=>	trans("The first name field is required."),
					"lname.required"			=>	trans("The last name field is required."),
					"email.required"			=>	trans("The email field is required."),
					"email.email"				=>	trans("The email must be a valid email address."),
					"g-recaptcha-response.required"				=>	trans("The recaptcha field is required."),
					"g-recaptcha-response.unique_validation"	=>	trans("The recaptcha is unique field is required."),
				)
			);
			$password 					= 	$request->input('password');
			
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
				$obj 									=  new Contact;				
				$obj->name 								=  $request->input('fname').' '.$request->input('lname');
				$obj->email 							=  $request->input('email');
				$obj->message 							=  $request->input('message');
				$obj->save();
				if ($obj->save()) {
					$setting 			=  Setting::get();
					$adminEmail 		=   $setting[11]->value;  
					$full_name          = 	$obj->name;
					$email		        =	$obj->email;
					$message		    =	$obj->message;
					$settingsEmail 		= 	Config::get('Site.email');
					//$route_url      	=	WEBSITE_URL.'account-verification/'.$validate_string;
					$emailActions		= 	EmailAction::where('action','=','contact_inquiry')->get()->toArray();
					$emailTemplates		= 	EmailTemplate::where('action','=','contact_inquiry')->get(array('name','subject','action','body'))->toArray();
					$cons 				= 	explode(',',$emailActions[0]['options']);
					$constants 			= 	array();
					foreach($cons as $key => $val){
						$constants[] = '{'.$val.'}';
					}
					$subject 		= 	'Contact Inquiry'; 
					$rep_Array 		= 	array($full_name, $email, $message); 

					$messageBody	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']); 
					$mail			= 	$this->sendMail($adminEmail,$full_name,$subject,$messageBody,$settingsEmail);

					Session::flash('success', 'Your Contact Inquiry has been send successfully and we will as contact as well as soon');
					return response()->json(['success' => true, 'page_redirect' => url('/contact-us')]);
				} else {
					Session::flash('error', 'The Contact Inquiry data is invalid. Please enter again!');

					return response()->json(['success' => true]);
				}
			}
		}
	}


	public function profile()
	{

		if(Auth::user()){

			if(Auth::user()->user_role_id == NANNY_ROLE_ID){
				$user_data	 = Auth::user(); 
				return View::make('front.dashboard.nanny_profile',compact('user_data'));
			}
			elseif(Auth::user()->user_role_id == SUBSCRIBER_ROLE_ID){
				$user_data	 = Auth::user(); 
				return View::make('front.dashboard.customer_profile',compact('user_data'));
			}else{
				return Redirect::to('/');
			}
		}else{
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
			return Redirect::to('/');
		}
	}

	public function profileUpdate(Request $request){

		if(Auth::user()){

			if(Auth::user()->user_role_id == NANNY_ROLE_ID){

				$formData	=	$request->all();

				if(!empty($formData)){
					Validator::extend('custom_other_certificate', function($attribute, $value, $parameters) {
						$type	=	OTHER_CERTIFICATES_EXTENSION;
						foreach($value as $val){
							if(in_array($val->getClientOriginalExtension(),$type)){
								return true;
							}else{
								return false;
							}
						}
					});

					$validator 					=	Validator::make(
						$request->all(),
						array(
							'name'	    	=> 'required',
							'postcode'	    => 'required|numeric',
							'age'	        => 'required|numeric',
							'experience'	=> 'required|numeric',
							"email" 			=> "required|email|unique:users,email,".Auth::user()->id, 
							'phone_number'		=> 'required|numeric',
							'city'	    	    => 'required',
							'state'	    	    => 'required',
							'description'	    => 'required',
							'photo_id'				=> 'mimes:'.IMAGE_EXTENSION,
							'resume'				=> 'mimes:'.IMAGE_EXTENSION_DOCUMENTS,
							'cpr_certificate'		=> 'mimes:'.CAREER_FORM_DOCUMENTS,
							'other_certificates'	=> 'mimes:'.CAREER_FORM_DOCUMENTS,
							'nanny_price'	=> 'required|numeric',
							'identification_type'	=> 'required',
							'identification_file'	=> 'mimes:'.IMAGE_EXTENSION_DOCUMENTS,

						),
						array(
							"name.required"				=>	trans("The name field is required."),
							"age.required"				=>	trans("The age field is required."),
							"age.numeric"		        =>	trans("The age must be numeric."),
							"experience.required"		=>	trans("The experience field is required."),
							"experience.numeric"		=>	trans("The experience must be numeric."),
							"postcode.required"			=>	trans("The postcode field is required."),
							"postcode.numeric"		    =>	trans("The age must be numeric."),
							"description.required"	    =>	trans("The description field is required."),
							"city.required"				=>	trans("The city field is required."),
							"state.required"			=>	trans("The state field is required."),
							"email.required"			=>	trans("The email field is required."),
							"email.email"				=>	trans("The email must be a valid email address."),
							"email.unique"				=>	trans("The email has already been taken."),
							"phone_number.required"		=>	trans("The phone number field is required."),
							"phone_number.numeric"		=>	trans("The phone number must be numeric."),
							"photo_id.mimes"		    =>  trans("The photo id must be in: 'jpeg, jpg, png, gif, bmp formats'"),
							"resume.mimes"				=>	trans("The resume must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
							"cpr_certificate.mimes"		=>	trans("The cpr certificate must be in: 'pdf, docx, doc formats'"),
							"other_certificates.custom_other_certificate"		=>	trans("The other certificates must be in: 'pdf, docx, doc formats'"),
							"nanny_price.required"		=>	trans("The fee charge field is required."),
							"nanny_price.numeric"		=>	trans("The fee charge must be numeric."),
							"identification_file.mimes"					=>	trans("The uploaded file must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
							"identification_file.required"					=>	trans("This field is required."),
							"identification_type.required"					=>	trans("The Identification Type field is required."),

						)
					);
					if($validator->fails()){
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
						$obj 						=  User::find(Auth::user()->id);			
						$obj->name 			        =  ucfirst($request->input('name'));
						$obj->email 			    =  $request->input('email');
						$obj->age 				    =  $request->input('age');
						$obj->experience 		    =  $request->input('experience');
						$obj->city 				    =  $request->input('city');
						$obj->state 				=  $request->input('state');
						$obj->phone_number 			=  $request->input('phone_number');
						$obj->postcode			    =  $request->input('postcode');
						$obj->description		    =  $request->input('description');
						$obj->nanny_price		    =  $request->input('nanny_price');
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
						$obj->save();

						if ($request->hasFile('other_certificates')) {
							$model				=   new userCertificates;
							$model->user_id		=   Auth::user()->id;
							$extension  		=	$request->file('other_certificates')->getClientOriginalExtension();
							$fileName			=	time().'-other-certificates.'.$extension;
							$folderName  		=	strtoupper(date('M'). date('Y'))."/";
							$folderPath			=	OTHER_CERTIFICATES_DOCUMENT_ROOT_PATH.$folderName;
							if (!File::exists($folderPath)) {
								File::makeDirectory($folderPath, $mode = 0777, true);
							}
							if ($request->file('other_certificates')->move($folderPath, $fileName)) {
								$model->other_certificates	=	$folderName.$fileName;
							}
							$model->save();
						}		
						Session::flash('success', 'Profile Successfully Updated');
						return response()->json(['success' => true, 'page_redirect' => url('/profile')]);
					}

				}else{
					return response()->json(['success' => true, 'page_redirect' => url('/')]);
				}
			}elseif(Auth::user()->user_role_id == SUBSCRIBER_ROLE_ID){

				$customerformData	=	$request->all();

				if(!empty($customerformData)){

					$validator 					=	Validator::make(
						$request->all(),
						array(
							
							'name'	    	    => 'required',
							'postcode'	        => 'required|numeric',
							'phone_number'		=> 'required|numeric',
							'city'	    	    => 'required',
							'state'	    	    => 'required',
							"email" 			=> "required|email|unique:users,email,".Auth::user()->id, 
							'photo_id'			=> 'mimes:'.IMAGE_EXTENSION,
							
						),
						array(
							"name.required"				=>	trans("The name field is required."),
							"email.required"			=>	trans("The email field is required."),
							"email.email"				=>	trans("The email must be a valid email address."),
							"email.unique"				=>	trans("The email has already been taken."),
							"postcode.required"			=>	trans("The postcode field is required."),
							"postcode.numeric"		    =>	trans("The age must be numeric."),
							"city.required"				=>	trans("The city field is required."),
							"state.required"			=>	trans("The state field is required."),
							"phone_number.required"		=>	trans("The phone number field is required."),
							"phone_number.numeric"		=>	trans("The phone number must be numeric."),
							"photo_id.mimes"		    =>  trans("The photo id must be in: 'jpeg, jpg, png, gif, bmp formats'"),
						)
					);
					if($validator->fails()){
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

						$obj 						=  User::find(Auth::user()->id);			
						$obj->name 			        =  ucfirst($request->input('name'));
						$obj->email 				=  $request->input('email');
						$obj->city 				    =  $request->input('city');
						$obj->state 				=  $request->input('state');
						$obj->phone_number 			=  $request->input('phone_number');
						$obj->postcode			    =  $request->input('postcode');

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
						} 

						$obj->save();
						Session::flash('success', 'Profile Successfully Updated');
						return response()->json(['success' => true, 'page_redirect' => url('/profile')]);

					}

				}else{
					return response()->json(['success' => true, 'page_redirect' => url('/')]);
				}
			}else{

				return response()->json(['success' => true, 'page_redirect' => url('/')]);

			}

		}else{
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
			return Redirect::to('/');
		}	
	}

	public function changePassword(Request $request)
	{

		if(Auth::user()){

			if(Auth::user()->user_role_id == NANNY_ROLE_ID){
				$formData						=	$request->all();
					// dd($formData);
				if(!empty($formData)){

					$messages = array(
						'old_password.required' 				=> trans('The old Password field is required.'),
						'new_password.required' 				=> trans('The new password field is required.'),
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
							'old_password'			    => 'required',
							'new_password'			    => 'required|min:8|custom_password',
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
						$user = User::find(Auth::user()->id);
						$old_password 		= $request->input('old_password');
						$password 			= $request->input('new_password');
						
						if(!Hash::check($old_password, $user->getAuthPassword())){
							Session::flash('error', 'Password does not match');
							return response()->json(['success' => true, 'page_redirect' => url('/profile')]);

						}else{
							$user->password = Hash::make($password);
							$user->save();
							Session::flash('success', 'Password Changed Successfully.');
							return response()->json(['success' => true, 'page_redirect' => url('/profile')]);

						} 
					} 
				} else{
					return response()->json(['success' => true, 'page_redirect' => url('/')]);
				}

			}elseif(Auth::user()->user_role_id == SUBSCRIBER_ROLE_ID){

				$formData						=	$request->all();

				if(!empty($formData)){

					$messages = array(
						'old_password.required' 				=> trans('The old Password field is required.'),
						'new_password.required' 				=> trans('The new password field is required.'),
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
							'old_password'			    => 'required',
							'new_password'			    => 'required|min:8|custom_password',
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
						$user = User::find(Auth::user()->id);
						$old_password 		= $request->input('old_password');
						$password 			= $request->input('new_password');

						if(!Hash::check($old_password, $user->getAuthPassword())){
						//	echo 123; die;
							Session::flash('error', 'Password does not match');
							return response()->json(['success' => true, 'page_redirect' => url('/profile')]);

						}else{
						//	echo 123345; die;
							$user->password = Hash::make($password);
							$user->save();
							Session::flash('success', 'Password Changed Successfully.');
							return response()->json(['success' => true, 'page_redirect' => url('/profile')]);

						} 
					} 
				} else{
					return response()->json(['success' => true, 'page_redirect' => url('/')]);
				}


			}else{
				return response()->json(['success' => true, 'page_redirect' => url('/')]);
			}
		} else{

			return response()->json(['success' => true, 'page_redirect' => url('/')]);
		}

	}


	public function logout()
	{
		
		Auth::guard('web')->logout();
		return redirect('/');
	}

	public function setAvailability(){

		if(!empty(Auth::user())){
			$availabilities =  SetAvailability::where('user_id', Auth::user()->id)->first();
			$holidays = Holiday::where('user_id', Auth::user()->id)->get();
			return View::make('front.pages.set_availability', compact('availabilities', 'holidays'));
		}else{
			Session::flash('error',trans("Please login first"));
			return Redirect::to('/dashboard');
		}
	}

	public function savesetAvailability(Request $request)
	{
		if(!empty(Auth::user())){
			$setArray = array();
			$user_id = Auth::user()->id;
			if(!empty($request)){
				$checkExist = SetAvailability::where('user_id',$user_id)->first();
				if(!empty($checkExist)){
				  $checkExist->monday_from_time = $request->input('monday_from_time');
				  $checkExist->monday_to_time = $request->input('monday_to_time');
				  $checkExist->tuesday_form_time = $request->input('tuesday_form_time');
				  $checkExist->tuesday_to_time = $request->input('tuesday_to_time');
				  $checkExist->wednesday_form_time = $request->input('wednesday_form_time');
				  $checkExist->wednesday_to_time = $request->input('wednesday_to_time');
				  $checkExist->thursday_form_time = $request->input('thursday_form_time');
				  $checkExist->thursday_to_time = $request->input('thursday_to_time');
				  $checkExist->friday_form_time = $request->input('friday_form_time');
				  $checkExist->friday_to_time = $request->input('friday_to_time');
				  $checkExist->saturday_form_time = $request->input('saturday_form_time');
				  $checkExist->saturday_to_time = $request->input('saturday_to_time');
				  $checkExist->sunday_form_time = $request->input('sunday_form_time');
				  $checkExist->sunday_to_time = $request->input('sunday_to_time');
				  $checkExist->save();
				  Session::flash('success',trans("Availabilities successfully updated."));
					return Redirect::back();
				}else{
				  $obj = new SetAvailability();
				  $obj->user_id = Auth::user()->id;
				  $obj->monday_from_time = $request->input('monday_from_time');
				  $obj->monday_to_time = $request->input('monday_to_time');
				  $obj->tuesday_form_time = $request->input('tuesday_form_time');
				  $obj->tuesday_to_time = $request->input('tuesday_to_time');
				  $obj->wednesday_form_time = $request->input('wednesday_form_time');
				  $obj->wednesday_to_time = $request->input('wednesday_to_time');
				  $obj->thursday_form_time = $request->input('thursday_form_time');
				  $obj->thursday_to_time = $request->input('thursday_to_time');
				  $obj->friday_form_time = $request->input('friday_form_time');
				  $obj->friday_to_time = $request->input('friday_to_time');
				  $obj->saturday_form_time = $request->input('saturday_form_time');
				  $obj->saturday_to_time = $request->input('saturday_to_time');
				  $obj->sunday_form_time = $request->input('sunday_form_time');
				  $obj->sunday_to_time = $request->input('sunday_to_time');
				  $obj->save();
				  Session::flash('success',trans("Availabilities successfully Added."));
					return Redirect::back();
				}
				
			}else{
				Session::flash('error',trans("Please Select Any One field"));
				return Redirect::back();
			}
			

		}else{

			Session::flash('error',trans("Please login first"));
			return Redirect::to('/login');
		}

	}

	public function getcurentLocation(Request $request)
	{
		$default = '';
		
		$location = $this->getGeoLocation();
		if(isset($location['zip_code'])){
			
			return response()->json(['success' => true, 'mesg'=>'Location Found' ,  'data' => $location['zip_code']]);

		}else{

			return response()->json(['success' => true, 'data' => $default,  'mesg' =>'Location Found']);
		}


	}
	
	public function add_holiday($date){

		if(Auth::user()){
			if(empty($date)){
				Session::flash('error',trans("sothing went to wrong"));
				return Redirect::back();
			}
			$checHoliday = Holiday::where('holiday_date', date('Y-m-d', strtotime($date)))->where('user_id', Auth::user()->id)->first();
			if(!empty($checHoliday)){
				Session::flash('error',trans("holiday already added"));
				return Redirect::back();
			}
			$obj = new Holiday;
			$obj->user_id 		= Auth::user()->id;
			$obj->holiday_date 	= date('Y-m-d', strtotime($date));
			$obj->user_id = Auth::user()->id;
			$obj->save();
			Session::flash('success',trans("holiday added successfully."));
			return Redirect::back();
		}else{
			Session::flash('error',trans("Please login first"));
			return Redirect::to('/login');
		}
	}

	public function deleteHolidays($id){
		$check = Holiday::where('id', $id)->where('user_id', Auth::user()->id)->first();
		if(!empty($check)){
			Holiday::where('id', $id)->where('user_id', Auth::user()->id)->delete();
			Session::flash('success',trans("holiday delete successfully."));
			return Redirect::back();
		}else{
			Session::flash('error',trans("sothing went to wrong"));
			return Redirect::back();
		}
	}

	public function gettimeSlots(Request $request)
	{

		$request->replace($this->arrayStripTags($request->all()));
		$formData	=	$request->all();
		if(!empty($formData)){
			if(isset($formData['date'])  &&  isset($formData['nanny_id']) ){
				$holidays = Holiday::where('user_id', $formData['nanny_id'])->pluck('holiday_date')->toArray();
				if(in_array($formData['date'], $holidays)){
					return response()->json(['success' => false, 'mesg'=>'Holiday on this date, please choose another date' ,  'data' =>'']);
				}else{
				    $day = strtolower(date("l",strtotime($formData['date'])));
					  $timeslote = SetAvailability::where('user_id', $formData['nanny_id'])->first();
					 $getSlotsByTime = '';
					if(!empty($timeslote)){
						if($day == 'monday'){
							$fromTime = !empty($timeslote->monday_from_time) ? $timeslote->monday_from_time:'';
							$toTime = !empty($timeslote->monday_to_time) ? $timeslote->monday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);

						}elseif($day == 'tuesday'){
							$fromTime = !empty($timeslote->tuesday_form_time) ? $timeslote->tuesday_form_time:'';
							$toTime = !empty($timeslote->tuesday_to_time) ? $timeslote->tuesday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);

						}elseif($day == 'wednesday'){
							$fromTime = !empty($timeslote->wednesday_form_time) ? $timeslote->wednesday_form_time:'';
							$toTime = !empty($timeslote->wednesday_to_time) ? $timeslote->wednesday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);

						}elseif($day == 'thursday'){
							$fromTime = !empty($timeslote->thursday_form_time) ? $timeslote->thursday_form_time:'';
							$toTime = !empty($timeslote->thursday_to_time) ? $timeslote->thursday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);

						}elseif($day == 'friday'){
						  $fromTime = !empty($timeslote->friday_form_time) ? $timeslote->friday_form_time:'';
							$toTime = !empty($timeslote->friday_to_time) ? $timeslote->friday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);
							$day = 'Friday';
						}elseif($day == 'saturday'){
							$fromTime = !empty($timeslote->saturday_form_time) ? $timeslote->saturday_form_time:'';
							$toTime = !empty($timeslote->saturday_to_time) ? $timeslote->saturday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);
							//$day = 'Saturday';
							//echo "<pre>";print_r($getSlotsByTime);

						}elseif($day == 'sunday'){
							$fromTime = !empty($timeslote->sunday_form_time) ? $timeslote->sunday_form_time:'';
							$toTime = !empty($timeslote->sunday_to_time) ? $timeslote->sunday_to_time:'';
							$getSlotsByTime =  $this->getSlotsByTime($fromTime, $toTime);
							$day = 'Sunday';

						}
						//echo "<pre>";print_r($getSlotsByTime);die;
						$html1 = '';
						if(!empty($getSlotsByTime)){
              $html1 .= '<div class="col-md-6"><div class="form-group>">
              <select class="form-control time_slot_id" name="time_slot_id" id="time_slot_id"><option value="">Select Time</option>';

							foreach($getSlotsByTime as $kgetSlot=>$vgetSlot)
							{
								$html1 .= '<option value="'.$vgetSlot['start'].'-'.$vgetSlot['end'].'">'.date('h:i a', strtotime($vgetSlot['start'])).'-'.date('h:i a', strtotime($vgetSlot['end'])).'</option>';
							}
							$html1 .= '</select><span id="time_slot_id_error" class="help-inline error"></span>
							</div></div>';
						}
						return response()->json(['success' => true, 'mesg'=>'Available' ,  'data'=> $html1]);
						die;
						

						$slots = array('monday' => $mondaystarTime .' '.$mondayToTime, 'tuesday' => $tuesdaystarTime .' '.$tuesdayToTime, 'wednesday' => $wednesdaystarTime .' '.$wednesdayToTime, 'thursday' => $thursdaystarTime .' '.$thursdayToTime, 'fridayday' => $fridaystarTime .' '.$fridayToTime, 'saturday' => $saturdaystarTime .' '.$saturdayToTime, 'sundayday' => $sundaystarTime .' '.$sundayToTime);

						$slots = array('monday' => array($mondaystarTime,$mondayToTime), 'tuesday' => array($tuesdaystarTime, $tuesdayToTime), 'wednesday' => array($wednesdaystarTime, $wednesdayToTime), 'thursday' => array($thursdaystarTime, $thursdayToTime), 'friday' => array($fridaystarTime,$fridayToTime), 'sunday' =>array($sundaystarTime, $sundayToTime), 'saturday' => array($saturdaystarTime,$saturdayToTime));

						foreach($slots as $key=> $_timeslote){
							if($key == $day){
							   if(!empty($_timeslote)){
							   	// $_timeslote = str_replace(" ","-",$_timeslote);
							   	 if(!empty($_timeslote[0])){
							   	 	$output ="<div class='checkRadio_box'>
							   		<input type='radio' name='time_slot' id='time_slot_id'  value='$_timeslote[0]-$_timeslote[1]'>
									<label for=''>".date('h:i a', strtotime($_timeslote[0])).'-'.date('h:i a', strtotime($_timeslote[1]))."</label></div>";
								}else{
							   		$output = "Not Available";
							   }	
								}
							}
						} 
						$output.="<span id='time_slot_id_error' class='help-inline error'></span>";
						return response()->json(['success' => true, 'mesg'=>'Available' ,  'data'=> $output]);
					}else{	 
						return response()->json(['success' => false, 'mesg'=>'Nanny Not Available,please choose another date' ,  'data' =>'']);
					}
				}
			}else{
				return response()->json(['success' => false, 'mesg'=>'Please select nanny' ,  'data' =>'']);
			}
		}else{
			return response()->json(['success' => false, 'mesg'=>'Please select nanny' ,  'data' =>'']);
		}
		

	} 

	public function scheduleInterviewSubmit(Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		$validator 					=	Validator::make(
			$request->all(),
			array(
				'date'		    => 'required',
				'time_slot_id' 	=> 'required',
			),	
			array(
				"date.required"			=>	trans("The date field is required."),
				"time_slot_id.required"	=>	trans("The time slot field is required."),
				
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
			
			if(empty(Auth::user())){
				$first_name = $request->input('first_name');
				$email = $request->input('email');
				$phone_number = $request->input('phone_number');
				$validateString		     	=  md5(time().$email);
				$obj 						=  new User;
				$obj->first_name 			=  $first_name;
				$name 					    =  $obj->first_name;
				$obj->name 					=  ucfirst($name);
				$obj->slug 					=  $this->getSlug($name,'name','User');
				$obj->email 				=  $email;
				$obj->phone_number 			=  $phone_number;
				$obj->user_role_id 			=  SUBSCRIBER_ROLE_ID;
				$obj->validate_string	    =  $validateString;
				$obj->verified		        =  0;
				$obj->is_active			    =  1;
				$obj->is_approved			=  1;
				$obj->save();
				$userId					=  $obj->id;

				$email 			    	=	$obj->email;
				$full_name				= 	$name ; 
				$route_url     			=   WEBSITE_URL.'user-verificaion/'.$validateString;
				$click_link   			=   $route_url;
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','set_your_password')->get()->toArray();
				$emailTemplates	    = 	EmailTemplate::where('action','=','set_your_password')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_name,$route_url,$click_link);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
				Session::flash('success',trans("Your interview has been scheduled and your account has been registered successfully. We have sent a email to you for set your password."));
			}else{
				$userId					    =  Auth::user()->id;
				$email 						=  Auth::user()->email;
				$name 					    =  Auth::user()->name;
			}

			date_default_timezone_set('Asia/Kolkata');
			$currentTime1 =  date('h:i');
			$currentTime2 =  date('H:i');

			$weekdays1 = array(0=>'12:00-02:00',1=>'02:00-04:00',2=>'04:00-06:00',3=>'06:00-08:00',4=>'08:00-10:00',5=>'10:00-12:00');

			$weekdays2 = array(0=>'12:00-14:00',1=>'14:00-16:00',2=>'16:00-18:00',3=>'18:00-20:00',4=>'20:00-22:00',5=>'22:00-24:00');

			$interviewScheduled  		= ScheduleInterview::leftjoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('schedule_interview.user_id',$userId)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->select("schedule_interview.*" ,"day_availabilities.from_time",'day_availabilities.time_slot')->get();
			$totalInterviewScheduledCount = 0;
			if(!empty($interviewScheduled)){
				foreach ($interviewScheduled as $key => $value) {
					if(in_array($value->time_slot, $weekdays1)){
						//$interviewScheduledCount  		= ScheduleInterview::leftjoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('schedule_interview.user_id',$userId)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->count();
						//$totalInterviewScheduledCount += $interviewScheduledCount;
						$totalInterviewScheduledCount += 1;
					}elseif(in_array($value->time_slot, $weekdays2)){
						//$interviewScheduledCount  		= ScheduleInterview::leftjoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('schedule_interview.user_id',$userId)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->where('day_availabilities.from_time', '>=', $currentTime2)->count();
						//$totalInterviewScheduledCount += $interviewScheduledCount;
						$totalInterviewScheduledCount += 1;
					}
				}
			}
		
			if($totalInterviewScheduledCount == 3 || $totalInterviewScheduledCount > 3){
				$errors = [];
				$errors['date'."_error"] = 'Cannot schedule interview with more than 3 nannies';
				return response()->json(['success' => false, 'errors' => $errors]);
			}
			
			$obj 						=  new ScheduleInterview;
			$obj->interview_date 		=  $request->input('date');
			$obj->nanny_id 			    =  $request->input('nanny_id');
			$obj->user_id 			    =  $userId;
			$obj->meeting_day_time 		=  $request->input('time_slot_id');
			$obj->save();

			$interview_id  				= $obj->id;



			if(!empty($interview_id)){
				/*$timeslote 					= SetAvailability::where('id', $obj->day_availabilities_id)->orderBy('created_at','desc')->first();
				//$todatDate = date('Y-m-d', strtotime($request->input('date'))).'T'.$timeslote->from_time.'Z';
				//$todatDate = date('Y-m-d').'T'.'16:45:00Z';
				$encoded_params = json_encode(
					array(
						"topic"=> 'Interview',
						"type"=>"2",
						'start_time'=>$todatDate, 
						"duration"=>(String)60,
						'timezone'=>"UTC",
						'agenda'=> "Interview",
						
					));
				$URL= "https://api.zoom.us/v2/users/me/meetings";
				$ch 			= 	curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array (
					"Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6Iks2bkRkUHhpUTNtM2hLU1ViZWF3M2ciLCJleHAiOjE5MDg2MjM2NDAsImlhdCI6MTYyNDYyMTkzNH0.Lx2ifZQ3uvAuM9qw8M5isCy0dxCdPfXuW0N3n2cfTUc",
					'Content-Type: application/json'
				));
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_params);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				$result = curl_exec($ch);
				$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$result     = json_decode($result, true);
				$outPutArr	=	array('status_code'=>$http_status, 'result'=>$result);
				
				if($outPutArr['status_code'] == 201){
					ScheduleInterview::where('id', $interview_id)->update([ 'meeting_number' => $outPutArr['result']['id'], 'meeting_password' => $outPutArr['result']['password'], 'host_email' => $outPutArr['result']['host_email'], 'join_url' => $outPutArr['result']['join_url'], 'jsan_data' =>  $outPutArr]);
				}
				*/
				$timeSlotData = explode('-', $request->input('time_slot_id'));
				$fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
				$toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):'';
				$interviewTime = $fromTIme.' - '.$toTIme; 
				$date 						= date('d-m-Y',strtotime($request->input('date')));
				$nannyUser 					= 	User::where('id',$request->input('nanny_id'))->first();
				$clientUser                 = User::where('id',$obj->user_id)->first();

				$notification 					= new Notification;
				$notification->sender_id 		= $userId;
				//$notification->message 			= 'You have created schedule with'.' '.$nannyUser->name.' '.'on'.' '.$date.' '.'at'.' '.$timeslote->time_slot;
				$notification->message 			= 'Interview has been scheduled successfully. Please check your email for further details.';
				
				$notification->type 			= 1;
				$notification->interview_id 	= $interview_id;
				$notification->save();

				$nannynotification 				= new Notification;
				$nannynotification->sender_id 	= $request->input('nanny_id');
				//$nannynotification->message 	= 'Interview has been schedule successfully with'.' '.$clientUser->name.' '.'on'.' '.$date.' '.'at'.' '.$timeslote->time_slot;
				$nannynotification->message     = 'Interview has been scheduled successfully. Please check your email for further details.';
				$nannynotification->type 		= 1;
				$nannynotification->interview_id = $interview_id;
				$nannynotification->save();

				//mail to user
				$email 			    	=	$email;
				$full_name				= 	$name; 
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','schedule_interview')->get()->toArray();
				$emailTemplates	    = 	EmailTemplate::where('action','=','schedule_interview')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_name,$nannyUser->name,$interviewTime,$date,$nannyUser->name);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

				//mail to nanny
				
				$email 			    	=	$nannyUser->email;
				$full_nanny_name				= 	$nannyUser->name; 
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','schedule_interview_nanny')->get()->toArray();
				$emailTemplates	    	= 	EmailTemplate::where('action','=','schedule_interview_nanny')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_nanny_name,$full_name,$date,$interviewTime);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);

				Session::flash('success',trans("Interview has been scheduled successfully. Please check your email for further details."));

				return response()->json(['success' => true, 'data' =>  '', 'mesg'=>'Interview has been schedule successfully' ]);
			}
		}
	}


	public function checkCoupenCode(Request $request){

		

		if(!empty($request->coupen_code)){

			$current_date = date("Y-m-d");
			$coupendata = DB::table('coupon_codes')->select('coupon_code', 'amount','coupon_type', 'id')->where(['coupon_code'=>$request->coupen_code , 'is_active' => 1 , 'is_deleted'=>0])->where('start_date','<=' , $current_date)->where('end_date','>=' , $current_date)->first();


			if(!empty($coupendata)){
				    //$coupendata   =  CustomHelper::getCoupenById($request->coupen_code);
				   
					$planinfo = Package::where(['id' => $request->planid])->first();

					$plan_price = !empty($planinfo->price > 0 ) ? $planinfo->price:0;
					$final_price = 0;
					if(!empty($coupendata)){

						if($coupendata->coupon_type =='fixed_amount'){

							if($coupendata->amount> $plan_price ){
								$final_price = 0;
							}else{
								$final_price =  $plan_price - $coupendata->amount ; 
								$final_price =  number_format($final_price, 2) ;
								$final_price =  $final_price ;
							}


						}else{

							$percentAmmount =  $plan_price*$coupendata->amount/100 ; 
							if($percentAmmount >  $plan_price   ){
								$final_price = 0;
							}else{
								$final_price = $plan_price - $plan_price*$coupendata->amount/100 ; 
								$final_price =  number_format($final_price, 2) ;
								$final_price =  $final_price ;
							}

						}

					}else{

						$final_price =  number_format($plan_price,2) ;
						$final_price =  $final_price ;
					}

				if(!empty(Auth::user())){

					$userid =   Auth::user()->id; 
					$model	=	DB::table('user_plans')->where('user_id',$userid)->where('coupon_code_id', $coupendata->id)->first();

					
					if(!empty($model)){

						if($coupendata->applicable_type == 'one_time'){

							return response()->json(['success' => false, 'data' =>  '', 'mesg'=>'Invalid coupen code' ]);

						}else{

							return response()->json(['success' => true, 'data' =>  $coupendata , 'final_price' => $final_price, 'mesg'=>'Coupon code applied' ]);

						}

					}else{

						return response()->json(['success' => true, 'data' =>  $coupendata,'final_price' => $final_price, 'mesg'=>'Coupon code applied' ]);

					}	 

				}else{

					return response()->json(['success' => true, 'data' =>  $coupendata ,'final_price' => $final_price, 'mesg'=>'Coupon code applied' ]);

				}
				
			}else{

				return response()->json(['success' => false, 'data' =>  '', 'mesg'=>'Invalid Coupon code' ]);
			}

		}else{

			return response()->json(['success' => false, 'data' =>  '', 'mesg'=>'Please add Coupon code' ]);
		}
		
	}



	public function nannyInterviewList(Request $request)
	{
		if(!empty(Auth::user())){
			$user_role   = Auth::user()->user_role_id;
			$userId      = Auth::user()->id;
			$inputGet    =	$request->all();
			if($user_role == NANNY_ROLE_ID){
				$DB					=	ScheduleInterview::query();
				$DB->select('schedule_interview.*', 'user.name as user_name')
				->leftjoin('users as user', 'schedule_interview.user_id', '=', 'user.id')
				->where(['schedule_interview.nanny_id'=> $userId , 'schedule_interview.is_deleted'=>0 , 'user.is_deleted'=>0 ]);

				$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'schedule_interview.created_at';
				$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';	
				$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
				$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
				$complete_string		=	$request->query();
				unset($complete_string["sortBy"]);
				unset($complete_string["order"]);
				$query_string			=	http_build_query($complete_string);
				$results->appends($inputGet)->render();

				//echo "<pre>";print_r($results);die;
				
				return View::make('front.dashboard.nanny_schedule_interview', compact('results','sortBy','order','query_string'));


			}else{
				Session::flash('error',trans("Something Went Wrong"));
				return Redirect::to('/');

			}
			

		}else {
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');

		}
	}

	public function clientInterviewList(Request $request){



		if(!empty(Auth::user())){
			$user_role   = Auth::user()->user_role_id;
			$userId      = Auth::user()->id;
			$inputGet    =	$request->all();
			$DB					=	ScheduleInterview::query();
			$DB->select('schedule_interview.*', 'user.name as user_name')->leftjoin('users as user', 'schedule_interview.nanny_id', '=', 'user.id')
			->where(['schedule_interview.user_id'=> $userId , 'schedule_interview.is_deleted'=>0 , 'user.is_deleted'=>0 ]);

			$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'schedule_interview.created_at';
			$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';	
			$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
			$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
			$complete_string		=	$request->query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$results->appends($inputGet)->render();

			/*echo "<pre>";print_r($results);die;*/

			return View::make('front.dashboard.client_schedule_interview', compact('results','sortBy','order','query_string'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
	}

	public function nannyNotificationList(Request $request){
		if(!empty(Auth::user())){
			if($request->ajax()){
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
				return View::make('front.dashboard.notification_data',compact('results'));
			}
			$offset=0;
			$PerPageRecord=4;
			$results=Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
			return View::make('front.dashboard.nanny_notification',compact('results'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}

		
	}

	public function clientNotificationList(Request $request){

		if(!empty(Auth::user())){
			if($request->ajax()){
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
				return View::make('front.dashboard.notification_data',compact('results'));
			}
			$offset=0;

			$PerPageRecord=3;
			$results=Notification::where('sender_id',Auth::user()->id)->orderBy('created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();

			return View::make('front.dashboard.client_notification',compact('results'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}
	public function changeNotificationReadStatus(Request $request){
		if(!empty(Auth::user())){
			if(!empty($request->id)){
				Notification::where('id',$request->id)->update(['read_status'=>1]);
				return response()->json(['success'=>1]);
			}
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function nannyEarningList(){
		if(!empty(Auth::user())){

			$earnings 		= Earning::where('nanny_id',Auth::user()->id)->get();
			$totalEarnings 	= Earning::where('nanny_id',Auth::user()->id)->sum('amount');

			return View::make('front.dashboard.nanny_myearning',compact('earnings','totalEarnings'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function nannyEarningListSearch(Request $request){
		$date = date('Y-m-d',strtotime($request['date']));
		$dateValue = date('d/m/Y',strtotime($request['date']));

		if(!empty(Auth::user())){

			$earnings 		= Earning::where('nanny_id',Auth::user()->id)->whereDate('created_at',$date)->get()->toArray();
			$totalEarnings 	= Earning::where('nanny_id',Auth::user()->id)->whereDate('created_at',$date)->sum('amount');

			return View::make('front.dashboard.nanny_myearning_search',compact('earnings','totalEarnings','date','dateValue'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}
	public function clientEarningList(){
		if(!empty(Auth::user())){
			$earnings 		= Earning::where('user_id',Auth::user()->id)->get()->toArray();
			$totalEarnings 	= Earning::where('user_id',Auth::user()->id)->sum('amount');
			return View::make('front.dashboard.client_myearning',compact('earnings','totalEarnings'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function clientEarningListSearch(Request $request){

		$date = date('Y-m-d',strtotime($request['date']));
		// print_r($date);die;
		$dateValue = date('d/m/Y',strtotime($request['date']));

		if(!empty(Auth::user())){
			$earnings 		= Earning::where('user_id',Auth::user()->id)->whereDate('created_at',$date)->get()->toArray();
			$totalEarnings 	= Earning::where('user_id',Auth::user()->id)->whereDate('created_at',$date)->sum('amount');
			return View::make('front.dashboard.client_myearning_search',compact('earnings','totalEarnings','date','dateValue'));
			
		}else{	
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function getNannyAvaiblity(Request $request)
	{
		$nannyId = $request->nannyId;  
		$availabilities = SetAvailability::where('user_id', $nannyId)->first();
		$html = '';
		if(Auth::user()->user_role_id == 3){
		 	$html = View::make('front.pages.book_avaiblity', compact('availabilities', 'nannyId'))->render();
		 	return response()->json(['data'=>$html]);
		}
		if(Auth::user()->user_role_id == 2){
		 	$html = View::make('front.pages.user_book_nanny_avaiblity', compact('availabilities', 'nannyId'))->render();
		 	return response()->json(['data'=>$html]);
		}

		
	} 

	public function getBookingSlots(Request $request){
		$bookingId = $request->id;
		$booking = DB::table('bookings')->where('id',$bookingId)->first();
		$bookingDetails = DB::table('booking_details')->where('booking_id',$bookingId)->orderBy('booking_date','ASC')->get()->toArray();

		/*echo "<pre>";
		print_r($bookingDetails);die;*/

		$availabilities = SetAvailability::where('user_id', $booking->nanny_id)->get()->toArray();
		$html = View::make('front.pages.booking_details', compact('availabilities', 'bookingId','bookingDetails','booking'))->render();
		return response()->json(['data'=>$html]);
	}

	// public function clientBookingList(Request $request){

	// 	$DB		=	Booking::leftJoin('users','bookings.user_id','users.id')->where('bookings.nanny_id',0)->select('bookings.*','users.name','users.photo_id');


	// 	$searchVariable		=	array(); 
	// 	$inputGet			=	$request->all();
	// 	if ($request->all()) {
	// 		$searchData			=	$request->all();
	// 		unset($searchData['display']);
	// 		unset($searchData['_token']);

	// 		if(isset($searchData['order'])){
	// 			unset($searchData['order']);
	// 		}
	// 		if(isset($searchData['sortBy'])){
	// 			unset($searchData['sortBy']);
	// 		}
	// 		if(isset($searchData['page'])){
	// 			unset($searchData['page']);
	// 		}
	// 		if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
	// 			$dateS = $searchData['date_from'];
	// 			$dateE = $searchData['date_to'];
	// 			$DB->whereBetween('bookings.booking_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
	// 		}elseif(!empty($searchData['date_from'])){
	// 			$dateS = $searchData['date_from'];
	// 			$DB->where('bookings.booking_date','>=' ,[$dateS." 00:00:00"]); 
	// 		}elseif(!empty($searchData['date_to'])){
	// 			$dateE = $searchData['date_to'];
	// 			$DB->where('bookings.booking_date','<=' ,[$dateE." 00:00:00"]);
	// 		}

	// 		foreach($searchData as $fieldName => $fieldValue){
	// 			if($fieldValue != ""){
	// 				if($fieldName == "name"){
	// 					$DB->where("users.name",'like','%'.$fieldValue.'%');
	// 				}
	// 				if($fieldName == "status"){
	// 					$DB->where("bookings.status",$fieldValue);
	// 				}
	// 			}
	// 			$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
	// 		}
	// 	}
	
	// 	$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'bookings.booking_date';
	// 	$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';

	// 	if($request->ajax() && $request->method()=='POST'){
	// 		// print_r($request->all());die;
	// 		$inputOffset = $request->input('offset');
	// 		$PerPageRecord=4;
	// 		$offset=$inputOffset*$PerPageRecord;
	// 		$results=	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();

	// 		return View::make('front.dashboard.booking_data',compact('results'));
	// 		die;
	// 	}

	// 	$offset=0;
	// 	$PerPageRecord=4;
	// 	$results = 	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();
	// 				//echo '<pre>'; print_r($results);die;
	// 	$complete_string		=	$request->query();
	// 	unset($complete_string["sortBy"]);
	// 	unset($complete_string["order"]);
	// 	$query_string			=	http_build_query($complete_string);
	// 	// $results->appends($inputGet)->render();

	// 	return View::make('front.dashboard.client_mybooking',compact('results'));
	// }

	public function clientBookingList(Request $request){

		if(!empty(Auth::user())){
			$DB		=	Booking::leftJoin('users','bookings.nanny_id','users.id')->select('bookings.*','users.name', 'users.photo_id')->where('bookings.user_id',Auth::user()->id);

			$searchVariable		=	array(); 
			$inputGet			=	$request->all();
			if ($request->all()) {
				$searchData			=	$request->all();
				unset($searchData['display']);
				unset($searchData['_token']);

				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
					$dateS = $searchData['date_from'];
					$dateE = $searchData['date_to'];
					$DB->whereDate('bookings.start_date','>=', $dateS)->where('bookings.end_date','<=',$dateE); 											
				}elseif(!empty($searchData['date_from'])){
					$dateS = $searchData['date_from'];
					$DB->whereDate('bookings.start_date','>=' ,$dateS); 
				}elseif(!empty($searchData['date_to'])){
					$dateE = $searchData['date_to'];
					$DB->whereDate('bookings.end_date','<=' ,$dateE);
				}

				foreach($searchData as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == "name"){
							$DB->where("users.name",'like','%'.$fieldValue.'%');
						}
						if($fieldName == "status"){
							$DB->where("bookings.status",$fieldValue);
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}

			$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'bookings.booking_date';
			$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
			
			
			if($request->ajax() && $request->method()=='POST'){
				$type='client';
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();
				
				return View::make('front.dashboard.booking_data',compact('results','type'));
				
			}

			$offset=0;
			$PerPageRecord=4;
			$results = 	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();

			return View::make('front.dashboard.client_mybooking',compact('results'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function nannyBookingList(Request $request){

		if(!empty(Auth::user())){
			$DB		=	Booking::leftJoin('users','bookings.user_id','users.id')->select('bookings.*','users.name','users.photo_id')->where('bookings.nanny_id',  Auth::user()->id);



			$searchVariable		=	array(); 
			$inputGet			=	$request->all();
			if ($request->all()) {
				$searchData			=	$request->all();
				unset($searchData['display']);
				unset($searchData['_token']);

				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
					$dateS = $searchData['date_from'];
					$dateE = $searchData['date_to'];
					$DB->whereDate('bookings.start_date','>=', $dateS)->where('bookings.end_date','<=',$dateE); 											
				}elseif(!empty($searchData['date_from'])){
					$dateS = $searchData['date_from'];
					$DB->whereDate('bookings.start_date','>=' ,$dateS); 
				}elseif(!empty($searchData['date_to'])){
					$dateE = $searchData['date_to'];
					$DB->whereDate('bookings.end_date','<=' ,$dateE);
				}

				foreach($searchData as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == "name"){
							$DB->where("users.name",'like','%'.$fieldValue.'%');
						}
						if($fieldName == "status"){
							$DB->where("bookings.status",$fieldValue);
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}

			$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'bookings.booking_date';
			$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
			
			
			if($request->ajax() && $request->method()=='POST'){
				$type= 'nanny';
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();

				$html = View::make('front.dashboard.booking_data',compact('results','type'))->render();
				return response()->json(['data'=>$html]);


			}

			$offset=0;
			$PerPageRecord=4;
			$results = 	$DB	->where('bookings.is_deleted',0)->orderBy($sortBy, $order)->offset($offset)->limit($PerPageRecord)->get();

			return View::make('front.dashboard.nanny_mybooking',compact('results'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	

	public function nannyBooking(Request $request)
	{	
		/*echo "<pre>";
		print_r($request->all());die;*/

		$checkPlan = UserPlans::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
		if(!empty($checkPlan)){
			if($checkPlan->plan_end_date <= date('Y-m-d') ||  $checkPlan->status == 0){
				Session::flash('success',trans("You don't have a plan for booking, please purchase new plan"));
			    return response()->json(['success'=>0,'message'=>"You don't have a plan for booking, please purchase new plan"]);
			}
			
		} 
	    
		$startDate 	= !empty($request->start_date)?date('Y-m-d',strtotime($request->start_date)):'';
		$endDate 		= !empty($request->end_date)?date('Y-m-d',strtotime($request->end_date)):'';


	   
		$obj = new Booking;
		$obj->user_id 							= Auth::user()->id;
		$obj->nanny_id 							= $request->nannay_id;
		$obj->booking_date 					= date('Y-m-d');
		$obj->start_date 						= $startDate;
		$obj->end_date 							= $endDate;
		$obj->set_avaiblity_id 		  = $request->avablity_id;
		$obj->status  = 0;
		$obj->save();
		
		$totalhours = 0;
		$setArray = array();
		if($obj->save()){
			ScheduleInterview::where('id', $request->interview_id)->update(['is_booking' => 1]);
			$bookingId = $obj->id;

			 $start = strtotime($startDate); 
				$end = strtotime($endDate); 
				$range = array();

				$date = strtotime("-1 day", $start);  
				while($date < $end)  { 
				   $date = strtotime("+1 day", $date);
				   $range[] = date('Y-m-d', $date);
				} 
				foreach($range as $rang){

					$sdate = strtotime($rang);
					$getdateDay = date('l', $sdate);
					if($getdateDay == 'Monday'){
						if(!empty($request->monday_from_time)){
							 $fromtiemonday = $request->monday_from_time;
							 $endtimemonday = $request->monday_to_time;
							  
							  $objDetails 												= new BookingDetails;
								$objDetails->booking_id             = $bookingId;
								$objDetails->from_time = !empty($fromtiemonday) ? $fromtiemonday: '';
								$objDetails->to_time = !empty($endtimemonday) ? $endtimemonday: '';
								$objDetails->day = 'Monday';
								$objDetails->booking_date = $rang;
								$objDetails->save();
						}
					}
					
				if($getdateDay == 'Tuesday'){
					if(!empty($request->tuesday_from_time)){
						 
						 $fromtietuesday = $request->tuesday_from_time;
						 $endtuesday = $request->tuesday_to_time;

						  $objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtietuesday) ? $fromtietuesday: '';
							$objDetails->to_time = !empty($endtuesday) ? $endtuesday: '';
							$objDetails->day = 'Tuesday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}

				if($getdateDay == 'Wednesday'){

					if(!empty($request->wednesday_from_time)){
						 
							 $fromtiemewednesday = $request->wednesday_from_time;
							 $endtimewednesday = $request->wednesday_to_time;
						  $objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtiemewednesday) ? $fromtiemewednesday: '';
							$objDetails->to_time = !empty($endtimewednesday) ? $endtimewednesday: '';
							$objDetails->day = 'Wednesday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}

				if($getdateDay == 'Thursday'){

					if(!empty($request->thursday_from_time)){
						 
						  $fromtiethursday = $request->thursday_from_time;
							$endtimethursday = $request->thursday_to_time;

						  $objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtiethursday) ? $fromtiethursday: '';
							$objDetails->to_time = !empty($endtimethursday) ? $endtimethursday: '';
							$objDetails->day = 'Friday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}
				if($getdateDay == 'Friday'){	
					if(!empty($request->friday_from_time)){
						 
						  $fromtiefriday = $request->friday_from_time;
							$endtimefriday = $request->friday_to_time;

						  $objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtiefriday) ? $fromtiefriday: '';
							$objDetails->to_time = !empty($endtimefriday) ? $endtimefriday: '';
							$objDetails->day = 'Saturday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}
				if($getdateDay == 'Saturday'){
					if(!empty($request->saturday_from_time)){
						  $fromtimesaturdayData = $request->saturday_from_time;
							$endtimesaturday = $request->saturday_to_time;

						  $objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtimesaturdayData) ? $fromtimesaturdayData: '';
							$objDetails->to_time = !empty($endtimesaturday) ? $endtimesaturday: '';
							$objDetails->day = 'Sunday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}
				if($getdateDay == 'Sunday'){
					if(!empty($request->sunday_from_time)){
						 
						  $fromtimesunday = $request->sunday_from_time;
							$endtimesunday = $request->sunday_to_time;

							$objDetails 												= new BookingDetails;
							$objDetails->booking_id             = $bookingId;
							$objDetails->from_time = !empty($fromtimesunday) ? $fromtimesunday: '';
							$objDetails->to_time = !empty($endtimesunday) ? $endtimesunday: '';
							$objDetails->day = 'Sunday';
							$objDetails->booking_date = $rang;
							$objDetails->save();
					}
				}
			}

			if(!empty($request->nannay_id)){
				$userData                   =  CustomHelper::getUserDataById($request->nannay_id);

				if(!empty($userData)){

					$settingsEmail		    = 	Config::get('Site.to_email');
					// mail send to Nannay 
					$email 			    	=	$userData->email;
					$full_name				= 	$userData->name; 
					
					$emailActions			= 	EmailAction::where('action','=','admin_nanny_booking_email')->get()->toArray();
					$emailTemplates	    = 	EmailTemplate::where('action','=','admin_nanny_booking_email')->get(array('name','subject','action','body'))->toArray();
					
					$cons 					= 	explode(',',$emailActions[0]['options']);

					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($full_name, Auth::user()->name); 

					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mailnanny				= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
					//
					$adminemail 			= Config::get('Site.admin_nanny_booking_email');
					if(!empty($adminemail)){
						$mailadmin			    = 	$this->sendMail($adminemail,'Admin',$subject,$messageBody,$settingsEmail);
					}
					// mail send to Nannay 


				  //Mail send to User Booking
					$useremail 			    =	Auth::user()->email;
					$user_full_name			= 	Auth::user()->name; 
					$nannyName              =   $userData->name;
					$emailActions1			= 	EmailAction::where('action','=','user_booking_request')->get()->toArray();
					$emailTemplates1	    	= 	EmailTemplate::where('action','=','user_booking_request')->get(array('name','subject','action','body'))->toArray();
					$cons1 					= 	explode(',',$emailActions1[0]['options']);
					$constants1 			= 	array();
					foreach($cons1 as $key => $val){
						$constants1[] 		= 	'{'.$val.'}';
					}
					$subject1 				= 	$emailTemplates1[0]['subject'];
					$nannyName;
					$rep_Array1 			= 	array($user_full_name,$nannyName);
					$messageBody1			= 	str_replace($constants1, $rep_Array1, $emailTemplates1[0]['body']);
					$mailuser			    = 	$this->sendMail($useremail,$user_full_name,$subject1,$messageBody1,$settingsEmail);

				   //Mail send to User
					$checkUserBooking = Booking::where('user_id', Auth::user()->id)->first();
					if(empty($checkUserBooking)){
					 /*Mail send to User first Booking  Appreciation*/
						$useremail 			    =	Auth::user()->email;
						$user_full_name			= 	Auth::user()->name; 
						$emailActions2			= 	EmailAction::where('action','=','appreciation')->get()->toArray();
						$emailTemplates2	    	= 	EmailTemplate::where('action','=','appreciation')->get(array('name','subject','action','body'))->toArray();
						$cons2 					= 	explode(',',$emailActions2[0]['options']);
						$constants1 			= 	array();
						foreach($cons2 as $key => $val){
							$constants1[] 		= 	'{'.$val.'}';
						}
						$subject2 				= 	$emailTemplates2[0]['subject'];
						$rep_Array2 			= 	array($user_full_name);  
						$messageBody2			= 	str_replace($constants1, $rep_Array2, $emailTemplates2[0]['body']);
					    $mailuser			    = 	$this->sendMail($useremail,$user_full_name,$subject2,$messageBody2,$settingsEmail);
					 /*Mail send to User first Booking  Appreciation*/
					}


					


				}

				$notification = new Notification;
				$notification->sender_id = Auth::user()->id;
				$notification->message = 'Request has been sent successfully. Waiting for Approval';
				$notification->type = 1;
				$notification->booking_id = $bookingId;
				$notification->save();

				$nannynotification = new Notification;
				$nannynotification->sender_id = $request->nannay_id;
				$nannynotification->message = 'New Booking is created please accept or reject the booking';
				$nannynotification->type = 1;
				$nannynotification->booking_id = $bookingId;
				$nannynotification->save();
				Session::flash('success',trans("Request has been sent successfully. Waiting for Approval"));
				return response()->json(['success'=>1,'message'=>'Request has been sent successfully. Waiting for Approval']);
			}
			
		}else{
			Session::flash('error',trans("something went to wrong"));
			return response()->json(['success'=>0,'message'=>'something went to wrong']);
		}

		
	}// end 


	public function bookingApproved($id){
		$check = Booking::where('id', $id)->where('nanny_id', Auth::user()->id)->first();
		if(!empty($check)){
			Booking::where('id', $id)->where('nanny_id', Auth::user()->id)->update(['status' => 1]);
			Session::flash('success',trans("Booking successfully Approved."));

			$userData  = CustomHelper::getUserDataById($check->user_id);
			$nannyData  = CustomHelper::getUserDataById($check->nanny_id);

			$usernotification = new Notification;
			$usernotification->sender_id = $check->user_id;
			$usernotification->message = $userData->name.' Your Booking Nanny '.$nannyData->name.' Request has been successfully Approved';
			$usernotification->type = 1;
			$usernotification->booking_id = $id;
			$usernotification->save();
			
			$nannynotification = new Notification;
			$nannynotification->sender_id = $check->nanny_id;
			$nannynotification->message = $userData->name.' Booking has been successfully Approved';
			$nannynotification->type = 1;
			$nannynotification->booking_id = $id;
			$nannynotification->save();

			$setting 			=  Setting::get();
			$adminEmail 		=   $setting[11]->value;  
			$full_name          = 	$userData->name;
			$email		        =	$userData->email;
				//$message		    =	$obj->message;
			$settingsEmail 		= 	Config::get('Site.email');
				//$route_url      	=	WEBSITE_URL.'account-verification/'.$validate_string;
			$emailActions		= 	EmailAction::where('action','=','user_nannay_booking_approved')->get()->toArray();
			$emailTemplates		= 	EmailTemplate::where('action','=','user_nannay_booking_approved')->get(array('name','subject','action','body'))->toArray();
			$cons 				= 	explode(',',$emailActions[0]['options']);
			$constants 			= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject']; 
			$rep_Array 		= 	array($full_name, $nannyData->name); 

			$messageBody	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']); 
			$mail			= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);


			return Redirect::back();
		}else{
			Session::flash('error',trans("sothing went to wrong"));
			return Redirect::back();
		}
	}

	public function bookingRejected(Request $request){
		//print_r($request->all());
		if(empty($request->reject_reason)) {
            return response()->json(['success' => false, 'message' => 'Please enter the reject reason before rejecting the request.']);
        }
       
		$id = $request->id;
		$check = Booking::where('id', $id)->where('nanny_id', Auth::user()->id)->first();
		if(!empty($check)){
			Booking::where('id', $id)->where('nanny_id', Auth::user()->id)->update(['status' => 3, 'reject_reason' => $request->reject_reason]);
			$userData  = CustomHelper::getUserDataById($check->user_id);
			$nannyData  = CustomHelper::getUserDataById($check->nanny_id);
			
			$usernotification = new Notification;
			$usernotification->sender_id = $check->user_id;
			$usernotification->message = $userData->name.' Your Booking Nanny '.$nannyData->name.' Request has been Rejected following reason '.$request->reject_reason;
			$usernotification->type = 1;
			$usernotification->booking_id = $id;
			$usernotification->save();
			
			$nannynotification = new Notification;
			$nannynotification->sender_id = $check->nanny_id;
			$nannynotification->message = $userData->name.' Booking has been Rejected, following reason '.$request->reject_reason;
			$nannynotification->type = 1;
			$nannynotification->booking_id = $id;
			$nannynotification->save();
			

			$setting 			=  Setting::get();
			$adminEmail 		=   $setting[11]->value;  
			$full_name          = 	$userData->name;
			$email		        =	$userData->email;
				//$message		    =	$obj->message;
			$settingsEmail 		= 	Config::get('Site.email');
				//$route_url      	=	WEBSITE_URL.'account-verification/'.$validate_string;
			$emailActions		= 	EmailAction::where('action','=','user_nannay_booking_rejected')->get()->toArray();
			$emailTemplates		= 	EmailTemplate::where('action','=','user_nannay_booking_rejected')->get(array('name','subject','action','body'))->toArray();
			$cons 				= 	explode(',',$emailActions[0]['options']);
			$constants 			= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject']; 
			$rep_Array 		= 	array($full_name, $nannyData->name, $request->reject_reason); 

			$messageBody	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']); 
			$mail			= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
			Session::flash('success',trans("booking rejected successfully."));
			return response()->json(['success' => true]);
		}else{
			Session::flash('error',trans("sothing went to wrong"));
			return Redirect::back();
		}
	}

	public function nannyInboxList(){
		if(!empty(Auth::user())){

			return View::make('front.dashboard.nanny_inbox');
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function clientInboxList(){
		if(!empty(Auth::user())){
			
			/*$bookins =  Booking::leftJoin('users as u', 'u.id', 'bookings.user_id')->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->select('users.*','u.name as username', 'n.name as nanny_name')->where('bookings.user_id', Auth::user()->id)->where('bookings.status',1)->get();*/
			
			$userData = Booking::leftJoin('users as u', 'u.id', 'bookings.user_id')->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->select('u.name as customer', 'n.name as nanny', 'n.photo_id', 'bookings.id', 'bookings.booking_date', 'bookings.status', 'bookings.nanny_id', 'bookings.user_id', 'bookings.created_at','bookings.start_date','bookings.end_date')->where('bookings.status',1)->where('bookings.user_id', Auth::user()->id)->get();

			$chataData = DB::table('chats')->select('chats.*', 'u.name as user_name', 'n.photo_id', 'n.name as nanny_name')->where('n.id', Auth::user()->id)->leftJoin('users as u', 'u.id', 'chats.user_id')->leftJoin('users as n', 'n.id', 'chats.nanny_id')->orderBy('chats.created_at', 'desc')->get();
			
			return View::make('front.dashboard.client_inbox', compact('userData'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function nannyRatingList(Request $request){
		if(!empty(Auth::user())){
			if($request->ajax() && $request->method()=='POST'){
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=	ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->leftJoin('users','client_reviews.user_id','users.id')->select('client_reviews.*','users.name','users.id as data_id')->orderBy('client_reviews.created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
				$html = View::make('front.dashboard.ratings_data',compact('results'))->render();
				return response()->json(['data'=>$html]);
			}
			$offset=0;
			$PerPageRecord=4;
			$results = ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->leftJoin('users','client_reviews.user_id','users.id')->select('client_reviews.*','users.name','users.id as data_id')->orderBy('client_reviews.created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
			$averageRating = ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->avg('rating');
			$totalRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->count();
			$excellentRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->where('rating',5)->count();
			$goodRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->where('rating',4)->count();
			$averageRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->where('rating',3)->count();
			$belowaverageRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->where('rating',2)->count();
			$poorRatingCount=ClientReview::where('client_reviews.nanny_id',Auth::user()->id)->where('rating',1)->count();
			return View::make('front.dashboard.nanny_rating',compact('results','averageRating','totalRatingCount','excellentRatingCount','goodRatingCount','averageRatingCount','belowaverageRatingCount','poorRatingCount'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}
	public function nannyGiveFeedback(Request $request){
		// print_r($request->all());die;
		if(!empty(Auth::user())){
			if($request->ajax() && $request->method()=='POST'){
				$request->replace($this->arrayStripTags($request->all()));
				$formData						=	$request->all();
				$validator 					=	Validator::make(
					$request->all(),
					array(
						'client_id'		    => 'required',
						'review'		    => 'required',
						'rating'		    => 'required',
					),	
					array(
						"client_id.required"			=>	trans("This field is required."),
						"review.required"			=>	trans("Please enter a review."),
						"rating.required"			=>	trans("Please select a rating."),
						
					)
				);
				if ($validator->fails()){
					return response()->json(['success' => 0, 'errors' => $validator->errors()]);
				}else{

					$obj                 =  new NannyReview;
					$obj->user_id        =  $request->client_id;       
					$obj->nanny_id       =  Auth::user()->id;       
					$obj->rating       	 =  $request->rating;       
					$obj->review       	 =  $request->review;
					$obj->save();    
					$userId   = $obj->id;
					if(!empty($userId)){
						return response()->json(['success' => 1, 'data' =>  '', 'message'=>'Feedback has been submitted successfully' ]);
					}else{
						return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
					}

				}
			}else{
				return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
			}
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function clientRatingList(Request $request){
		if(!empty(Auth::user())){


			if($request->ajax() && $request->method()=='POST'){
				$inputOffset = $request->input('offset');
				$PerPageRecord=4;
				$offset=$inputOffset*$PerPageRecord;
				$results=	 ClientReview::leftJoin('users','client_reviews.nanny_id','users.id')->select('client_reviews.*','users.name','users.id as data_id')->orderBy('client_reviews.created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
				$html = View::make('front.dashboard.ratings_data',compact('results'))->render();
				return response()->json(['data'=>$html]);
			}
			$offset=0;
			$PerPageRecord=4;
			$results = ClientReview::leftJoin('users','client_reviews.nanny_id','users.id')->select('client_reviews.*','users.name','users.id as data_id')->orderBy('client_reviews.created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();
			$averageRating = ClientReview::avg('rating');
			$totalRatingCount=ClientReview::count();
			$excellentRatingCount=ClientReview::where('rating',5)->count();
			$goodRatingCount=ClientReview::where('rating',4)->count();
			$averageRatingCount=ClientReview::where('rating',3)->count();
			$belowaverageRatingCount=ClientReview::where('rating',2)->count();
			$poorRatingCount=ClientReview::where('rating',1)->count();
			return View::make('front.dashboard.client_rating',compact('results','averageRating','totalRatingCount','excellentRatingCount','goodRatingCount','averageRatingCount','belowaverageRatingCount','poorRatingCount'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function clientGiveFeedback(Request $request){
		// print_r($request->all());die;
		if(!empty(Auth::user())){
			if($request->ajax() && $request->method()=='POST'){
				$request->replace($this->arrayStripTags($request->all()));
				$formData						=	$request->all();
				$validator 					=	Validator::make(
					$request->all(),
					array(
						'nanny_id'		    => 'required',
						'review'		    => 'required',
						'rating'		    => 'required',
					),	
					array(
						"nanny_id.required"			=>	trans("This field is required."),
						"review.required"			=>	trans("Please enter a review."),
						"rating.required"			=>	trans("Please select a rating."),
						
					)
				);
				if ($validator->fails()){
					return response()->json(['success' => 0, 'errors' => $validator->errors()]);
				}else{

					$obj                 =  new ClientReview;
					$obj->user_id        =  Auth::user()->id;      
					$obj->nanny_id       =  $request->nanny_id;       
					$obj->rating       	 =  $request->rating;       
					$obj->review       	 =  $request->review;
					$obj->save();    
					$userId   = $obj->id;
					if(!empty($userId)){
						Session::flash('success',trans("Feedback has been submitted successfully"));
						return response()->json(['success' => 1, 'data' =>  '', 'message'=>'Feedback has been submitted successfully', 'rating' => $obj->rating ]);
					}else{
						return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
					}

				}
			}else{
				return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
			}
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function clientGiveFeedbackToSite(Request $request){

		if(!empty(Auth::user())){
			if($request->ajax() && $request->method()=='POST'){
				$request->replace($this->arrayStripTags($request->all()));
				$formData						=	$request->all();
				$validator 					=	Validator::make(
					$request->all(),
					array(
						'review'		    => 'required',
						'site_rating'		    => 'required',
					),	
					array(
						"review.required"			=>	trans("Please enter a review."),
						"site_rating.required"			=>	trans("Please select a rating."),
						
					)
				);
				if ($validator->fails()){
					return response()->json(['success' => 0, 'errors' => $validator->errors()]);
				}else{

					$obj                 =  new Siterating;
					$obj->user_id        =  Auth::user()->id;           
					$obj->rating       	 =  $request->site_rating;       
					$obj->review       	 =  $request->review;
					$obj->save();    
					$userId   = $obj->id;
					if(!empty($userId)){
						Session::flash('success',trans("Feedback has been submitted successfully"));
						return response()->json(['success' => 1, 'data' =>  '', 'message'=>'Feedback has been submitted successfully']);
					}else{
						return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
					}

				}
			}else{
				return response()->json(['success' => 2, 'data' =>  '', 'message'=>'Something went wrong.' ]);
			}
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
		
	}

	public function myPlanDetail(){
		if(!empty(Auth::user())){
			$userId = Auth::user()->id;
			$package=UserPlans::where('user_plans.user_id',$userId)->leftJoin('packages','user_plans.plan_id','packages.id')->select('user_plans.*','packages.name','packages.slug','packages.price','packages.no_of_month','packages.description')->orderBy('user_plans.created_at', 'DESC')->where('status',1)->first();
			$standard 	= CustomHelper::getmasterByType('standard ');
			$pro 		= CustomHelper::getmasterByType('pro');
			$advanced	= CustomHelper::getmasterByType('advanced');
			$pakages   	= Package::where('is_active', 1)->where('is_deleted', 0)->orderBy('order_type', 'asc')->get();
				
			return View::make('front.dashboard.my_plan_detail',compact('package', 'standard', 'pro', 'advanced', 'pakages'));
		}else{
			Session::flash('error',trans("Something Went Wrong"));
			return Redirect::to('/');	
		}
	}

	public function myNewPlan(Request $request,$id){
		$id  = base64_decode($id);

	    if(!empty($id)){
	      $planDeatil = Package::where('id',$id)->where('is_active', 1)->where('is_deleted', 0)->first();
	      $customerId = Auth::user()->customer_id;
	      Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
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
	      	return View::make('front.dashboard.my_new_plan', compact('planDeatil','customer'));
	    }else{
	    	Session::flash('error','Something went to wrong');
	    	Redirect::back();
	    }
	}
		
		
	public function myNewPlanSubmit(Request $request)
	{
		//echo "<pre>";print_r($request->all());die;
		$Plan = Package::where('id', $request->planid)->first();
		if(!empty($request->card_id) && $request->card_id != 1)
		{
			
			if(empty($Plan)){
				return response()->json(['success' => false, 'data'=>0, 'msg' => 'Plan is invalid please select plan']);	
			}
			    $users 	= Auth::user();
				Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

			  $getCardData = $stripe->customers->retrieveSource(
				  Auth::user()->customer_id,
				  $request->card_id,
				);
				   if(!empty($getCardData)){
					   $userData = $stripe->customers->updateSource(
						  Auth::user()->customer_id,
						  $getCardData['id'],
						);
					   //$checkCoupanwithTotalPrice =  CustomHelper::checkCoupenCode($request->coupen_code, $request->planid);

						if(!empty($userData) && $userData->id == $request->card_id){
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
						
						if(!empty($SubscriptionData) && $SubscriptionData['status'] == 'active'){

							$activeSubction = UserPlans::where('user_id', Auth::user()->id)->where('status', 1)->get();
							if(!empty($activeSubction)){
								foreach ($activeSubction as $key => $value) {
									if(!empty($value->subscription_id)){
									 	$stripe->subscriptions->cancel(
										  $value->subscription_id,
										  []
										);
									}
									
								}	
							}
							UserPlans::where('user_id', Auth::user()->id)->where('status', 1)->update(['status' => 0]);
							$objPlan =  new UserPlans;
							$objPlan->user_id 			=  Auth::user()->id;
							$objPlan->plan_id 			=  $Plan->id;
							$objPlan->status 			=  1;
							$objPlan->plan_start_date    =  date('Y-m-d'); 
							$objPlan->plan_end_date		 =  date('Y-m-d', strtotime("+".$Plan->no_of_month." months", strtotime(date('Y-m-d'))));
							$objPlan->subscription_id 	=  $SubscriptionData['id'];
							if($objPlan->save()){
									$dataPlan = CustomHelper::getPlanById($objPlan->plan_id);
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

									Session::flash('success', 'Subscription created successfully');
									return response()->json(['success' => true, 'msg' => 'Subscription created successfully', 'page_redirect' => url('/my-plan')]);
							}else{
								Session::flash('error', 'Something went to wrong, plan created, please try again');
									return response()->json(['success' => true, 'msg' => 'Something went to wrong, plan created', 'page_redirect' => url('/my-plan')]);
							}


							
						}else{
							return response()->json(['success' => false, 'data'=>0, 'msg' => 'Subscription is not created, sothing went to wrong']);	
						}
						
						
					}else{
						return response()->json(['success' => false, 'data'=>0, 'msg' => 'Card is Invalid, Please add another card']);	
					}
			   }else{
			   	return response()->json(['success' => false, 'data'=>0, 'msg' => 'Card not found, Please add another card']);	
			   }
				
		}else{
			$request->replace($this->arrayStripTags($request->all()));
			$formData						=	$request->all();
			$this_year = date("y");
			if(!empty($formData)){
				$validator 					=	Validator::make(
						$request->all(),
						array(
							'name'						=> 'required',
							'card-number'				=> 'required|numeric',
							'cvc'						=> 'required|numeric',
							'card-expiry-month'			=> 'required|numeric',
							'card-expiry-year'			=> "required|numeric|min:$this_year|max:$this_year+10",
						),	
						array(
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
					$users 	= Auth::user();
					Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
					$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

					$cardData =  $stripe->customers->createSource(
	                    Auth::user()->customer_id,
	                    ['source' => $request->stripe_token]
	                );

	                //echo "<pre>";print_r($cardData);die;

					$cardData = $stripe->customers->update(
					  $users->customer_id,
					  ['metadata' => ['order_id' => 'Auth::user()->id']]
					);

					if(!empty($cardData)){
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
						if(!empty($SubscriptionData) && $SubscriptionData['status'] == 'active'){
							
							$activeSubction = UserPlans::where('user_id', Auth::user()->id)->where('status', 1)->get();
							if(!empty($activeSubction)){
								foreach ($activeSubction as $key => $value) {
									if(!empty($value->subscription_id)){
									 	$stripe->subscriptions->cancel(
										  $value->subscription_id,
										  []
										);
									}
									
								}	
							}
							UserPlans::where('user_id', Auth::user()->id)->where('status', 1)->update(['status' => 0]);
							
							$objPlan =  new UserPlans;
							$objPlan->user_id 			=  Auth::user()->id;
							$objPlan->plan_id 			=  $Plan->id;
							$objPlan->status 			=  1;
							$objPlan->plan_start_date    =  date('Y-m-d'); 
							$objPlan->plan_end_date		 =  date('Y-m-d', strtotime("+".$Plan->no_of_month." months", strtotime(date('Y-m-d'))));
							$objPlan->subscription_id 	=  $SubscriptionData['id'];
							$objPlan->save();	
							if($objPlan->save()){
									$dataPlan = CustomHelper::getPlanById($objPlan->plan_id);
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

									Session::flash('success', 'Subscription created successfully');
									return response()->json(['success' => true, 'msg' => 'Subscription created successfully', 'page_redirect' => url('/my-plan')]);
							}else{
								Session::flash('error', 'Something went to wrong, plan created, please try again');
									return response()->json(['success' => true, 'msg' => 'Something went to wrong, plan created', 'page_redirect' => url('/my-plan')]);
							}
						}else{
							return response()->json(['success' => false, 'data'=>0, 'msg' => 'Subscription is not created, sothing went to wrong']);	
						}
						
						
					}else{
						return response()->json(['success' => false, 'data'=>0, 'msg' => 'Card is Invalid, Please add another card']);	
					}
				}
			}

		}
		
	}
	 
	public function changeUserPlanStatus(Request $request){

		if(!empty($request->id)){
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			$UserPlans = UserPlans::where('user_id', Auth::user()->id)->where('status', 1)->first();
			if(!empty($UserPlans)){
				try{
					if(!empty($UserPlans->subscription_id)){
						$stripe = new \Stripe\StripeClient(
							env('STRIPE_SECRET')
						);
						$stripe->subscriptions->cancel(
							$UserPlans->subscription_id,
							[]
						);
					}
				}catch(\Stripe\Exception\CardException $e) {
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
			UserPlans::where('id',$request->id)->update(['status'=>0]);
			$userPlans = UserPlans::find($request->id);
			
			Session::flash('success',trans("Plan has been cancelled successfully"));
			return response()->json(['success'=>1,'message'=>"Plan has been cancelled successfully"]);
		}

	}
	public function clientPaymentSetting(){
		$response	=	array();

		return View::make('front.dashboard.client_paymentsetting');
	}

	public function tip($validate_string){
		$userData =  DB::table('earnings')->where('validate_string', $validate_string)->first();
		if(!empty($userData)){
			$nanny_id = $userData->nanny_id;
			$user_id = $userData->user_id;
			return View::make('front.users.tip',compact('nanny_id', 'user_id'));
		}else{
			Session::flash('error',trans("Something went wrong"));
			return Redirect::to('/');
		}
		
	}

	public function tipSave(Request $request){
		 // print_r($request->all());
		
		$this_year = date("y");

		$validator 					=	Validator::make(
			$request->all(),
			array(
				'name'						=> 'required',
				'card-number'				=> 'required|numeric',
				'cvc'						=> 'required|numeric',
				'card-expiry-month'			=> 'required|numeric',
				'card-expiry-year'			=> "required|numeric|min:$this_year|max:$this_year+10",
				'amount'					=> 'required|numeric|min:50',
				
			),
			array(
				"name.required"						=>	trans("The name field is required."),
				"amount.required"					=>	trans("The amount field is required."),
				"amount.numeric"					=>	trans("The amount must be a number."),
				"card-number.required"				=>	trans("The card number field is required."),
				"card-number.numeric"				=>	trans("The card number must be a number."),
				"cvc.required"						=>	trans("The cvc field is required."),
				"cvc.numeric"						=>	trans("The cvc must be a number."),
				"card-expiry-month.required"		=>	trans("The card expiry month field is required."),
				"card-expiry-month.numeric"			=>	trans("The card expiry month must be a number."),
				"card-expiry-year.required"			=>	trans("The card expiry year field is required."),
				"card-expiry-year.numeric"			=>	trans("The card expiry year must be a number."),
				"amount.min"						=>	trans("Minimum amount is $50. Please enter more amount"),
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
		}
		else{
			$user_id = $request->user_id; 
			Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			$customerData = User::where('id',$user_id)->first();
			$customer_id = $customerData->customer_id;

			if(!empty($customerId)){
				$customer = Stripe\Customer::retrieve("$customerId");

				try{
					// $customer->sources->create(array("source" => $request->input('stripe_token')));	
					// print_r('fdsf');die;

					$customerId = User::where('id',$user_id)->value('customer_id');
					$customer = array();
					if(!empty($customerId)){
						$customer = Stripe\Customer::retrieve("$customerId");
					}
					$response["status"]			=	"success";
					$response["message"]		=	'Card has been added.';
					$response["cardlist"]		=	$customer;

					if(!empty($objplan) && $objplan->plan_id > 0){
						$dataPlan = CustomHelper::getPlanById($objplan->plan_id);
						$email 			    		=	$customerData->email;
						$full_name					= 	$customerData->name;
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
					'email' => $customerData->email,
					'name' =>  $customerData->name,
				]);


				User::where('id',$customerData->id)->update(array('customer_id'=>$customer->id));
				$customerId = $customer->id;
				$customer = Stripe\Customer::retrieve("$customerId");
				// print_r($customer);die;


				try{
						// $customer->sources->create(array("source" => $request->input('stripe_token')));



					$customerId = $customerData->customer_id;
					$customer = array();
					if(!empty($customerId)){
						$customer = Stripe\Customer::retrieve("$customerId");
					}


					$response["status"]			=	"success";
					$response["message"]		=	trans("messages.card_has_been_added");
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
			}

			$card_id 	 = $customer->id;
			

			try{

				Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

				$stripe = new \Stripe\StripeClient(
					env('STRIPE_SECRET')
				);
				$charge = $stripe->charges->create([
					'amount' => $request['amount'],
					'currency' => 'usd',
					"customer" => $card_id,
					'description' => 'Tip',
				]);

				$array = json_decode(json_encode($charge), true); 
				

				if(isset($array['status']) && $array['status'] == 'succeeded'){
					
					$earning 					= new Earning;
					$earning->user_id 			= Auth::user()->id;
					$earning->nanny_id 			= $request['nanny_id'];
					$earning->amount 			= $request['amount'];
					$earning->total_amount		= $request['amount'];
					$earning->tip_date 			= date('Y-m-d');
					$earning->transaction_id 	= $array['balance_transaction'];
					$earning->status 			= 1;
					$earning->type 				= 2;

					$earning->save();

					$nannyUser 					= 	User::where('id',$request['nanny_id'])->first();
					$clientUser                 = 	User::where('id',$earning->user_id)->first();

					$notification 					= new Notification;
					$notification->sender_id 		= $earning->user_id;
					$notification->message 			= 'Tip given successfully by'.' '.$clientUser->name;
					$notification->type 			= 1;
					$notification->save();

					$nannynotification 				= new Notification;
					$nannynotification->sender_id 	= $request['nanny_id'];
					$nannynotification->message 	= 'Tip received successfully.';
					$nannynotification->type 		= 1;
					$nannynotification->save();
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
			if($response['status'] == 'card_error'){
				return response()->json(['success'=>0, 'message'=>$response['message']]);
			}else{
				Session::flash('success',trans("Tip added successfully."));
				return response()->json(['success'=>1, 'page_redirect' => url('/'),'message'=>"Tip added successfully."]);
			}
			

		}
		// die;
	}

	public function registeredStripAccount(Request $request)
	{
		try
		{

			Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			$response = \Stripe\OAuth::token([
				'grant_type' => 'authorization_code',
				'code' => $request['code'],
			]);

			if(isset($response->stripe_user_id))
			{ 
				$isExist = User::where('stripe_user_id', $response->stripe_user_id)->count();
				if($isExist == 0)
				{
					$connected_account_id = $response->stripe_user_id;
					User::where('id', Auth::user()->id)->update(['stripe_user_id' => $connected_account_id]);
					//return back()->with('success', 'Stripe account updated.');
					Session::flash('success', 'Stripe account connected.');
					return Redirect::route('user.nanny-payment-setting');
				}
				else
				{
					Session::flash('error', 'Sorry!! This strip account is already connected');
					return Redirect::route('user.nanny-payment-setting');
				}
			}
			else if(isset($response->invalid_request))
			{
				Session::flash('error', 'Invalid Request');
				return Redirect::route('user.nanny-payment-setting');
			}
			else
			{
				Session::flash('error', 'Something went wrong');
				return Redirect::route('user.nanny-payment-setting');
			}

		}
		catch(\Exception $e)
		{  

			Session::flash('error', 'Something went wrong');
			return Redirect::route('user.nanny-payment-setting');
		}
	}

	public function disconnectStripeAccount(Request $request)
	{
		try
		{ 
			Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			$data = Stripe\OAuth::deauthorize([
				'client_id' => env('STRIPE_CLIENT_ID'),
				'stripe_user_id' => Auth::user()->stripe_user_id,
			]);
			
			User::where('id', Auth::user()->id)->update(['stripe_user_id' => '']);
			return Redirect::back()->with('success', trans('Stripe account removed'));
		}
		catch(\Exception $e)
		{  
			return Redirect::back()->with('error', trans('Something went wrong'));
		}
	}


	public function reviewRatings($validate_string){
		$userData =  DB::table('earnings')->where('review_validate_string', $validate_string)->first();
		if(!empty($userData)){
			$nanny_id 	= $userData->nanny_id;
			$user_id 	= $userData->user_id;

			$offset 		=	0;
			$PerPageRecord	=	4;

			$results = NannyReview::where('user_id',$user_id)->leftJoin('users','nanny_reviews.nanny_id','users.id')->select('nanny_reviews.*','users.name','users.id as data_id')->orderBy('nanny_reviews.created_at', 'DESC')->offset($offset)->limit($PerPageRecord)->get();

			$averageRating 			= 	NannyReview::where('nanny_id',$nanny_id)->avg('rating');
			$totalRatingCount		=	NannyReview::where('nanny_id',$nanny_id)->count();
			$excellentRatingCount	=	NannyReview::where('nanny_id',$nanny_id)->where('rating',5)->count();
			$goodRatingCount		=	NannyReview::where('nanny_id',$nanny_id)->where('rating',4)->count();
			$averageRatingCount		=	NannyReview::where('nanny_id',$nanny_id)->where('rating',3)->count();
			$belowaverageRatingCount=	NannyReview::where('nanny_id',$nanny_id)->where('rating',2)->count();
			$poorRatingCount		=	NannyReview::where('nanny_id',$nanny_id)->where('rating',1)->count();

			return View::make('front.users.rating',compact('nanny_id', 'user_id','averageRating','totalRatingCount','excellentRatingCount','goodRatingCount','averageRatingCount','belowaverageRatingCount','belowaverageRatingCount','poorRatingCount','results'));
		}else{
			Session::flash('error',trans("Something went wrong"));
			return Redirect::to('/');
		}
		
	}

	public function reviewRatingSave(Request $request){
		$formData       =   $request->all();

		// echo"<pre>";print_r($formData);die;
		$response       =   array();
		if(!empty($formData)){
			$validator  	=   Validator::make(
				$request->all(),
				array(
					//"review"		=>	'required',
					"rating"		=>	'required',
					"review"		=>	'required',
				),
				array(
					"rating.required"		=>	'Rating is required',
					"review.required"		=>	'Review is required'
				)
			);
			if($validator->fails()){
				$response				=	array(
					'success' 			=> 	false,
					'errors' 			=> 	$validator->errors()
				);
				Session::flash('error',  'Please enter ratings.'); 
				return redirect()->back()->withErrors(['error', 'Please enter ratings']);
				
			}else{

				$obj                 =  new NannyReview;
				$obj->user_id        =  $request['user_id'];       
				$obj->nanny_id       =  $request['nanny_id'];
				$obj->rating       	 =  $request['rating'];   
				$obj->review       	 =  $request['review'];
				$obj->status       	 =  1;

				$obj->save();    


				$nannyUser 						= User::where('id',$request['nanny_id'])->first();

				$nannynotification 				= new Notification;
				$nannynotification->sender_id 	= $request['nanny_id'];
				$nannynotification->message 	= 'Rating and review given successfully.';
				$nannynotification->type 		= 1;
				$nannynotification->save();
				
				Session::flash('success',  'Review has been saved successfully.'); 
				return redirect()->back();
			}
		}
	}

	public function joinInterview(Request $request,$id){
		$result = ScheduleInterview::where('id',$id)->first();
		return View::make('front.dashboard.zoom',compact('result'));
	}

	public function getCardData(Request $request)
	{ 
		$customerId = Auth::user()->customer_id;

		Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	      $customer = '';
			
			$customer =  Stripe\Customer::retrieveSource(
			  $customerId,
			  $request->cardId,
			  []
			);			
		if(!empty($customer)){
			return response()->json(['success'=>1, 'data' =>$customer,  'message'=>'success']);
		}else{
			return response()->json(['success'=>0, 'message'=>'fail']);
		}	
		

	}

	public function interviewApproved($id)
	{
		$timeslote = ScheduleInterview::where('id', $id)->where('nanny_id', Auth::user()->id)->first();
		if(!empty($timeslote)){

			$timeslotes = explode('-', $timeslote->meeting_day_time); 
            $fromTIme = !empty($timeslotes[0]) ? date('h:i a', strtotime($timeslotes[0])):'';
            $toTIme = !empty($timeslotes[1]) ? date('h:i a',strtotime($timeslotes[1])):'';
			$interviewTime = $fromTIme.' - '.$toTIme; 
			$todatDate = date('Y-m-d', strtotime($timeslote->interview_date)).'T'.$fromTIme.'Z';
			//$todatDate = date('Y-m-d').'T'.'16:45:00Z';
			$encoded_params = json_encode(
				array(
					"topic"=> 'Interview',
					"type"=>"2",
					'start_time'=>$todatDate, 
					"duration"=>(String)60,
					'timezone'=>"UTC",
					'agenda'=> "Interview",
					
				));
			$URL= "https://api.zoom.us/v2/users/me/meetings";
			$ch 			= 	curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array (
				"Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6Iks2bkRkUHhpUTNtM2hLU1ViZWF3M2ciLCJleHAiOjE5MDg2MjM2NDAsImlhdCI6MTYyNDYyMTkzNH0.Lx2ifZQ3uvAuM9qw8M5isCy0dxCdPfXuW0N3n2cfTUc",
				'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_params);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$result = curl_exec($ch);
			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$result     = json_decode($result, true);
			$outPutArr	=	array('status_code'=>$http_status, 'result'=>$result);
			
			if($outPutArr['status_code'] == 201){
				ScheduleInterview::where('id', $id)->update([ 'meeting_number' => $outPutArr['result']['id'], 'meeting_password' => $outPutArr['result']['password'], 'host_email' => $outPutArr['result']['host_email'], 'join_url' => $outPutArr['result']['join_url'], 'jsan_data' =>  $outPutArr, 'is_interview' => 1]);
			}

				$notification 					= new Notification;
				$notification->sender_id 		= Auth::user()->id;
				//$notification->message 			= 'You have created schedule with'.' '.$nannyUser->name.' '.'on'.' '.$date.' '.'at'.' '.$timeslote->time_slot;
				$notification->message 			= 'Interview has been approved successfully. Please check your email for further details.';
				
				$notification->type 			= 1;
				$notification->interview_id 	= $id;
				$notification->save();

				$nannynotification 				= new Notification;
				$nannynotification->sender_id 	= $timeslote->user_id;
				//$nannynotification->message 	= 'Interview has been schedule successfully with'.' '.$clientUser->name.' '.'on'.' '.$date.' '.'at'.' '.$timeslote->time_slot;
				$nannynotification->message     = 'Nanny has been approved interview successfully. Please check your email for further details.';
				$nannynotification->type 		= 1;
				$nannynotification->interview_id = $id;
				$nannynotification->save();

				//User Send email
				$userData 				=   User::where('id',$timeslote->user_id)->first();
				$date 					=   date('m/d/Y',strtotime($timeslote->interview_date));
				$roureUrl               =   route('meeting.join', Crypt::encrypt($timeslote->id));
				$email 			    	=	$userData->email;
				$full_name				= 	$userData->name; 
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','user_interview_sheduled_approved')->get()->toArray();
				$emailTemplates	    	= 	EmailTemplate::where('action','=','user_interview_sheduled_approved')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_name,Auth::user()->name,$interviewTime,$date,Auth::user()->name, $roureUrl);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

				//Nanny Send email	

				$email 			    	=	Auth::user()->email;
				$full_nanny_name		= 	Auth::user()->name; 
				$settingsEmail		    = 	Config::get('Site.to_email');
				$emailActions			= 	EmailAction::where('action','=','nanny_interview_sheduled_approved')->get()->toArray();
				$emailTemplates	    	= 	EmailTemplate::where('action','=','nanny_interview_sheduled_approved')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_nanny_name,$full_name,$date,$interviewTime, $roureUrl);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);

				Session::flash('success', 'Interview has been approved successfully');
				return Redirect::back();



		}else{
			Session::flash('something went to wrong');
			Redirect::back();
		}
		
	}

	public function interviewRejected(Request $request)
	{
		//print_r($request->all());
		if(empty($request->reject_reason)) {
            return response()->json(['success' => false, 'message' => 'Please enter the reject reason before rejecting the request.']);
        }
       
		$id = $request->id;
		$check = ScheduleInterview::where('id', $id)->where('nanny_id', Auth::user()->id)->first();
		if(!empty($check)){
			ScheduleInterview::where('id', $id)->where('nanny_id', Auth::user()->id)->update(['status' => 3, 'reject_reason' => $request->reject_reason]);
			$userData  = CustomHelper::getUserDataById($check->user_id);
			$nannyData  = CustomHelper::getUserDataById($check->nanny_id);
			
			$usernotification = new Notification;
			$usernotification->sender_id = $check->user_id;
			$usernotification->message = $userData->name.' Your Interview with '.$nannyData->name.' Request has been Rejected following reason '.$request->reject_reason;
			$usernotification->type = 1;
			$usernotification->booking_id = $id;
			$usernotification->save();
			
			$nannynotification = new Notification;
			$nannynotification->sender_id = $check->nanny_id;
			$nannynotification->message = $userData->name.'Interview has been Rejected, following reason '.$request->reject_reason;
			$nannynotification->type = 1;
			$nannynotification->booking_id = $id;
			$nannynotification->save();
			

			$setting 			=  Setting::get();
			$adminEmail 		=   $setting[11]->value;  
			$full_name          = 	$userData->name;
			$email		        =	$userData->email;
				//$message		    =	$obj->message;
			$settingsEmail 		= 	Config::get('Site.email');
				//$route_url      	=	WEBSITE_URL.'account-verification/'.$validate_string;
			$emailActions		= 	EmailAction::where('action','=','user_nannay_interview_rejected')->get()->toArray();
			$emailTemplates		= 	EmailTemplate::where('action','=','user_nannay_interview_rejected')->get(array('name','subject','action','body'))->toArray();
			$cons 				= 	explode(',',$emailActions[0]['options']);
			$constants 			= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject']; 
			$rep_Array 		= 	array($full_name, $nannyData->name, $request->reject_reason); 

			$messageBody	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']); 
			$mail			= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
			Session::flash('success',trans("Interview rejected successfully."));
			return response()->json(['success' => true]);
		}else{
			Session::flash('error',trans("sothing went to wrong"));
			return Redirect::back();
		}
	}

	public function clearSetaviblity(Request $request)
	{
		if(!empty($request->id) && !empty($request->day)){
			$checkNanny	= SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->first();
			
			if(!empty($checkNanny)){
				if(strtolower($request->day) == 'monday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['monday_from_time' => '', 'monday_to_time' => '']);
				}elseif(strtolower($request->day) == 'tuesday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['tuesday_form_time' => '', 'tuesday_to_time' => '']);
				}elseif(strtolower($request->day) == 'wednesday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['wednesday_form_time' => '', 'wednesday_to_time' => '']);
				}elseif(strtolower($request->day) == 'thursday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['thursday_form_time' => '', 'thursday_to_time' => '']);
				}elseif(strtolower($request->day) == 'friday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['friday_form_time' => '', 'friday_to_time' => '']);
				}elseif(strtolower($request->day) == 'saturday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['saturday_form_time' => '', 'saturday_to_time' => '']);
				}elseif(strtolower($request->day) == 'sunday'){
					SetAvailability::where('id', $request->id)->where('user_id', Auth::user()->id)->update(['sunday_form_time' => '', 'sunday_to_time' => '']);
				}
					Session::flash('success', 'Successfully Updated');
		   			return response()->json(['success' => true]);
			  }else{
			  	Session::flash('error', 'Something went to wrong');
		   		return response()->json(['success' => false, 'data' => 1]);
			  }

			
		}else{
			Session::flash('error', 'Something went to wrong');
		   return response()->json(['success' => false, 'data' => 2]);
		}

	}
}