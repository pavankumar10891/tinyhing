<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Cms;
use App\Model\CmsDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* CmspagesController Controller
*
* Add your methods in the class below
*
*/
class CmspagesController extends BaseController {

	public $model				=	'Cms';
	public $sectionName			=	'Cms Pages';
	public $sectionNameSingular	=	'Cms Page';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all cms_pages 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Cms::query();
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
			/* if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('cms_pages.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('cms_pages.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('cms_pages.created_at','<=' ,[$dateE." 00:00:00"]); 						
			} */
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "page_name"){
						$DB->where("cms_pages.page_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "title"){
						$DB->where("cms_pages.title",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
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
	* Function for add new cms page
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		$languages					=	DB::select("CALL GetAcitveLanguages(1)");
		if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		return  View::make("admin.$this->model.add",compact('languages' ,'language_code'));
	}// end add()
	
/**
* Function for save new cms page
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){
		//$this->request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'page_name' 		=> $request->input('page_name'),
				'title' 			=> $dafaultLanguageArray['title'],
				'body' 				=> $dafaultLanguageArray['body'],
			),
			array(
				'page_name' 		=> 'required',
				'title' 			=> 'required',
				'body' 				=> 'required',
			),
			array(
				"page_name.required"				=>	trans("The page name field is required."),
				"title.required"			=>	trans("The page title field is required."),				
				"body.required"				=>	trans("The description field is required."),				
			)
		);
		
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{ 
			$obj 					= new Cms;
			$obj->page_name    		= $request->input('page_name');
			$obj->title   			= $dafaultLanguageArray['title'];
			$obj->body   			= $dafaultLanguageArray['body'];
			$obj->slug   			= $this->getSlug($dafaultLanguageArray['title'],'title','Cms');
			$obj->save();
			
			$lastId					=	$obj->id;
			if(!empty($thisData)){
				foreach ($thisData['data'] as $language_id => $value) {
					$subObj				=  new CmsDescription();
					$subObj->language_id	=	$language_id;
					$subObj->parent_id		=	$lastId;
					$subObj->title			=	$value['title'];		
					$subObj->body			=	$value['body'];		
					$subObj->save();
				}
			}
			Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
			return Redirect::route($this->model.".index");
		}
	}//end save()
	
	/**
	* Function for display page for edit cms page
	*
	* @param $modelId id  of cms page
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		$model	=	Cms::findorFail($modelId);
		
		$objDescription	=	CmsDescription::where('parent_id', '=',  $modelId)->get();
		$multiLanguage		 	=	array();
		if(!empty($objDescription)){
			foreach($objDescription as $description) {
				$multiLanguage[$description->language_id]['title']			=	$description->title;				
				$multiLanguage[$description->language_id]['body']			=	$description->body;				
			}
		}
		$languages					=	DB::select("CALL GetAcitveLanguages(1)");
		if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage));
	} // end edit()
	
	/**
	* Function for update cms page 
	*
	* @param $modelId as id of cms page 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model	=	Cms::findorFail($modelId);
		
		///$this->request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'page_name' 		=> $request->input('page_name'),
				'title' 			=> $dafaultLanguageArray['title'],
				'body' 				=> $dafaultLanguageArray['body'],
			),
			array(
				'page_name' 		=> 'required',
				'title' 			=> 'required',
				'body' 				=> 'required',
			),
			array(
				"page_name.required"		=>	trans("The page name field is required."),
				"title.required"			=>	trans("The page title field is required."),				
				"body.required"				=>	trans("The description field is required."),				
			)
		);
		
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{ 
			$obj 					= $model;
			$obj->page_name    		= $request->input('page_name');
			$obj->title   			= $dafaultLanguageArray['title'];
			$obj->body   			= $dafaultLanguageArray['body'];
			$obj->save();
			
			$lastId					=	$obj->id;
			CmsDescription::where("parent_id",$lastId)->delete();
			if(!empty($thisData)){
				foreach ($thisData['data'] as $language_id => $value) {
					$subObj				=  new CmsDescription();
					$subObj->language_id	=	$language_id;
					$subObj->parent_id		=	$lastId;
					$subObj->title			=	$value['title'];		
					$subObj->body			=	$value['body'];		
					$subObj->save();
				}
			}
			Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
			return Redirect::route($this->model.".index");
		}
	}// end update()
	
	/**
	* Function for update status
	*
	* @param $modelId as id of cms page 
	* @param $status as status of cms page 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		$model	=	Cms::findorFail($modelId);
		
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('cms_pages',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for update cms page   status
	*
	* @param $modelId as id of cms page  
	* @param $modelStatus as status of cms page  
	*
	* @return redirect page. 
	*/	
	public function delete($modelId = 0){
		$model	=	Cms::findorFail($modelId);
		CmsDescription::where("parent_id",$modelId)->delete();
		Cms::where('id',$modelId)->delete();
		Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Cms::findorFail($modelId);
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()
	
}// end CmspagesController
