<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Lookup;
use App\Model\LookupDescription;
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
	class LookupsController extends BaseController {
		
		public $model				=	'Lookups';
		public $sectionName			=	'Lookups';
		public $sectionNameSingular	=	'Lookup';

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
	public function listLookups($type='',Request $request) {
		if(empty($type)) {
			return Redirect::to('adminpnlx/dashboard');
		}
		$DB				=	Lookup::query()->where('lookup_type',$type);
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
		if ($request->all()){
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
				if($fieldName == "code"){
					$DB->where("lookups.code",'like','%'.$fieldValue.'%');
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
		$results->appends($request->all())->render();
		
		return  View::make('admin.Lookups.index',compact('results','searchVariable','sortBy','order','type','query_string'));
	}// end listDropDown()
/**
* Function for display page  for add new DropDown  
*
* @param $type as category of dropdown 
*
* @return view page. 
*/
	public function addLookups($type=''){
		$languages					=	DB::select("CALL GetAcitveLanguages(1)");
        if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];

		return  View::make('admin.Lookups.add',compact('type','languages' ,'language_code'));
	} //end addDropDown()
/**
* Function for save added DropDown page
*
* @param null
*
* @return redirect page. 
*/
	function saveLookups($type='',Request $request){
		// print_r($request->all());die;
		$request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		
		/* $imageValidation='';
		if($type=='interest')
		{
			$imageValidation='';
		}else{
			$imageValidation='required|mimes:'.IMAGE_EXTENSION;
		} */

		$validator = Validator::make(
			array(
				'code' 			=>  $dafaultLanguageArray['code'],
				//'lookup_type'	=>	$type,
				//'image'         =>  $request->file('image'),
				
			),	
			array(
				'code' 			=> 'required',
				//'image'			=>	$imageValidation,				
			),
			array(
				"code.required"				=> "The title field is required.",
				//"image.required"			=>	trans("The image field is required"),
				//"image.mimes"				=>	trans("The image must be in: 'jpeg, jpg, png, gif, bmp.'"),				
			)
		);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/lookups-manager/add-lookups/'.$type)
				->withErrors($validator)->withInput();
		}else{
			// dd($dafaultLanguageArray['code']);die;
			$lookups = new Lookup;
			$lookups->code    						= 	$dafaultLanguageArray['code'];
			$lookups->lookup_type    				= 	$type;

		/* 	if($request->hasFile('image')){
				$extension 			=	$request->file('image')->getClientOriginalExtension();
				$fileName			=	time().'-image.'.$extension;
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	CRIME_CATEGORY_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$lookups->image	=	$folderName.$fileName;
				}
			} */

			if(isset($request->is_featute) && $request->is_featute == 1){
				$lookups->optional   = 	$request->is_featute;
			}else{
				$lookups->optional   = 	0;
			}

			$lookups->save(); 
			$lookupsId								=	$lookups->id;

			if(!$lookupsId){
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}

			foreach ($thisData['data'] as $language_id => $value) {
				$lookupDescription_obj					=  new LookupDescription();
				$lookupDescription_obj->language_id		=	$language_id;
				$lookupDescription_obj->parent_id		=	$lookupsId;
				$lookupDescription_obj->code	 	    =	$value['code'];	
				$lookupDescription_obj->save();
			}



			Session::flash('flash_notice', trans(ucwords(str_replace("-"," ",$type)).' added successfully')); 
			return Redirect::to('adminpnlx/lookups-manager/'.$type);
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
	public function editLookups($Id,$type){
		$lookups				=	Lookup::find($Id);
		if(empty($lookups)) {
			return Redirect::to('adminpnlx/lookups-manager/'.$type);
		}
		$LookupDescription	=	LookupDescription::where('parent_id', '=',  $Id)->get();
        $multiLanguage		 	=	array();
        if(!empty($LookupDescription)){
			foreach($LookupDescription as $description) {
				$multiLanguage[$description->language_id]['code']				=	$description->code;				
			}
		}
        $languages			=	DB::select("CALL GetAcitveLanguages(1)");
        if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		return  View::make('admin.Lookups.edit',array('lookups' => $lookups,'type'=>$type,'languages' => $languages,'language_code' => $language_code,'multiLanguage' => $multiLanguage));
	}// end editDropDown()
/**
* Function for update DropDown 
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/
	function updateLookups($Id,$type='',Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$this_data										=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$this_data['data'][$language_code];

		$lookups 										= 	Lookup:: find($Id);
		$validator 										= 	Validator::make(
			array(
				'code' 		=> $dafaultLanguageArray['code'],
			),
			array(
				'code' 		=> 'required',
				//'image'     => 'nullable|mimes:'.IMAGE_EXTENSION,
			),
			array(
				"code.required"	=> "The title field is required.",	
				//"image.required"			=>	trans("The image field is required"),
				//"image.mimes"				=>	trans("The image must be in: 'jpeg, jpg, png, gif, bmp.'"),			
			)
		);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/lookups-manager/edit-lookups/'.$Id.'/'.$type)
				->withErrors($validator)->withInput();
		}else{
			$lookups->code								= 	$dafaultLanguageArray['code'];

			/* if($request->hasFile('image')){ 
				if(File::exists(CRIME_CATEGORY_IMAGE_ROOT_PATH.$lookups->image)) {
					File::delete(CRIME_CATEGORY_IMAGE_ROOT_PATH.$lookups->image);	
				}
				$extension 		=	$request->file('image')->getClientOriginalExtension();
				$fileName		=	time().'-image.'.$extension;
				$folderName     = 	strtoupper(date('M'). date('Y'))."/";
				$folderPath		=	CRIME_CATEGORY_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$lookups->image=	$folderName.$fileName;
				}
			} */

			if(isset($request->is_featute) && $request->is_featute == 1){
				$lookups->optional   = 	$request->is_featute;
			}else{
				$lookups->optional   = 	0;
			}

			$lookups->save();
			$lookupsId		=	$lookups->id;
			$lookupsId		=	$Id;

			if(!$lookupsId){
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}

			LookupDescription::where('parent_id', '=', $lookupsId)->delete();
            foreach ($this_data['data'] as $language_id => $value) {
				$lookupDescription_obj					=  new LookupDescription();
				$lookupDescription_obj->language_id		=	$language_id;
				$lookupDescription_obj->parent_id		=	$lookupsId;
				$lookupDescription_obj->code	 	    =	$value['code'];	
				$lookupDescription_obj->save();
			}

			Session::flash('flash_notice',trans(ucwords(str_replace("-"," ",$type))." updated successfully")); 
			return Redirect::intended('adminpnlx/lookups-manager/'.$type);
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
	public function updateLookupStatus($Id = 0, $Status = 0,$type=''){
		if($Status == 0	){
			$statusMessage	=	trans(ucwords(str_replace("-"," ",$type))." deactivated successfully");
		}else{
			$statusMessage	=	trans(ucwords(str_replace("-"," ",$type))." activated successfully");
		}
		$this->_update_all_status('lookups',$Id,$Status);
		
		Session::flash('flash_notice',$statusMessage); 
		return Redirect::to('adminpnlx/lookups-manager/'.$type);
	}// end updateDropDownStatus()
/**
* Function for delete DropDown 
*
* @param $Id as id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/	
	public function deleteLookups($Id = 0,$type=''){
		$lookups					=	Lookup::find($Id) ;
		
		if(!empty($lookups)){
			$this->_delete_table_entry('lookups',$Id,'id');
			Session::flash('flash_notice', trans(ucwords(str_replace("-"," ",$type))." removed successfully"));  
		}else{
			Session::flash('error', trans("Invalid url"));  
		}
		return Redirect::to('adminpnlx/lookups-manager/'.$type);
	}// end deleteDropDown()

}// end DropDownController
