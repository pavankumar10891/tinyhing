<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\User;
use App\Model\userCertificates;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* Vender Controller
*
* Add your methods in the class below
*
*/
class NannyController extends BaseController {

	public $model		=	'Nanny';
	public $sectionName	=	'Nanny Management';
	public $sectionNameSingular	= 'Nanny';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Nannies 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	User::query();
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
				$DB->whereBetween('users.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('users.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('users.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("users.name",'like','%'.$fieldValue.'%');
					}
					elseif($fieldName == "phone"){
						$DB->where("users.phone_number",'like','%'.$fieldValue.'%');
					}
					elseif($fieldName == "email"){
						$DB->where("users.email",'like','%'.$fieldValue.'%');
					}
					elseif($fieldName == "is_active"){
						$DB->where("users.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("users.user_role_id",NANNY_ROLE_ID);
		$DB->where("users.is_deleted",0);
		$DB->select("users.*");
	    $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';	
	    $records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
	    $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()
	
	/**
	 * Function for add new Vender
	 *
	 * @param null
	 *
	 * @return view page. 
	 */


	public function add(Request $request){ 
		return  View::make("admin.$this->model.add");
	}// end add()
	
/**
* Function for save new Nanny
*
* @param null
*
* @return redirect page. 
*/

	function save(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){	
			Validator::extend('custom_password', function($attribute, $value, $parameters) {
                if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value)  && preg_match('#[\W]#', $value)) {
                    return true;
                } else {
                    return false;
                }
            });
			
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		    => 'required',
					//'last_name'			    => 'required',
					'age'			        => 'required|numeric',
					'nanny_price'			=> 'required|numeric',
					'experience'			=> 'required',
					'description'			=> 'required',
					'email' 				=> 'required|email|unique:users,email',
					'phone_number' 			=> 'required|digits_between:10,15|numeric',
					'password'				=> 'required|min:8|custom_password',
					'confirm_password'		=> 'required|same:password',
					'photo_id'				=> 'required|mimes:'.IMAGE_EXTENSION,
					'resume'				=> 'mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'cpr_certificate'		=> 'mimes:'.CAREER_FORM_DOCUMENTS,
					'other_certificates'	=> 'mimes:'.OTHER_CERTIFICATES_EXTENSION,
					'identification_type'	=> 'required',
					'identification_file'	=> 'required|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
				),	
				array(
					"name.required"					=>	trans("The name field is required."),
					//"last_name.required"			=>	trans("The last name field is required."),
					"age.required"			        =>	trans("The age field is required."),
					"age.numeric"		            =>	trans("The age must be numeric"),
					"nanny_price.required"			=>	trans("The fees field is required."),
					"nanny_price.numeric"		    =>	trans("The fees must be numeric"),
					"experience.required"			=>	trans("The experience field is required."),
					"description.required"			=>	trans("The description field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email must be unique."),
					"phone_number.required"		    => 	trans("The phone number field is required"),
					"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
					"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
					"email.unique"					=>	trans("The email must be unique."),
					"password.required"			    =>	trans("The password field is required"),
					"password.min"			     	=>	trans("The password must be atleast 8 characters"),
					"password.custom_password"	    =>	trans("The password must contain uppercase,lowercase,numbers,special characters"),				
					"confirm_password.required"	    =>	trans("The confirm password field is required"),
					"confirm_password.same"		    =>	trans("The confirm password does not match with password"),
					"photo_id.required"				=>  trans("The photo id field is required"),
					"photo_id.mimes"				=>  trans("The photo id must be in: 'jpeg, jpg, png, gif, bmp formats'"),
					"resume.mimes"					=>	trans("The resume must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"cpr_certificate.mimes"			=>	trans("The cpr certificate must be in: 'pdf, docx, doc formats'"),
					"other_certificates.mimes"		=>	trans("The other certificates must be in: 'pdf, docx, doc formats'"),
					"identification_file.mimes"					=>	trans("The uploaded file must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"identification_file.required"					=>	trans("This field is required."),
					"identification_type.required"					=>	trans("The Identification Type field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$validateString		     	=  md5(time().$request->input('email'));
				$obj 						=  new User;
				//$obj->first_name 			=  ucfirst($request->input('first_name'));
				//$obj->last_name 			=  ucfirst($request->input('last_name'));
				$name 					    =  $request->input('name');
				$obj->name 					=  ucfirst($request->input('name'));
				$obj->age 					=  $request->input('age');
				$obj->experience 			=  $request->input('experience');
				$obj->description 			=  $request->input('description');
				$obj->slug 					=  $this->getSlug($name,'name','User');
				$obj->email 				=  $request->input('email');
				$obj->phone_number 			=  $request->input('phone_number');
				$obj->user_role_id 			=  NANNY_ROLE_ID;
				$obj->password	 		    =  Hash::make($request->input('password'));
				$obj->validate_string	    =  $validateString;
				$obj->verified		        =  1;
				$obj->is_active			    =  1;
				$obj->postcode			    =  $request->input('postcode');
				$obj->nanny_price		    =  $request->input('nanny_price');
				

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
					return Redirect::back()->withInput();
				}
				$settingsEmail 			=	Config::get('Site.email');
				$full_name				= 	$obj->name; 
				$email					= 	$obj->email;
				$password				= 	$request->input('password');
				$route_url     			= 	WEBSITE_URL;
				$click_link   			=   $route_url;
				$emailActions			= 	EmailAction::where('action','=','user_registration_information')->get()->toArray();
				$emailTemplates			= 	EmailTemplate::where('action','=','user_registration_information')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($full_name,$email,$password); 
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully."));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()

	/**
	* Function for update status
	*
	* @param $modelId as id of Vender 
	* @param $status as status of Vender 
	*
	* @return redirect page. 
	*/	

	public function changeStatus($id){ 
		$status = User::where('id',$id)->value('is_active');
		if($status == '1'){
			User::where('id',$id)->update(['is_active' =>'0']);
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();
		}else{
			User::where('id',$id)->update(['is_active' => '1']);
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
		}
	}// end changeStatus()
	
	/**
	* Function for display page for edit Vender
	*
	* @param $modelId id  of Vender
	*
	* @return view page. 
	*/

	/* public function sendverification($modelId = 0,Request $request){
		$model		=	User::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
		$settingsEmail 			=	Config::get('Site.email');
		$full_name				= 	$model->name; 
		$email					= 	$model->email;
		$route_url     		    =   URL::to('verify/');
		$click_link   			=   $route_url;
		$emailActions			= 	EmailAction::where('action','=','account_verification')->get()->toArray();
		$emailTemplates			= 	EmailTemplate::where('action','=','account_verification')->get(array('name','subject','action','body'))->toArray();
		$cons 					= 	explode(',',$emailActions[0]['options']);
		$constants 				= 	array();
		foreach($cons as $key => $val){
			$constants[] 		= 	'{'.$val.'}';
		}
		$subject 				= 	$emailTemplates[0]['subject'];
		$rep_Array 				= 	array($full_name,$email,$route_url,$click_link); 
		$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
		$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
		$statusMessage	=	trans("Verification has been send successfully");
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}  */


	public function senduserverification($modelId = 0,$status,Request $request){
		$model		=	User::where('id',$modelId)->first();
		
		if(empty($model)) {
			return Redirect::back();
		}
		User::where('id',$modelId)->update(['is_approved'=>$status]);
		if($status == 1){
			if(empty($model->provider_id)){
				$settingsEmail 			=	Config::get('Site.to_email');
				$username 				=  $model->name;
				$email					= 	$model->email; 
				$route_url     		    =   WEBSITE_URL.'user-verificaion/'.$model->validate_string;
				$click_link   			=   $route_url;
				$emailActions			= 	EmailAction::where('action','=','set_your_password')->get()->toArray();
				$emailTemplates			= 	EmailTemplate::where('action','=','set_your_password')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				}
				$subject 				= 	$emailTemplates[0]['subject'];
				$rep_Array 				= 	array($username,$route_url,$click_link);  
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$username,$subject,$messageBody,$settingsEmail);
			}
			$settingsEmail 			=	Config::get('Site.to_email');
			$username 				=  $model->name;
			$email					= 	$model->email; 
			$setAvablityUrl         =   WEBSITE_URL.'set-availability';

			$emailActions2			= 	EmailAction::where('action','=','nanny_account_approved')->get()->toArray();
			$emailTemplates2			= 	EmailTemplate::where('action','=','nanny_account_approved')->get(array('name','subject','action','body'))->toArray();
			$cons2 					= 	explode(',',$emailActions2[0]['options']);
			$cons2tants 				= 	array();
			foreach($cons2 as $key => $val){
				$cons2tants[] 		= 	'{'.$val.'}';
			}
			$subject2 				= 	$emailTemplates2[0]['subject'];
			$rep_Array2 				= 	array($username,$setAvablityUrl);  
			$messageBody2			= 	str_replace($cons2tants, $rep_Array2, $emailTemplates2[0]['body']);
			//echo "<pre>";print_r($messageBody2);die;
			//$this->sendMail($email,$username,$subject2,$messageBody2,$settingsEmail);

			$statusMessage	=	trans("Account has been approved successfully.");
		}
		/*if($status == 2){
			$settingsEmail 			=	Config::get('Site.to_email');
			$username 				=  $model->name;
			$email					= 	$model->email; 
			$emailActions			= 	EmailAction::where('action','=','nanny_account_rejected')->get()->toArray();
			$emailTemplates			= 	EmailTemplate::where('action','=','nanny_account_rejected')->get(array('name','subject','action','body'))->toArray();
			$cons 					= 	explode(',',$emailActions[0]['options']);
			$constants 				= 	array();
			foreach($cons as $key => $val){
				$constants[] 		= 	'{'.$val.'}';
			}
			$subject 				= 	$emailTemplates[0]['subject'];
			$rep_Array3 				= 	array($username);  
			$messageBody			= 	str_replace($constants, $rep_Array3, $emailTemplates[0]['body']);
			$mail					= 	$this->sendMail($email,$username,$subject,$messageBody,$settingsEmail);
			$statusMessage	=	trans("Account has been rejected.");
		}*/
		
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	} 


	public function edit($modelId = 0,Request $request){
		// $model=User::where('users.id',$modelId)
		// ->leftJoin('user_certificates','users.id','=','user_certificates.user_id')
		// ->select('users.*','user_certificates.other_certificates','user_certificates.id')
		// ->get();

		// $model		=	User::where('id',$modelId)->first();
		// if(empty($model)) {
		// 	return Redirect::back();
		// }

        // return view::make("admin.$this->model.edit",['model'=>$model]);


		$model	=	User::where('id',$modelId)->where('is_deleted',0)->select('*')->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$userCertificateData	=	userCertificates::where('user_id',$modelId)->select('other_certificates','id')->get()->toArray();
        return view::make("admin.$this->model.edit",compact('model','userCertificateData'));
		

	 	// return View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	
	/**
	* Function for update Vender 
	*
	* @param $modelId as id of Vender 
	*
	* @return redirect page. 
	*/

	function update($modelId,Request $request){
		$model					=	User::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			Validator::extend('custom_password', function($attribute, $value, $parameters) {
                if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value)  && preg_match('#[\W]#', $value)) {
                    return true;
                } else {
                    return false;
                }
            });

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
					'name'		    		=> 'required',
					//'last_name'			    => 'required',
					'age'			        => 'required|numeric',
					'nanny_price'			=> 'required|numeric',
					'experience'			=> 'required',
					'description'			=> 'required',
					'email' 				=> 'required|email|unique:users,email,'.$modelId,
					'phone_number' 			=> 'required|digits_between:10,15|numeric',
					'password'				=> 'nullable|min:8|custom_password',
					'confirm_password'		=> 'nullable|same:password',
					'photo_id'				=> 'nullable|mimes:'.IMAGE_EXTENSION,
					'resume'				=> 'nullable|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'cpr_certificate'		=> 'nullable|mimes:'.CAREER_FORM_DOCUMENTS,
					'other_certificates'	=> 'nullable|mimes:'.OTHER_CERTIFICATES_EXTENSION,
					'identification_type'	=> 'required',
					'identification_file'	=> 'nullable|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
				),	
				array(
					"name.required"					=>	trans("The name field is required."),
					"last_name.required"			=>	trans("The last name field is required."),
					"age.required"			        =>	trans("The age field is required."),
					"age.numeric"		            =>	trans("The age must be numeric"),
					"experience.required"			=>	trans("The experience field is required."),
					"description.required"			=>	trans("The description field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email must be unique."),
					"phone_number.required"		    => 	trans("The phone number field is required"),
					"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
					"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
					"email.unique"					=>	trans("The email must be unique."),
					"password.min"			     	=>	trans("The password must be atleast 8 characters"),
					"password.custom_password"	    =>	trans("The password must contain uppercase,lowercase,numbers,special characters"),				
					"confirm_password.same"		    =>	trans("The confirm password does not match with password"),
					"photo_id.mimes"				=>  trans("The photo id must be in: 'jpeg, jpg, png, gif, bmp formats'"),
					"resume.mimes"					=>	trans("The resume must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"cpr_certificate.mimes"			=>	trans("The cpr certificate must be in: 'pdf, docx, doc formats'"),
					"other_certificates.custom_other_certificate"	=>	trans("The other certificates must be in: 'pdf, docx, doc formats'"),
					"identification_file.mimes"					=>	trans("The uploaded file must be in: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats'"),
					"identification_file.required"					=>	trans("This field is required."),
					"identification_type.required"					=>	trans("The Identification Type field is required."),
					"nanny_price.required"			=>	trans("The fees field is required."),
					"nanny_price.numeric"		    =>	trans("The fees must be numeric"),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$user 						=  User::where("id",$modelId)->select("password")->first();
				$obj 						=  User::find($modelId);
				//$obj->first_name 			=  ucfirst($request->input('first_name'));
				//$obj->last_name 			=  ucfirst($request->input('last_name'));
				$name 					    =  $request->input('name');
				$obj->name 					=  ucfirst($request->input('name'));
				$obj->age 					=  $request->input('age');
				$obj->experience 			=  $request->input('experience');
				$obj->description 			=  $request->input('description');
				$obj->slug 					=  $this->getSlug($name,'name','User');
				$obj->email 				=  $request->input('email');
				$obj->phone_number 			=  $request->input('phone_number');
				$obj->postcode			    =  $request->input('postcode');
				$obj->password				=  !empty($request->input('password')) ? Hash::make($request->input('password')):$user->password;
				$obj->nanny_price		    =  $request->input('nanny_price');

				if($request->hasFile('photo_id')){ 
					if(File::exists(USER_IMAGE_ROOT_PATH.$obj->photo_id)) {
						File::delete(USER_IMAGE_ROOT_PATH.$obj->photo_id);	
					}
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
					if(File::exists(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->resume)) {
						File::delete(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->resume);	
					}
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
					if(File::exists(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->cpr_certificate)) {
						File::delete(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->cpr_certificate);	
					}
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
					if(File::exists(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->identification_file)) {
						File::delete(CERTIFICATES_AND_FILES_ROOT_PATH.$obj->identification_file);	
					}
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
							if(File::exists(CERTIFICATES_AND_FILES_ROOT_PATH.$model->other_certificate)) {
								File::delete(CERTIFICATES_AND_FILES_ROOT_PATH.$model->other_certificate);	
							}
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
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for update Vender  status
	*
	* @param $modelId as id of Vender 
	* @param $modelStatus as status of Vender 
	*
	* @return redirect page. 
	*/	

	 public function delete($UserId = 0){
		$UserDetails = User::find($UserId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($UserId){
			$email = 'delete_'.$UserId.'_'.$UserDetails->email;
			$phone_number = 'delete_'.$UserId.'_'.$UserDetails->phone_number;
			$deleteDate = date("Y-m-d H:i:s");
			User::where('id',$UserId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number, 'deleted_at' => $deleteDate));
			userCertificates::where('user_id',$UserId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	User::where('id',$modelId)->where('is_deleted',0)->select('*')->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$userCertificateData	=	userCertificates::where('user_id',$modelId)->select('other_certificates','id')->get()->toArray();
        return view::make("admin.$this->model.view",compact('model','userCertificateData'));
	} // end view()


	public function removenannyCertificates($id)
    {
		userCertificates::where('id',$id)->delete();
        return response()->json(['success'=>"true"]);
    }
}// end VenderController