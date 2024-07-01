<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\DropDown;
use App\Model\Language;
use App\Model\DropDownDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
/**
* DropDownController Controller
*
* Add your methods in the class below
*
* This file will render views from views/dropdown
*/
	class DropDownController extends BaseController {
		
		public $model				=	'DropDown';
		public $sectionName			=	'Drivers';
		public $sectionNameSingular	=	'Driver';

		public function __construct(Request $request) {
			parent::__construct();
			View::share('modelName',$this->model);
			View::share('sectionName',$this->sectionName);
			View::share('sectionNameSingular',$this->sectionNameSingular);

			$this->request = $request;
		}
/**
* Function for display all DropDown    
*
* @param $type as category of dropdown 
*
* @return view page. 
*/
	public function listDropDown($type='',Request $request) {
		if(empty($type)) {
			return Redirect::to('adminpnlx/dashboard');
		}
		$DB				=	DropDown::query()->where('dropdown_type',$type);
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
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
			/* echo  "<pre>";
			print_r($searchData);die; */
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue) || $fieldValue==0){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		
		$sortBy = ($request->get('sortBy')) ? $request->get('sortBy') : 'updated_at';
	    $order  = ($request->get('order')) ? $request->get('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends($request->all())->render();
		
		return  View::make('admin.dropdown.index',compact('result','searchVariable','sortBy','order','type','query_string'));
	}// end listDropDown()
/**
* Function for display page  for add new DropDown  
*
* @param $type as category of dropdown 
*
* @return view page. 
*/
	public function addDropDown($type=''){
		$languages			=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		
		return  View::make('admin.dropdown.add',compact('languages' ,'language_code','type'));
	} //end addDropDown()
/**
* Function for save added DropDown page
*
* @param null
*
* @return redirect page. 
*/
	function saveDropDown($type='',Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$thisData										=	$request->all();
		$default_language								=	Config::get('default_language');
		$language_code 									=   $default_language['language_code'];
		$dafaultLanguageArray							=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'name' 			=>  $dafaultLanguageArray['name'],
				'dropdown_type'	=>	$type,
				
			),	
			array(
				'name' 			=> 'required',
				
			),
			array(
				"name.required"	=> "The name field is required.",				
			)
		);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/dropdown-manager/add-dropdown/'.$type)
				->withErrors($validator)->withInput();
		}else{
			$dropdown = new DropDown;
			$dropdown->slug    							= 	$this->getSlugWithoutModel($type ,'slug', 'dropdown_managers');
			$dropdown->name    							= 	$dafaultLanguageArray['name'];
			$dropdown->dropdown_type    				= 	$type;
			$dropdown->save(); 
			$dropdownId									=	$dropdown->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$modelDropDownDescription				=  new DropDownDescription();
				$modelDropDownDescription->language_id	=	$language_id;
				$modelDropDownDescription->parent_id	=	$dropdownId;
				$modelDropDownDescription->name			=	$value['name'];		
				$modelDropDownDescription->save();
			}
			Session::flash('flash_notice', trans(ucfirst($type).' added successfully')); 
			return Redirect::to('adminpnlx/dropdown-manager/'.$type);
		}
	}//end saveDropDown()
/**
* Function for display page  for edit DropDown page
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return view page. 
*/	
	public function editDropDown($Id,$type){
		$dropdown				=	DropDown::find($Id);
		if(empty($dropdown)) {
			return Redirect::to('adminpnlx/dropdown-manager/'.$type);
		}
		$dropdownDescription	=	DropDownDescription::where('parent_id', '=',  $Id)->get();
		$multiLanguage		 	=	array();
		if(!empty($dropdownDescription)){
			foreach($dropdownDescription as $description) {
				$multiLanguage[$description->language_id]['name']			=	$description->name;				
			}
		}
		$languages				=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language		=	Config::get('default_language');
		$language_code 			=   $default_language['language_code'];
		return  View::make('admin.dropdown.edit',array('languages' => $languages,'language_code' => $language_code,'dropdown' => $dropdown,'multiLanguage' => $multiLanguage,'type'=>$type));
	}// end editDropDown()
