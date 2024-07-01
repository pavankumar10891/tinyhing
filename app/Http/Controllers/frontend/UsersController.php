<?php

/**
 * User Controller
 */
namespace App\Http\Controllers\frontend;
//namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Redirect;
use View;
use Input;
use Validator;
use Hash;
use Session;
use App\Models\User;
use App\Models\Escort;
use App\Models\Tab;
use App\Models\MainTab;
use App\Models\UrlType;
use App\Models\EmailLog;
use Auth,Blade,Config,Cache,Cookie,DB,File,Mail,Response,URL,CustomHelper,Str;



class UsersController extends BaseController {
	
/** 
* Function to redirect website on main page
*
* @param null
* 
* @return
*/
 
	//Home Page
	public function index()
	{
		
		 ini_set('memory_limit', '500000M');
		 //ini_set('max_execution_time', '100000');
          ini_set('max_execution_time', '0');
/*
           $data  =  Escort::where('id', '>',99388)->get();
           foreach ($data as $key => $value) {
           	  //$UrlType = UrlType::where('url_type','escort')->where('url_id',$value->id)->first();
           	 
           	  	$obj = new UrlType;
           	  	$obj->url_id = $value->id;
           	  	$obj->url_value = Str::slug($value->name).'-'.$value->id;
           	  	$obj->url_type = 'escort';
           	  	$obj->save();
           	  
           }
     
          echo "Success";die;
          */
             //echo "<pre>";print_r($service_tags);
           // echo "<pre>";print_r($tagsArray);
	    $escorts =  Escort::with('escortimages')
	    ->with('receivedcity')
	    ->where('deleted_at', NULL)
	    ->where('active', 1)
	    ->where('type_of_escort', 'escort')
	    ->orderBy('created_at', 'desc')
	    ->limit(12)
	    ->get();

	    $tabsData  		= Tab::with('maintabs')->where('status',1)->where('deleted_at',Null)->get();
	    //$maintabData  	= MainTab::where('status',1)->where('deleted_at',Null)->get();

	    //UrlType::where('url_type', 'service')->update(['url_type' => 'sub_service']);

	     
		return View::make('frontend.users.index', compact('escorts', 'tabsData'));
	}

	public function loginForm()
	{
		if(!empty(Auth::user())){
			return Redirect::to('/');
		}else{
			return View::make('frontend.users.login');
		}
	}

	public function SignUpform()
	{

		if(!empty(Auth::user())){
			return Redirect::to('/');
		}else {
			return View::make('frontend.users.signup');

		}
	}


public function userRegisterEmail()
{
	return View::make('frontend.users.register_email');
}

public function userRegisterEmailSubmit(Request $request)
{
	$request->replace($this->arrayStripTags($request->all()));
	$formData	=	$request->all();

	if(!empty($formData)){
		$validator = Validator::make(
			$request->all(),
			array(
				'role' 			=> 'required',
				'email'    		=> 'required|email',
				'accepted'    	=> 'required',
			),
			array(
				'role.required' 	=> 'Please Select Your Type',
				'email.required' 	=> 'Please enter the email',
				'email.email' 		=> 'Please enter the valid email',
				'accepted.required' => 'Please accepted terms and condition ',
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
				//role 1=,memeber/client, 2=escort
				$email = $request->email;
				$validate_string = md5(time().$request->input('email'));
				$user = User::where('email', $email)->where('email_verified_at', '!=', Null)->first();
				if(!empty($user)){
					$user->password = !empty($user->password) ? $user->password:'';
	                $user->validate_string = $validate_string;
	                $user->email_verified_at = date('Y-m-d H:i:s');
	                $user->form_type = 'register';
	                
	                $user->save();	
				}else{
					$user = new User;
					$user->validate_string = $validate_string;
	                $user->email = $email;
	                $user->password = '';
	                $user->role_id = $request->role;
	                $user->email_verified_at = date('Y-m-d H:i:s');
	                $user->form_type = 'register';
	                $user->save();	
				}
				
				Session::put('user_email',$request->email);
                
				/*Session::put('user_role',$request->role);
				Session::put('user_email',$request->email);
				Session::put('verication_id',$validate_string);*/

				$rd = '<a href="'.url('/account-verify/'.$email.'/'.$validate_string).'">Click here</a>';
                $messageBody = 'Please varify your email address '.$email.' please '.$rd.'.'; 
                $settingsEmail 	= Config::get('Site.from_email');

           		$subject 				= 'Email Varification';
           		$mail					= 	$this->sendMail($email,'User',$subject,$messageBody,$settingsEmail);
				
				return response()->json(['success' => 1, 'page_redirect' => 'resend-mail','message'=>'Varification email send successfully']);
			}


	}

}

public function userRegisterSave(Request $request)
{
	$request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'username'            => 'required|unique:users,username',
                    'password'            => 'required|min:6',
                    'postal_code'         => 'required',
                    'location'         	  => 'required',
                    
                ),
                array(
                    'username.required'    => 'Please enter the username',
                    'username.unique'      => 'username already exits',
                    'username.required'    => 'Please enter the plz',
                    'location.required'    => 'Please enter the ort',
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
                    $email = $request->email;
                    $user = User::where('email', $email)->where('email_verified_at', '!=', null)->first();
                    if(!empty($user)){
                    	$user->validate_string 	= '';
	                    $user->username 	= $request->input('username');
	                    $user->password 	= Hash::make($request->input('password'));
	                    $user->post_code 	= $request->input('postal_code');
	                    $user->location_id 	= $request->input('location');
	                    $user->save();
	                     Auth::login($user);
	                     Session::flash('success', 'Successfully Registerd');
	                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'succes']);

                    }else{
                    	Session::flash('error', 'Something went to wrong');
	                    return response()->json(['success' => 2, 'page_redirect' => url('/'),'message'=>'Something went to wrong']);
                    }
                    
                   
                }

        }

}

