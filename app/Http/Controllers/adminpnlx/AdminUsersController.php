<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Admin;
use App\Model\Group;
use App\Model\Acl;
use App\Model\AclAdminAction;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* CustomersController Controller
*
* Add your methods in the class below
*
*/
class AdminUsersController extends BaseController {

	public $model		=	'Admin';
	public $sectionName	=	'UserAdmin';
	public $sectionNameSingular	=	'UserAdmin';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Users 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB	=	Admin::with('group')->where('user_role', '!=', 'super_admin');
		} else {
			$groupId = auth()->guard('admin')->user()->login_user_group_id;
			$DB	=	Admin::with('group')->where('user_role', '!=', 'super_admin') ->where('parent_admin_id', auth()->guard('admin')->user()->id)->whereRaw("FIND_IN_SET($groupId,group_id)");
					/* ->where(function ($query){
						$query->where('parent_admin_id', auth()->guard('admin')->user()->id)
							  ->orWhere('id', '=', auth()->guard('admin')->user()->id);
					}); */
							//->where('parent_admin_id', auth()->guard('admin')->user()->id);
		}
		$items = $request->per_page ?? Config::get("Reading.records_per_page");

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
				$DB->whereBetween('admins.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('admins.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('admins.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					//check user role
					if($fieldName == "group_id"){
                        $DB->whereHas('group', function($q) use($fieldValue) {
                            $q->whereRaw("find_in_set('".$fieldValue."',group_id)");
                        });
					}
					if($fieldName == "name"){
						$DB->where("admins.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "phone_number"){
						$DB->where("admins.phone_number",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "email"){
						$DB->where("admins.email",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("admins.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("admins.is_deleted",0);
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'admins.id';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		/* $results = $DB->orderBy($sortBy, $order)->toSql();
		echo "<pre>";
		print_r($results);die; */
		$results = $DB->orderBy($sortBy, $order)->paginate($items);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$activeGroups = Group::orderBy('name','ASC')->pluck('name', 'id')->toArray();
		return  View::make("admin.$this->sectionName.index",compact('results','searchVariable','sortBy','order','query_string', 'activeGroups'));
	}// end index()

	
	/**
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){

		$aclModules		=	Acl::select('title','slug','id')->where('is_active',1)->where('parent_id',0)->get();
		if(!empty($aclModules)){
			foreach($aclModules as &$aclModule){
				$aclModule['sub_module']	=	Acl::where('is_active',1)->where('parent_id',$aclModule->id)->select('title','slug','id')->get();
				$module_ids			=	array();
				if(!empty($aclModule['sub_module'])){
					foreach($aclModule['sub_module'] as &$module){
						$module_id		=		$module->id;
						$module_ids[$module->id]		=		$module->id;
						$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','function_name','id')->orderBy('name','ASC')->get();
						 
						 
					}
				}
				$newArray	=	array(); 
				//$module_id				=	$module->id;
				$aclModule['extModule']	=	Acl::where('is_active',1)->whereIn('parent_id',$module_ids)->select('title','slug','id')->get();
		 
				if(!empty($aclModule['extModule'])){ 
					foreach($aclModule['extModule'] as &$record){
						$action_id			=	$record->id;
						$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','function_name','id')->orderBy('name','ASC')->get(); 
					}
				}
				
				if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
					$action_id			=	$aclModule->id;
					$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','function_name','id')->orderBy('name','ASC')->get();  
				} 
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$activeGroups = Group::orderBy('name','ASC')->pluck('name', 'id')->toArray();
		} else {
			$groupId = auth()->guard('admin')->user()->group_id;
			$activeGroups = Group::where('id', $groupId)->first();
		}
		//echo "<pre>";print_r($aclModules);die;
		return  View::make("admin.$this->sectionName.add")->with(compact('activeGroups', 'aclModules'));
	}// end add()
	
/**
* Function for save new customer
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'						=> 'required',
					'group_id' 					=> 'required',
					'email' 					=> 'required|email|unique:admins,email',
					'phone_number' 				=> 'required|numeric|digits:10|unique:admins,phone_number',
					'password'					=> 'required|min:8',
					'confirm_password'  		=> 'required|min:8|same:password',
				),
				array(
					"name.required"				=>	trans("The first name field is required."),
					"group_id.required"			=>	trans("The group name field is required."),
					"email.required"			=>	trans("The email field is required."),
					"email.email"				=>	trans("The email must be a valid email address."),
					"email.unique"				=>	trans("The email has already been taken."),
					"phone_number.required"		=>	trans("The phone number field is required."),
					"phone_number.unique"		=>	trans("The phone number is already taken."),
					"phone_number.numeric"		=>	trans("The phone number must be numeric."),
					"phone_number.digits"		=>	trans("The phone number must be 10 digits."),
					"password.required"			=>	trans("The password field is required."),
					"password.min"				=>	trans("The password must be atleast 8 characters."),
					"confirm_password.required"	=>	trans("The confirm password field is required."),
					"confirm_password.same"		=>	trans("The confirm password not matched with password."),
					"confirm_password.min"		=>	trans("The confirm password must be atleast 8 characters."),
				)
			);

			$password 					= 	$request->input('password');
			if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
				$correctPassword		=	Hash::make($password);
			}else{
				$errors 				=	$validator->messages();
				$errors->add('password', trans("Password must have be a combination of numeric, alphabet and special characters."));
				return Redirect::back()->withErrors($errors)->withInput();
			}
			$groupId 					= 	!empty($request->input('group_id')) ? $request->input('group_id'): array();
			$group = array_filter($groupId);

			if(!empty($group)){
				$groupIds		=	$groupId;
			}
			else{
				$errors 				=	$validator->messages();
				$errors->add('group_id', trans("atleast 1 group selected"));
				return Redirect::back()->withErrors($errors)->withInput();
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new Admin;
				$obj->group_id 							=  implode(',', $request->input('group_id'));
				$obj->name 								=  $request->input('name');
				$obj->email 							=  $request->input('email');
				$obj->phone_number 						=  $request->input('phone_number');
				$obj->password	 						=  Hash::make($request->input('password'));
				$obj->user_role 						= 'user_admin';
				$obj->parent_admin_id 					= auth()->guard('admin')->user()->id;
				$obj->save();
				$userId					=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(!empty($thisData['data'])){
					$id	=	$userId;
					UserPermission::where('user_id',$id)->delete();
					UserPermissionAction::where('user_id',$id)->delete();
					
					foreach($thisData['data'] as $data){ 
						$obj 					= 	array(); 
						$obj['user_id']			=  !empty($id)?$id:0;  
						$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
						$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
						$userpermissiondata 	=   UserPermission::create($obj);
						$userpermissionID		=	$userpermissiondata->id;
					
						if(isset($data['module']) && !empty($data['module'])){
							foreach($data['module'] as $subModule){ 
								$objData 							= array(); 
								$objData['user_id']					=  !empty($id)?$id:0;  
								$objData['user_permission_id']		=  $userpermissionID; 
								$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
								$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
								$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
								$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
								UserPermissionAction::create($objData);
							}
						}
					} 
				}

				//mail email and password to new registered user
				$settingsEmail 			=	Config::get('Site.email');
				$full_name				= 	$obj->name; 
				$email					= 	$obj->email;
				$password				= 	$request->input('password');
				$route_url     			= 	URL::to('/adminpnlx/login');
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

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->sectionName.".index");
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
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('admins',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		
		$model					=	Admin::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
	
			 
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$activeGroups = Group::orderBy('name','ASC')->pluck('name', 'id')->toArray();
		} else {
			$groupId = auth()->guard('admin')->user()->group_id;
			$activeGroups = Group::where('id', $groupId)->pluck('name', 'id')->toArray();
		}
		$userId = $modelId;
		$aclModules		=	Acl::select('title','slug','id',DB::Raw("(select is_active from user_permissions where user_id = $userId AND admin_module_id = admin_modules.id LIMIT 1) as active"))->where('is_active',1)->where('parent_id',0)->get(); 
					
		if(!empty($aclModules)){
			foreach($aclModules as &$aclModule){
				$aclModule['sub_module']	=	Acl::where('is_active',1)->where('parent_id',$aclModule->id)->select('title','slug','id')->get();
				$module_ids			=	array();
				if(!empty($aclModule['sub_module'])){
					foreach($aclModule['sub_module'] as &$module){
						$module_id		=		$module->id;
						$module_ids[$module->id]		=		$module->id;
						$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','function_name','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $module_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('name','ASC')->get();
						 
						 
					}
				}
				$newArray	=	array(); 
				//$module_id				=	$module->id;
				$aclModule['extModule']	=	Acl::where('is_active',1)->whereIn('parent_id',$module_ids)->select('title','slug','id')->get();
		 
				if(!empty($aclModule['extModule'])){ 
					foreach($aclModule['extModule'] as &$record){
						$action_id			=	$record->id;
						$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','function_name','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('name','ASC')->get(); 
					}
				}
				
				if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
					$action_id			=	$aclModule->id;
					$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','function_name','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('name','ASC')->get();  
				} 
			}
		}
		return  View::make("admin.$this->sectionName.edit",compact('model', 'activeGroups', 'aclModules'));
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
		$thisData						=	$request->all(); 
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'				=> 'required',
					'group_id'				=> 'required',
					'email' 					=> 'required|email|unique:admins,email,'.$modelId,
					'phone_number' 				=> 'required|numeric|unique:admins,phone_number,'.$modelId,
					'password'					=> 'min:8',
					'confirm_password'  		=> 'min:8|same:password',
				),
				array(
					"name.required"		=>	trans("The first name field is required."),
					"group_id.required"		=>	trans("The group name field is required."),
					"email.required"			=>	trans("The email field is required."),
					"email.email"				=>	trans("The email must be a valid email address."),
					"email.unique"				=>	trans("The email has already been taken."),
					"phone_number.required"		=>	trans("The phone number field is required."),
					"phone_number.unique"		=>	trans("The phone number is already taken."),
					"phone_number.numeric"		=>	trans("The phone number must be numeric."),
					"password.min"				=>	trans("The password must be atleast 8 characters."),
					"confirm_password.same"		=>	trans("The confirm password not matched with password."),
					"confirm_password.min"		=>	trans("The confirm password must be atleast 8 characters."),
				)
			);
			if(auth()->guard('admin')->user()->user_role == 'super_admin'){
				$groupId 					= 	!empty($request->input('group_id')) ? $request->input('group_id'): array();
				$group = array_filter($groupId);
			}else{
				$groupId 					= 	@explode(",",$request->input('group_id'));
				$group 					= 	$groupId;
			}
			if(!empty($group)){
				$groupIds		=	$groupId;
			}
			else{
				$errors 				=	$validator->messages();
				$errors->add('group_id', trans("atleast 1 group selected"));
				return Redirect::back()->withErrors($errors)->withInput();
			}
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  $model;
				if(auth()->guard('admin')->user()->user_role == 'super_admin'){
					$obj->group_id 							=  implode(',', $request->input('group_id'));
				}else{
					$obj->group_id 							=  $request->input('group_id');
				}
				$obj->name 								=  $request->input('name');
				$obj->email 							=  $request->input('email');
				$obj->phone_number 						=  $request->input('phone_number');

				if(!empty($request->input('password'))){
					$obj->password	 						=  Hash::make($request->input('password'));
				}

				$obj->save();
				$userId					=	$obj->id;

				if(!empty($thisData['data'])) {
					UserPermission::where('user_id',$userId)->delete();
					UserPermissionAction::where('user_id',$userId)->delete();
					foreach($thisData['data'] as $data){ 
						$obj 					= 	array(); 
						$obj['user_id']			=  !empty($userId)?$userId:0;  
						$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
						$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
						$userpermissiondata 	=   UserPermission::create($obj);
						$userpermissionID		=	$userpermissiondata->id;
			
						if(isset($data['module']) && !empty($data['module'])){
							foreach($data['module'] as $subModule){ 
								$objData 							= array(); 
								$objData['user_id']					=  !empty($userId)?$userId:0;  
								$objData['user_permission_id']		=  $userpermissionID; 
								$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
								$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
								$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
								$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
								UserPermissionAction::create($objData);
							}
						}
					} 
				}
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));

				return Redirect::route($this->sectionName.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for update Currency  status
	*
	* @param $modelId as id of Currency 
	* @param $modelStatus as status of Currency 
	*
	* @return redirect page. 
	*/	
	public function delete($userId = 0){
		$userDetails	=	Admin::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$email 			=	'delete_'.$userId.'_'.$userDetails->email;		
			$phone_number 		=	'delete_'.$userId.'_'.$userDetails->phone_number;		
			Admin::where('parent_admin_id', auth()->guard('admin')->user()->id)->where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Admin::where('id',"$modelId")->first();
		
		if(empty($model)) {
			return Redirect::route($this->sectionName.".index");
		}
		return  View::make("admin.$this->sectionName.view",compact('model'));
	} // end view()

	public function getusergroup(Request $request){
	  $userId= $request->id;	
	  $data = Admin::where('id', $userId)->first();
	  if(!empty($data)){
		  $newData = explode(",",$data->group_id);
		   $groupArray = array();
		  foreach($newData as $key=>$value){
			  $sinfle =array();
			  $groupName = Group::select('name')->where('id', $value)->first();
			  $sinfle['id'] = $value;
			  $sinfle['name'] = $groupName->name;
			  $checkGroup =  Admin::where('default_group_id', $value)->first();
			  $checked = '';
			  if(!empty($checkGroup)){
				$checked =  'checked';
			  }
			  $groupArray[] = "<input type='hidden' id='user_group_id' name='user_id' value='".$userId."'><div class='radio-inline'><label class='radio radio-outline radio-success'><input type='radio' ".$checked." name='default_group_id'   value='".$value."'><span></span>". $groupName->name."</label><div><br/>";
		  } 
	  }  
	   return  $groupArray;
	}
	public function setAdminGroup(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData	=	$request->all();
	   if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'u_group_id'  				=> 'required',
				),
				array(
					"u_group_id.required"			=>	trans("The Group field is required."),
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
				//return Redirect::back()->withErrors($validator)->withInput();
			}else{
			  $group = $request->u_group_id;
			  $user = Admin::find($request->userId);
			  $user->default_group_id = $group;
			  if($user->save()){
			   return response()->json(['success' => true, 'page_redirect' => url()->previous()]);
			  }

			}
		}
	}
	

}// end BrandsController
