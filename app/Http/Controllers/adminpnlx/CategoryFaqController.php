<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController; 
use App\Model\Category;
use App\Model\CategoryDescription;
use App\Model\CategoryFaq;
use App\Model\CategoryFaqDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* CategoryController Controller
*
* Add your methods in the class below
*
*/
class CategoryFaqController extends BaseController {

	public $model		=	'CategoryFaq';
	public $sectionName	=	'Faqs';
	public $sectionNameSingular	=	'Faq';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
	}
	 
	/**
	* Function for display all categorys 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request, $categoryId){  
        $CategoryInfo					=	Category::findorFail($categoryId);
		if(empty($CategoryInfo)) {
			return Redirect::back();
        }
		$DB					=	CategoryFaq::where('category_faqs.category_id', $categoryId);
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		if (($request->all())) {
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
				if($fieldValue != ""){
					if($fieldName == "is_active"){
						$DB->where("category_faqs.is_active",$fieldValue);
					}
					if($fieldName == "question"){
						$DB->where("category_faqs.question",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "answer"){
						$DB->where("category_faqs.answer",'like','%'.$fieldValue.'%');
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
        $results->appends($request->all())->render();
        
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string','categoryId'));
	}// end index()
	
	/**
	* Function for add new category
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add($categoryId){  
        $CategoryInfo					=	Category::findorFail($categoryId);
		if(empty($CategoryInfo)) {
			return Redirect::back();
        }
        $languages					=	DB::select("CALL GetAcitveLanguages(1)");
        if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		
		return  View::make("admin.$this->model.add",compact('languages' ,'language_code','categoryId'));
	}// end add()
	
/**
* Function for save new category
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request, $categoryId){
        $CategoryInfo					=	Category::findorFail($categoryId);
		if(empty($CategoryInfo)) {
			return Redirect::back();
        }
		$request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
                'question' 	                      => $dafaultLanguageArray['question'],
                'answer' 	                      => $dafaultLanguageArray['answer'],
				
			),
			array(
				'question'                        => 'required',
				'answer' 			              => 'required',
			),
			array(
				 "question.required"	            	=>	trans("The question field is required."),
				 "answer.required"				        =>	trans("The answer field is required."),
			)

		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj = new CategoryFaq;
			$obj->category_id               = $categoryId;
			$obj->question   	            = $dafaultLanguageArray['question'];
			$obj->answer   	                = $dafaultLanguageArray['answer'];
			
			$objSave				            = $obj->save();
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
			}
			$last_id			=	$obj->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$CategoryfaqDescription_obj					=  new CategoryFaqDescription();
				$CategoryfaqDescription_obj->language_id	=	$language_id;
				$CategoryfaqDescription_obj->parent_id		=	$last_id;
				$CategoryfaqDescription_obj->question	    =	$value['question'];	
				$CategoryfaqDescription_obj->answer	        =	$value['answer'];	
				$CategoryfaqDescription_obj->save();
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
			return Redirect::route($this->model.".index",$obj->category_id);
		}
	}//end save()
	

	
	
	/**
	* Function for display page for edit category
	*
	* @param $modelId id  of category
	*
	* @return view page. 
	*/
	public function edit($modelId = 0){
        $model				=	CategoryFaq::find($modelId);
        $categoryId				=	$model->category_id;
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$CategoryFaqDescription	=	CategoryFaqDescription::where('parent_id', '=',  $modelId)->get();
        $multiLanguage		 	=	array();
        if(!empty($CategoryFaqDescription)){
			foreach($CategoryFaqDescription as $description) {
				$multiLanguage[$description->language_id]['question']			=	$description->question;			
				$multiLanguage[$description->language_id]['answer']			    =	$description->answer;			
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
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage,"categoryId"=>$categoryId,"modelId"=>$modelId));
	} // end edit()
	
	
	/**
	* Function for update category 
	*
	* @param $modelId as id of category 
	*
	* @return redirect page. 
	*/
	function update(Request $request , $modelId){
		$model					=	CategoryFaq::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
                'question' 	                      => $dafaultLanguageArray['question'],
                'answer' 	                      => $dafaultLanguageArray['answer'],
				
			),
			array(
				'question'                        => 'required',
				'answer' 			              => 'required',
			),
			array(
				 "question.required"	            	=>	trans("The question field is required."),
				 "answer.required"				        =>	trans("The answer field is required."),
			)
		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
            $obj                = $model;
            
			$obj->question   	            = $dafaultLanguageArray['question'];
			$obj->answer   	                = $dafaultLanguageArray['answer'];
		
			$objSave				            = $obj->save();
            
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
            }
            
            $last_id			=	$obj->id;
            
            CategoryFaqDescription::where('parent_id', '=', $last_id)->delete();
            foreach ($thisData['data'] as $language_id => $value) {
				$CategoryfaqDescription_obj					=  new CategoryFaqDescription();
				$CategoryfaqDescription_obj->language_id	=	$language_id;
				$CategoryfaqDescription_obj->parent_id		=	$last_id;
				$CategoryfaqDescription_obj->question	    =	$value['question'];	
				$CategoryfaqDescription_obj->answer	        =	$value['answer'];	
				$CategoryfaqDescription_obj->save();
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
			return Redirect::route($this->model.".index",$obj->category_id);
		}
	}// end update()
	

	/**
	* Function for update status
	*
	* @param $modelId as id of category 
	* @param $status as status of category 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('category_faqs',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()

	/**
	* Function for mark a couse as deleted 
	*
	* @param $userId as id of couse
	*
	* @return redirect page. 
	*/
	public function delete($id = 0){
		$model	=	CategoryFaq::find($id); 
		if(empty($model)) {
			return Redirect::back();
		}
		if($id){
			CategoryFaq::where('id',$id)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	} // end delete()
	
	
    
    public function view($modelId = 0){
		$model				=	CategoryFaq::where('id',"$modelId")->select('category_faqs.*')->first(); 
		$categoryId         = $model->category_id;						
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",array('model' => $model,'modelId' => $modelId,'categoryId' => $categoryId));
	}

}// end CategoryController