public function resendEmail()
{
	return View::make('frontend.users.resend_email');
}

public function resendEmailSubmit(Request $request)
{
		$request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'email'            => 'required|email',
                ),
                array(
                    'email.required'    => 'Please enter the email',
                    'email.email'       => 'Please enter the valid email',
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
                    	$email = $request->email;
                    	$user = User::where('email', $email)->where('deleted_at',null)->first();
                    	$user = User::where('email', $email)->where('email_verified_at', '!=', Null)->first();
                    	$validate_string = md5(time().$request->input('email'));
						if(!empty($user)){
							$user->password = !empty($user->password) ? $user->password:'';
			                $user->validate_string = $validate_string;
			                $user->email_verified_at = date('Y-m-d H:i:s');
			                $user->form_type = 'register';
			                
			                $user->save();	
						}else{
							$user = new User;
							$user->validate_string = $validate_string;
			                $user->email = $email;
			                $user->password = '';
			                $user->role_id = $request->role;
			                $user->email_verified_at = date('Y-m-d H:i:s');
			                $user->form_type = 'register';
			                $user->save();	
						}
                   		$validate_string = md5(time().$request->input('email'));
                   		
                    	$rd = '<a href="'.url('/account-verify/'.$email.'/'.$user->validate_string).'">Click here</a>';
		                $messageBody = 'Please varify your email address '.$email.' please '.$rd.'.'; 
		                $settingsEmail 	= Config::get('Site.from_email');
                   		$subject 				= 'Email Varification';
                   		$mail					= 	$this->sendMail($email,'User',$subject,$messageBody,$settingsEmail);
			            Session::flash('success', 'Varification email send successfully');
						return response()->json(['success' => 1, 'page_redirect' => 'resend-mail','message'=>'Varification email send successfully']);
                    
                   
                }

        }
}

public function accountVarification($id)
{
	echo $id;
}	




