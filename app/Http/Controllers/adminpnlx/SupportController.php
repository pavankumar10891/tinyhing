<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\User;
use App\Model\Admin;
use App\Model\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* Customers Controller
*
* Add your methods in the class below
*
*/
class SupportController extends BaseController {

	public $model		=	'Support';
	public $sectionName	=	'Support Account';
	public $sectionNameSingular	= 'Support Account';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Customers 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Admin::query();
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
				$DB->whereBetween('created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "phone_number"){
						$DB->where("phone_number",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "email"){
						$DB->where("email",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("user_role",'support_admin');
		$DB->where("is_deleted",0);
		$DB->select("*");
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
	 * Function for add new customer
	 *
	 * @param null
	 *
	 * @return view page. 
	 */
	public function add(){ 
		return  View::make("admin.$this->model.add");
	}// end add()
	
	/**
	* Function for save new customer
	*
	* @param null
	*
	* @return redirect page. 
	*/

		public function save(Request $request){
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
                    'name'		    		=> 'required',
					'email' 				=> 'required|email|unique:admins,email',
					'phone_number' 			=> 'required|digits_between:10,15|numeric',
					'password'				=> 'required|min:8|custom_password',
					'confirm_password'		=> 'required|same:password',
				),
				array(
					"name.required"					=>	trans("The name field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email is already exists."),
					"phone_number.required"		    => 	trans("The phone number field is required"),
					"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
					"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
					"password.required"			    =>	trans("The password field is required"),
					"password.min"			     	=>	trans("The password must be atleast 8 characters"),
					"password.custom_password"	    =>	trans("The password must contain uppercase, lowercase, numbers, special characters"),				
					"confirm_password.required"	    =>	trans("The confirm password field is required"),
					"confirm_password.same"		    =>	trans("The confirm password does not match with password")
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 						=  new Admin;
				$obj->name 					=  ucfirst($request->input('name'));
				$obj->email 				=  $request->input('email');
				$obj->phone_number 			=  $request->input('phone_number');
				$obj->user_role 			=  "support_admin";
				$obj->password	 		    =  Hash::make($request->input('password'));
				$obj->save();
				$userId					    =  $obj->id;
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
	* @param $modelId as id of customer 
	* @param $status as status of customer 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($id){ 
		$status = Admin::where('id',$id)->value('is_active');
		if($status == '1'){
			Admin::where('id',$id)->update(['is_active' =>'0']);
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();
		}else{
			Admin::where('id',$id)->update(['is_active' => '1']);
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
		}
	}// end changeStatus()
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/

	public function sendverification($modelId = 0,Request $request){
		$model		=	Admin::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
		$settingsEmail 			=	Config::get('Site.email');
		$full_name				= 	$model->name; 
		$email					= 	$model->email;
		$route_url     		    =   URL::to('verify/');
		$click_link   			=   $route_url;
		$emailActions			= 	EmailAction::where('action','=','account_verification')->get()->toArray();
		 $emailTemplates		= 	EmailTemplate::where('action','=','account_verification')->get(array('name','subject','action','body'))->toArray();
		$cons 					= 	explode(',',$emailActions[0]['options']);
		$constants 				= 	array();
		foreach($cons as $key => $val){
			$constants[] 		= 	'{'.$val.'}';
		}
		$subject 				= 	$emailTemplates[0]['subject'];
		$rep_Array 				= 	array($full_name,$route_url,$route_url); 
		$messageBody		    = 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
		$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);	
			
		$statusMessage	=	trans("Verification email has been sent successfully to your registered email");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
	} 


	public function edit($modelId = 0,Request $request){
		$model		=	Admin::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
	 	return View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Admin::findorFail($modelId);
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
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'				    		=> 'required',
					'email' 				     	=> 'required|email|unique:admins,email,'.$modelId,
					'phone_number' 				    => 'required|numeric|digits_between:10,15',
					'password'				        => 'min:8|custom_password',
					'confirm_password'		        => 'same:password|required_with:password',
				),
				array(
					"name.required"					=>	trans("The name field is required."),
					"email.required"				=>	trans("The email field is required."),
					"email.email"					=>	trans("The email is not valid email address."),
					"email.unique"					=>	trans("The email is already exists."),
					"phone_number.required"		    => 	trans("The phone number field is required"),
					"phone_number.numeric"		    =>	trans("The phone number must be numeric"),
					"phone_number.digits_between"   =>	trans("The phone number must be 10 to 15 digits"),
					"password.required"		 	    =>	trans("The password field is required"),
					"password.custom_password"	    =>	trans("The password must contain uppercase, lowercase, numbers, special characters"),				
					"password.min"				    =>	trans("The password must be atleast 8 characters"),
					"confirm_password.required"	    =>	trans("The confirm password field is required"),
					"confirm_password.same"		    =>	trans("The confirm password does not match with password"),
					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$user						=  Admin::where("id",$modelId)->select("password")->first();
				$obj 						=  Admin::find($modelId);
				$obj->name 					=  ucfirst($request->input('name'));
				$obj->email 				=  $request->input('email');
				$obj->phone_number 			=  $request->input('phone_number');
				$obj->password				=  !empty($request->input('password')) ? Hash::make($request->input('password')):$user->password;
				$obj->save();
				$userId						=	$obj->id;
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
	* Function for update Customer  status
	*
	* @param $modelId as id of Customer 
	* @param $modelStatus as status of Customer 
	*
	* @return redirect page. 
	*/	

	 public function delete($userId = 0){
		$userDetails = Admin::find($userId);
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$email = 'delete_'.$userId.'_'.$userDetails->email;
			$phone_number = 'delete_'.$userId.'_'.$userDetails->phone_number;
			$deleteDate = date("Y-m-d H:i:s");
			Admin::where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number, 'deleted_at' => $deleteDate));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Admin::where('id',"$modelId")->select('*')->first();      
		if($model->is_deleted =='1') {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()

}// end CustomersController