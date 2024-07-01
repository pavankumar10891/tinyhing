<?php 
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Acl; 
use App\Model\AclAdminAction; 
use App\Model\User; 
use App\Model\UserPermission; 
use App\Model\UserPermissionAction; 
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Artisan;
use Illuminate\Http\Request;

class AclController extends BaseController {
	public function __construct(Request $request) {
		parent::__construct();
		$this->request = $request;
	}
	public function index(Request $request){  
		$DB 					= 	Acl::query();
		$searchVariable			=	array(); 
		$inputGet				=	$request->all();
		if($inputGet) {
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
			foreach($searchData as $fieldName => $fieldValue){ 
				if(!empty($fieldValue) && ($fieldName == "parent_id")){
					$DB->where("admin_modules.$fieldName",$fieldValue); 
				}else if(!empty($fieldValue) && ($fieldName != "parent_id")){
					$DB->where("admin_modules.$fieldName",'LIKE','%'.$fieldValue.'%'); 
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		
		$sortBy 				= 	($request->get('sortBy')) ? $request->get('sortBy') : 'admin_modules.updated_at';
	    $order  				= 	($request->get('order')) ? $request->get('order')   : 'DESC';
		$result 				= 	$DB->leftjoin('admin_modules as parent_admin','parent_admin.id','=','admin_modules.parent_id') 
									->select('admin_modules.*','parent_admin.title as parent_title')
									->orderBy($sortBy,$order)
									->paginate(Config::get("Reading.records_per_page"));
		$parent_list 			= 	DB::table('admin_modules')->pluck('title','id')->toArray();
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends($request->all())->render();
		return View::make('admin.Acl.index', compact('result' ,'searchVariable','sortBy','order','query_string','parent_list'));
	} 
 
	public function add(){
		$parent_list = DB::table('admin_modules')/* ->where('admin_modules.type',1) */->pluck('title','id')->toArray();
		return View::make('admin.Acl.add',compact('parent_list'));
	} 
	 
	public function save(Request $request){
		//$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'title'				=> 'required',
					'path'				=> 'required',
					'module_order'		=> 'required',  
					//'type'		        => 'required',  
				) 
			); 
			if ($validator->fails()){
				 return Redirect::back()->withErrors($validator)->withInput();
			}else{  
				$obj 					=  array(); 
				$obj['parent_id']		=  (! empty($request->get('parent_id'))) ? trim(strip_tags($request->get('parent_id'),ALLOWED_TAGS_XSS)) : 0; 
				$obj['title']			=  trim(strip_tags($request->get('title'),ALLOWED_TAGS_XSS)); 
				$obj['path']			=  trim(strip_tags($request->get('path'),ALLOWED_TAGS_XSS)); 
				$obj['module_order']	=  trim(strip_tags($request->get('module_order'),ALLOWED_TAGS_XSS));
				$obj['icon']			=  $request->get('icon');  
				//$obj['type']	        =  $request->get('type');
				Acl::create($obj); 
				
				$admin_modules	=	$this->buildTree(0);
				Session::put('admin_modules',$admin_modules);
				Session::flash('success',trans("Module added successfully"));
				return Redirect::route('Acl.index');
			}
		}
	} 
	 
	public function edit($Id = 0){
		$model	=	Acl::find($Id); 
		if(empty($model)){
			return Redirect::back();
		}
		$model			=	Acl::with('get_admin_module_action')->where('id',$Id)->first();
		$parent_list	= 	DB::table('admin_modules')/* ->where('admin_modules.type',1) */->where('parent_id','!=',$Id)->pluck('title','id')->toArray();
		return View::make('admin.Acl.edit',compact('parent_list','Id','model'));
	}  
	 
	public function update($userId = 0,Request $request){
		//$array= array($request->title, $request->path); 	
		//$request->replace($this->arrayStripTags($array));
		$thisData					=	$request->all();
		$validator 					=	Validator::make(
			$request->all(),
			array(
				'title'				=> 'required',
				'path'				=> 'required',
				'module_order'		=> 'required',  
				//'type'		        => 'required',  
			) 
		); 
		if ($validator->fails()){	
			return Redirect::route('Acl.edit',$userId)
				->withErrors($validator)->withInput();
		}else{
			
			$data 					=  array(); 
			$data['parent_id']		=  (!empty($request->get('parent_id'))) ? trim(strip_tags($request->get('parent_id'),ALLOWED_TAGS_XSS)) : 0; 
			$data['title']			=  trim(strip_tags($request->get('title'),ALLOWED_TAGS_XSS)); 
			$data['path']			=  trim(strip_tags($request->get('path'),ALLOWED_TAGS_XSS)); 
			$data['module_order']	=  trim(strip_tags($request->get('module_order'),ALLOWED_TAGS_XSS));
			$data['icon']			=  $request->get('icon');
			//$obj['type']	        =  $request->get('type');
			
			/* $admin_modules	=	$this->buildTree(0);
			Session::put('admin_modules',$admin_modules); */
			AclAdminAction::where('admin_module_id',$userId)->delete();
			if(isset($thisData['data']) && !empty($thisData['data'])){
				foreach($thisData['data'] as $record){
					if(!empty($record['name']) && !empty($record['function_name'])){
						$obj 					= array(); 
						$obj['admin_module_id']	=  $userId; 
						$obj['name']			=  $record['name']; 
						$obj['function_name']	=  $record['function_name'];  
						AclAdminAction::create($obj);
					}
				}
			}
			
			
			if(isset($userId)){ 
				$obj				=	Acl::findorFail($userId);
				$obj->fill($data)->save();
			}  
			return Redirect::route('Acl.index')->with('success',trans("Module updated successfully"));
		}
	} 	
	 