/**
* Function for update DropDown 
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/
	function updateDropDown($Id,$type='',Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$this_data										=	$request->all();
		$dropdown 										= 	DropDown:: find($Id);
		$default_language								=	Config::get('default_language');
		$language_code 									=   $default_language['language_code'];
		$dafaultLanguageArray							=	$this_data['data'][$language_code];
		$validator 										= 	Validator::make(
			array(
				'name' 		=> $dafaultLanguageArray['name'],
			),
			array(
				'name' 		=> 'required',
			),
			array(
				"name.required"	=> "The name field is required.",				
			)
		);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/dropdown-manager/edit-dropdown/'.$Id.'/'.$type)
				->withErrors($validator)->withInput();
		}else{
			$dropdown->name								= 	$dafaultLanguageArray['name'];
			$dropdown->save();
			$dropdownId		=	$dropdown->id;
			$dropdownId		=	$Id;
			DropDownDescription::where('parent_id', '=', $Id)->delete();
			foreach ($this_data['data'] as $language_id => $value) {
				$modelDropDownDescription				=  new DropDownDescription();
				$modelDropDownDescription->language_id	=	$language_id;
				$modelDropDownDescription->name			=	$value['name'];	
				$modelDropDownDescription->parent_id	=	$dropdownId;
				$modelDropDownDescription->save();					
			}
			Session::flash('flash_notice',trans(ucfirst($type)." updated successfully")); 
			return Redirect::intended('adminpnlx/dropdown-manager/'.$type);
		}
	}// end updateDropDown()
/**
* Function for update DropDown  status
*
* @param $Id as id of DropDown 
* @param $Status as status of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/	
	public function updateDropDownStatus($Id = 0, $Status = 0,$type=''){
		if($Status == 0	){
			$statusMessage	=	trans(ucfirst($type)." deactivated successfully");
		}else{
			$statusMessage	=	trans(ucfirst($type)." activated successfully");
		}
		$this->_update_all_status('dropdown_managers',$Id,$Status);
		
		/* if($Status == 1){
			$message				=	trans("messages.master.master_activate_message");
		}else{
			$message				=	trans("messages.master.master_deactivate_message");
		}
		$model						=	DropDown::find($Id);
		$model->is_active			=	$Status;
		$model->save(); */
		Session::flash('flash_notice',$statusMessage); 
		return Redirect::to('adminpnlx/dropdown-manager/'.$type);
	}// end updateDropDownStatus()
/**
* Function for delete DropDown 
*
* @param $Id as id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/	
	public function deleteDropDown($Id = 0,$type=''){
		$dropdown					=	DropDown::find($Id) ;
		//$dropdown->description()->delete();
		/* if($type=='faq'){
			$dropdown->faq()->delete();
		} */
		//$dropdown->delete();
		if(!empty($dropdown)){
			$this->_delete_table_entry('dropdown_managers',$Id,'id');
			$this->_delete_table_entry('dropdown_manager_descriptions',$Id,'parent_id');
			Session::flash('flash_notice', trans(ucfirst($type)." removed successfully"));  
		}else{
			Session::flash('error', trans("Invalid url"));  
		}
		return Redirect::to('adminpnlx/dropdown-manager/'.$type);
	}// end deleteDropDown()
/**
* Function for multiple delete
*
* @param $type as type of dropdown
*
* @return redirect page. 
*/
 	public function performMultipleAction($type = 0){
		if(Request::ajax()){
			$actionType 			= (($request->get('type'))) ? $request->get('type') : '';
			if(!empty($actionType) && !empty($request->get('ids'))){
				if($actionType	==	'delete'){
					$dropdown		=	DropDown::whereIn('id', $request->get('ids'));
					$dropdown->description()->delete();
					/* if($type=='faq'){
						$dropdown->faq()->delete();
					} */
					$dropdown->delete();
				}
				Session::flash('flash_notice', trans("messages.user_management.action_performed_message")); 
			}
		}
	}//end performMultipleAction()
}// end DropDownController