public function userLogin(){
	return View::make('frontend.users.login');
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
					'password' => 'required|min:6',
					'email'    => 'required|email',
				),
				array(
					'email.required' => 'Please enter the email',
					'email.email' => 'Please enter the valid email',
					'password.required' => 'Please enter the password',
					'password.min' => 'Password must be 6 characters long.',
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
						if(!empty($user)) { 
							if($user->person_check == 1) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not verified.']);
							}elseif($user->is_active == 1) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account is not active.']);
							}elseif($user->status == 0) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account approval is pending.']);
							}elseif($user->blocked == 1) {
								return response()->json(['success' => 2, 'page_redirect' => 'login','message'=>'It seems your account has been blocked.']);
							}else{
								$userdata = array(
									'email' 		=> $request->input('email'),
									'password' 		=> $request->input('password')
								);
								Auth::login($user);
								Session::flash('success', 'You are now logged in!');
										///$userId = Auth::user()->id;
									return response()->json(['success' => true, 'page_redirect' => url('/')]);

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


public function searchCity(Request $request)
{
	$type = '';
	$dataArry = array();
	/*if(is_numeric($request->subject_name)){
	  	$data = $this->citySearchByTerm('postcode', $request->subject_name);
	  	 if(!empty($data)){
	  	 	foreach($data as $data){
	  	     $singleArray = array();
	  	     $singleArray['state_name']  = $data['state_name'];
	  	     $singleArray['type_name']   = $data['url_postcode'];		
	  	 	 $dataArry[] 			     = $singleArray;	
	  	 	}
	  	 }
	}else{
		$data = $this->citySearchByTerm('city', $request->subject_name);
		if(!empty($data)){
	  	 	foreach($data as $data){
	  	 	 $singleArray 				= array();
	  	 	 $singleArray['state_name'] = $data['state_name'];	
	  	 	 $singleArray['type_name']  = $data['url_city'];	
	  	 	 $dataArry[] 				= $singleArray;
	  	 	}
	  	 }
	}*/
	;
	$data = $this->citySearchByTerm('city', strtolower($request->subject_name));

		if(!empty($data)){
			  if(!empty($data['results_city'])){
			  	if(is_numeric($request->subject_name)){
			  		foreach($data['results_city'] as $city){
			  	 	 $singleArray 				= array();
			  	 	 $singleArray['state_name'] = 'PLZ';	
			  	 	 $singleArray['type_name']  = $city['postocde'].' ('. $city['city'].')';
			  	 	 $singleArray['slug'] 		=  Str::slug($singleArray['type_name']); 
			  	 	 $singleArray['user_lat'] 	= str_replace(',', '.', $city['postcode_lat']);
			  	 	 $singleArray['user_lon'] 	= str_replace(',', '.', $city['postcode_lon']); 
			  	 	 $dataArry[] 				= $singleArray;
			  	 	}

			  	}else{
			  		foreach($data['results_city'] as $city){
			  	 	 $singleArray 				= array();
			  	 	 $singleArray['state_name'] = 'City';	
			  	 	 $singleArray['type_name']  = $city['city'];
			  	 	 $singleArray['slug'] 		=  Str::slug($singleArray['type_name']); 
			  	 	 $singleArray['user_lat'] 	= str_replace(',', '.', $city['city_lat']);
			  	 	 $singleArray['user_lon'] 	= str_replace(',', '.', $city['city_lon']); 
			  	 	 $singleArray['slug'] 		=  Str::slug($singleArray['type_name']); 	
			  	 	 $dataArry[] 				= $singleArray;
			  	 	}
			  	}
			  	 	
		  	   }
		  	   if(!empty($data['results_state'])){
			  	 	foreach($data['results_state'] as $state){
			  	 	 $singleArray 				= array();
			  	 	 $singleArray['state_name'] = 'State';	
			  	 	 $singleArray['type_name']  = $state['state_name'];
			  	 	 $singleArray['slug'] 		=  Str::slug($singleArray['type_name']); 
			  	 	 $singleArray['user_lat'] 	= str_replace(',', '.', $state['state_lat']);
			  	 	 $singleArray['user_lon'] 	= str_replace(',', '.', $state['state_lon']); 	
			  	 	 $dataArry[] 				= $singleArray;
			  	 	}
		  	   }
		  	   if(!empty($data['results_ort'])){
			  	 	foreach($data['results_ort'] as $city){
			  	 	 $singleArray 				= array();
			  	 	 $singleArray['state_name'] = 'Places';	
			  	 	 $singleArray['type_name']  = $city['ort_name'];
			  	 	 $singleArray['slug'] 		= Str::slug($singleArray['type_name']);	
			  	 	 $singleArray['user_lat'] 	= str_replace(',', '.', $city['postcode_lat']);
			  	 	 $singleArray['user_lon'] 	= str_replace(',', '.', $city['postcode_lon']); 
			  	 	 $dataArry[] 				= $singleArray;
			  	 	}
		  	   }
	  	 }
	$dataArry = $this->array_multi_unique($dataArry);
	return response()->json($dataArry);
	//echo $type;
	

}

public function addDetailsPage(Request $request)
    {
           echo "sxasx";
           //https://intim.stage02.obdemo.com/en/lunadd
          
          ini_set('memory_limit', '500000M');
          ini_set('max_execution_time', '0');
            $slug_arr           =   array();
            $attribute_arr      =   array();
            $service_radius     =  isset($request->radius) ? $request->radius:9999999999;
			if($service_radius==0){
				$service_radius     =  9999999999;
			}
            $userLatlong        =  Session::get('user_lat_long');
            $pageLimit          = 20;
            $check = 0;
              /*$UrlType->url_type  = 'community_name';
                      $UrlType->url_id    = 0;*/
           /* echo "saxsa";die;
          echo "<pre>";print_r($data);die;*/

          
            $sag1 = request()->segment(2);
            if($sag1 != "" && strpos($sag1, ':') === false){
                $Code =  explode('-',  $sag1);
               if(is_numeric($Code[0])){
                   $slug_arr[] =   $Code[0]; 
               }else{
                $slug_arr[] =   $sag1;
               }
                
            }else if($sag1 != ""){
                $attribue_v = explode('&', $sag1);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }

            $sag2 = request()->segment(3);
            if($sag2 != "" && strpos($sag2, ':') === false){
                
                $slug_arr[] =   $sag2;
              
            }else if($sag2 != ""){
                $attribue_v = explode('&', $sag2);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }

            $sag3 = request()->segment(4);
            if($sag3 != "" && strpos($sag3, ':') === false){
               
               $slug_arr[] =   $sag3;
            }else if($sag3 != ""){
                $attribue_v = explode('&', $sag3);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }
            
            $sag4 = request()->segment(5);
            if($sag4 != "" && strpos($sag4, ':') === false){
               
                $slug_arr[] =   $sag4;
            }else if($sag4 != ""){
                $attribue_v = explode('&', $sag4);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }

            $sag5 = request()->segment(6);
            if($sag5 != "" && strpos($sag5, ':') === false){
                $slug_arr[] =   $sag5;
            }else if($sag5 != ""){
                $attribue_v = explode('&', $sag5);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }

            $sag6 = request()->segment(7);
            if($sag6 != "" && strpos($sag6, ':') === false){
               $slug_arr[] =   $sag6;
            }else if($sag6 != ""){
                $attribue_v = explode('&', $sag6);
                if(!empty($attribue_v)){
                    foreach($attribue_v as $attribue){
                        $attribute_arr[] = $attribue;
                    }
                }
            }

            //echo $sag6 = request()->segment(4);
            //die;

            //echo "<pre>";print_r($slug_arr);die;           
            $escorts =  Escort::with('escortimages');


            $dataArr = UrlType::whereIn('url_value', $slug_arr)->get()->toArray();
            /*if(!empty($attribute_arr)){
                foreach($attribute_arr as $attribute_arr_v){
                    $e_attribute_arr_v  =   explode(":",$attribute_arr_v);
                    $attribue_key       =   $e_attribute_arr_v[0];
                    $attribue_value     =   $e_attribute_arr_v[1];
                    $escorts->leftjoin("escort_".$attribue_key.'s',"escort_".$attribue_key."s.user_id","escort.id");
                    $escorts->where("escort_".$attribue_key,$attribue_value);
                }
            }*/
            if(empty($dataArr)){
               return Redirect::back();
            }
            if(!empty($attribute_arr)){
                foreach($attribute_arr as $attribute_arr_v){
                    $e_attribute_arr_v  =   explode(":",$attribute_arr_v);
                    $attribue_key       =   $e_attribute_arr_v[0];
                    $attribue_value     =   $e_attribute_arr_v[1];

                    $relation_table_name    =   "escort_".$attribue_key."s";
                    "<br>";
                    $relation_table_id      =  $attribue_key."_id";
                    $escorts->leftjoin($relation_table_name,$relation_table_name.".id","escorts.".$relation_table_id);
                    $escorts->where($relation_table_name.".name",$attribue_value);
                }
            }
            
            if(!empty($dataArr)){
                foreach($dataArr as $dataArr_v){
                    if($dataArr_v["url_type"] == "escort"){
                    	$profileData = $escorts->where("id",$dataArr_v["url_id"])
                        ->with('category')->with('escorSerice')->with('subservice')->with('sexuality')->with('figure')->with('escorttype')->with('escortPiercing')->with('receivedcity')
                        ->where('deleted', 0)->where('active', 1)->where('type_of_escort', 'escort')->orderBy('created_at', 'desc')->first();
                       echo "<pre>";print_r($profileData);die;
                        if(!empty($profileData)){
                            /*$profileData->escorSerice = array();
                            if(!empty($profileData->escorSerice)){
                                foreach($profileData->escorSerice as $key2=>$value2){
                                    $singSubService = array();
                                    $singSubService['name'] = $value2->name;
                                    $singSubService['data'] = array();
                                    foreach
                                }
                            }*/
                            $escorts =  Escort::with('escortimages')
                            ->with('receivedcity')
                            ->where('deleted_at', NULL)
                            ->where('active', 1)
                            ->where('type_of_escort', 1)
                            ->orderBy('created_at', 'desc')
                            ->where('i_receive_city_id', $profileData->i_receive_city_id)
                            ->where('id', '!=', $profileData->id)
                            ->get();

                        }

                        return View::make('frontend.users.ad_details', compact('profileData', 'escorts'));
                    }

                    if(!empty($userLatlong) &&  !empty(array_filter($userLatlong))){
                     
                         $escorts = $escorts->where(\DB::raw("((3959 * acos( cos(radians(" . $userLatlong[0] . ") ) * cos(radians( IFNULL(latitude,0))) * cos( radians(IFNULL( longitude,0)  ) - radians(" . $userLatlong[1] . ") ) + sin(radians(" . $userLatlong[0] . ") ) * sin( radians( IFNULL(latitude,0) ) ) )))"),"<=" ,$service_radius)->orderBy(\DB::raw("((3959 * acos( cos(radians(" . $userLatlong[0] . ") ) * cos(radians( IFNULL(latitude,0))) * cos( radians(IFNULL( longitude,0)  ) - radians(" . $userLatlong[1] . ") ) + sin(radians(" . $userLatlong[0] . ") ) * sin( radians( IFNULL(latitude,0) ) ) )))"))->paginate(20);
                         
                         if(!empty($escorts)){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                         
                         return View::make('frontend.users.search_details', compact('escorts'));
                    }
                    elseif($dataArr_v["url_type"] == "postcode"){
                       
                       //echo "<pre>"; print_r($userLatlong);die;
                        $escorts = $escorts->where("i_receive_post_code",$dataArr_v["url_value"])->paginate(20);
                         // echo "<pre>";print_r($escorts);die;  
                         $urlName = 'Postalcode Search';
                                                  $check = 0;
                         if(!empty($escorts)){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                         return View::make('frontend.users.search_details', compact('escorts', 'urlName'));
                    }
                    elseif($dataArr_v["url_type"] == "city"){
                          //echo "dee";die;
                    
                         $escorts = $escorts->where("i_receive_city_id",explode(',',$dataArr_v["url_id"]))->paginate(20);

                         if(count($escorts) > 0){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                         return View::make('frontend.users.search_details', compact('escorts'));

                    }elseif($dataArr_v["url_type"] == "community_name"){
                       
                        $checklatlong =  DB::table('geo_location_codes')->select('community_name')->where('community_name', $dataArr_v["url_value"])->first();
                        if(!empty($checklatlong)){
                            $lat = $checklatlong->local_community_lat;
                            $long = $checklatlong->local_community_lon;

                             Session::put('check', $check);
                            $escorts = $escorts->where(\DB::raw("((3959 * acos( cos(radians(" . $lat . ") ) * cos(radians( IFNULL(latitude,0))) * cos( radians(IFNULL( longitude,0)  ) - radians(" . $long . ") ) + sin(radians(" . $lat . ") ) * sin( radians( IFNULL(latitude,0) ) ) )))"),"<=" ,$service_radius)->orderBy(\DB::raw("((3959 * acos( cos(radians(" . $lat . ") ) * cos(radians( IFNULL(latitude,0))) * cos( radians(IFNULL( longitude,0)  ) - radians(" . $long . ") ) + sin(radians(" . $lat . ") ) * sin( radians( IFNULL(latitude,0) ) ) )))"))->paginate(20);
                        }else{
                            
                          $escorts =  $escorts->paginate(20);
                        }

                        if(!empty($escorts)){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                         return View::make('frontend.users.search_details', compact('escorts', 'urlName'));

                    }else if($dataArr_v["url_type"] == "category"){
                       
                        $escorts = $escorts->where("category_id",$dataArr_v["url_id"])->paginate(20);
                        $urlName = 'Category Search';
                        if(!empty($escorts)){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                        return View::make('frontend.users.search_details', compact('escorts', 'urlName'));
                    }else{
                        
                        $escorts =  $escorts->paginate(20);
                        if(!empty($escorts)){
                            $check = 1;
                         }else{
                            $check = 0;
                         }
                          Session::put('check', $check);
                       return View::make('frontend.users.search_details', compact('escorts','urlName', 'check'));
                    }
                }
            }
           

    }


    public function forgotPassword()
    {
    	return View::make('frontend.users.forgot_password');
    }
    public function forgotPasswordSendEmail(Request $request){

    	$request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();
        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'forgot_email'            => 'required|email',
                ),
                array(
                    'forgot_email.required'    => 'Please enter the email',
                    'forgot_email.email'       => 'Please enter the valid email',
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
                    $email = $request->forgot_email;
                	$user = User::where('email',$email)->where('deleted_at',null)->first();
                	if(empty($user)){
                	  $errors['forgot_email_error'] = 'Email does not exits our system';
                	  return response()->json(['success' => false, 'errors' => $errors]);
                	}else{
                		$validate_string = md5(time().$request->input('email'));
                		$user->validate_string = $validate_string;
                		$user->save();
                		$Name = $user->name;
                   		
                    	$rd = '<a href="'.url('/reset-password/'.$email.'/'.$validate_string).'">Click here</a>';
		                $message = 'Your password change url, please '.$email.' please '.$rd.'.'; 
		                $settingsEmail 	= Config::get('Site.from_email');
		                $subject 				= 'Chnage Password';
           				$mail					= 	$this->sendMail($email,$Name,$subject,$message,$settingsEmail);
			            Session::flash('success', 'We have send change password email');
						return response()->json(['success' => 1, 'page_redirect' => url('/login'),'message'=>'Varification email send successfully']);

                	}
                	
                }

        }

    }
     public function resetPasswordSave(Request $request){

    	$request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();
        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'password'            => 'required|min:6',
                    'confirm_password'    => 'required|same:password',
                ),
                array(
                    'password.required'    		=> 'Please enter the password',
                    'confirm_password.required' => 'Please enter the confirm password',
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
                    $user = User::where('email',$email)->where('deleted_at',null)->first();
                    if(!empty($user)){
                    	$user->password      		= Hash::make($request->input('password'));
                    	$user->validate_string      = null;
                    	$user->save();
                    	Session::flash('success', 'Password change successfully');
						return response()->json(['success' => 1, 'page_redirect' => url('/login'),'message'=>'password change successfully']);
                    }else{
                    	Session::flash('error', 'Something went to wrong');
						return response()->json(['success' => 1, 'page_redirect' => url('/login'),'message'=>'password change successfully']);
                    }
                    
                }

        }

    }

    public function reportAd(Request $request){

    	$request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();
        if(!empty($formData)){
	        	if (Auth::check()) {
		        	$validator = Validator::make(
		                $request->all(),
		                array(
		                	'reason'            => 'required',
		                ),
		                array(
		                    'reason.required'    => 'Please enter the reason',

		                    )
		            );
	        	}else{
	        		$validator = Validator::make(
		                $request->all(),
		                array(
		                	'name'            	=> 'required',
		                    'email'            	=> 'required|email',
		                	'reason'            => 'required',
		                ),
		                array(
		                	'name.required'    => 'Please enter the name',
		                    'email.required'    => 'Please enter the email',
		                    'email.email'       => 'Please enter the valid email',
		                    'reason.required'    => 'Please enter the reason',

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
                	$name = $request->input('name');
            		$email = $request->input('email');
            		$reason = $request->input('reason');

                	if(Auth::user()){
	                	$name = Auth::user()->username;
	                	$email = Auth::user()->email;
                	}
                	
                	$message =  'Name: '.$name.'<br>'.
			                	'Email: '.$email.'<br>'.
			                	'Reson: '.$reason.'<br>';
           			$adminEmail = 'intim@mailinator.com'; 

       				$settingsEmail 	= Config::get('Site.from_email');
	                $subject 				= 'Report Ad';
       				$mail					= $this->sendMail($adminEmail,$name,$subject,$message,$settingsEmail);
		            Session::flash('success', 'Report sent successfully');
					return response()->json(['success' => 1, 'page_redirect' => url('/'),'message'=>'Report sent successfully']);
                	
                }

        }

    }



}// end UsersController class