	public function status($userId = 0, $userStatus = 0){
		
		
		if($userStatus == 1){
			$statusMessage	=	trans("Module deactivated successfully");
			Acl::where('id',$userId)->update(array('is_active'=>1));
		}else{
			$statusMessage	=	trans("Module activated successfully");
			Acl::where('id',$userId)->update(array('is_active'=>0));
		}
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::route('Acl.index');
	}  
	
	public function deleteAcl($userId = 0){
		$userDetails	=	Acl::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route('Acl.index');
		}		
		 Acl::where('parent_id',$userId)->delete();
		 Acl::where('id',$userId)->delete();
		 Session::flash('flash_notice',trans("Module removed successfully"));
		return Redirect::route('Acl.index');
	} 
	 
	public function addMoreRow(Request $request){
		$counter	=	$request->get('counter'); 
		return View::make('admin.Acl.add_more',compact('counter'));
	}
 
	public function userPermission(){
		if(Auth::check() && Auth::id() != ADMIN_ID){
			Session::flash("flash_notice",trans("You are not allowed to access this page."));
			return Redirect::route('myaccount');
		}
		$seachData	=	array();
		$userList	=	User::where('is_active',1)
						->whereIn('user_role_id',array(SUPER_ADMIN_ROLE_ID))
						->where('is_deleted',0)
						->where('users.is_deleted',0)
						->pluck("full_name","id")->toArray(); 
		 
		$aclModules	=	array();
		if(!empty(Input::all())){
			$seachData	=	Input::all('');
			if(isset($seachData['id']) && !empty($seachData['id'])){ 
				$user_id		=	(!empty($seachData['id']))?$seachData['id']:'';
				$userData       =   User::find($user_id);
				if($userData){
					$user_role_id   =   $userData->user_role_id;
					
					if($user_role_id == 1){
						$type = 1;
					}else{
						$type = 0;
					}
					
					$aclModules		=	Acl::select('title','id',DB::Raw("(select is_active from user_permissions where user_id = $user_id AND admin_module_id = admin_modules.id LIMIT 1) as active"))->where('type',$type)->where('is_active',1)->where('parent_id',0)->get(); 
					
				if(!empty($aclModules)){
					foreach($aclModules as &$aclModule){
						$aclModule['sub_module']	=	Acl::where('is_active',1)->where('type',$type)->where('parent_id',$aclModule->id)->select('title','id')->get();
						$module_ids			=	array();
						if(!empty($aclModule['sub_module'])){
							foreach($aclModule['sub_module'] as &$module){
								$module_id		=		$module->id;
								$module_ids[$module->id]		=		$module->id;
								$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $module_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();
								 
								 
							}
						}
						 
						$newArray	=	array(); 
						//$module_id				=	$module->id;
						$aclModule['extModule']	=	Acl::where('is_active',1)->where('type',$type)->whereIn('parent_id',$module_ids)->select('title','id')->get();
				 
						if(!empty($aclModule['extModule'])){ 
							foreach($aclModule['extModule'] as &$record){
								$action_id			=	$record->id;
								$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get(); 
							}
						}
						
						if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
							$action_id			=	$aclModule->id;
							$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();  
						} 
					}
				} 
				}else{
					return Redirect::Back();
				}
			}else{
				return Redirect::Back();
			}  
		}
		return View::make('admin.Acl.user_permission',compact('userList','aclModules','seachData'));
	} 
	
	public function saveUserPermission(Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$formData	=	$request->all();
		if(!empty($formData['data'])){
			UserPermission::where('user_id',$formData['user_id'])->delete();
			UserPermissionAction::where('user_id',$formData['user_id'])->delete();
			foreach($formData['data'] as $data){ 
				$obj 					= 	array(); 
				$obj['user_id']			=  !empty($formData['user_id'])?$formData['user_id']:0;  
				$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
				$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
				$userpermissiondata 	=   UserPermission::create($obj);
				$userpermissionID		=	$userpermissiondata->id;
			
				if(isset($data['module']) && !empty($data['module'])){
					foreach($data['module'] as $subModule){ 
						$objData 							= array(); 
						$objData['user_id']					=  !empty($formData['user_id'])?$formData['user_id']:0;  
						$objData['user_permission_id']		=  $userpermissionID; 
						$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
						$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
						$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
						$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
						UserPermissionAction::create($objData);
					}
				}
			} 
			//User::where('id',$formData['user_id'])->update(['is_login'=>1]);
			Session::flash('success',trans("Permissions saved successfully"));
			return "1";die;
		}
	} 
	
	public function clearCache(){
		Artisan::call('cache:clear');
		echo "Cache is cleared";die;
	}
} 
