<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController; 
use App\Model\Faq;
use App\Model\FaqDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* FaqController Controller
*
* Add your methods in the class below
*
*/
class FaqController extends BaseController {

	public $model		=	'Faqs';
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
	public function index(Request $request){ 
		$DB					=	Faq::query();
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
						$DB->where("faqs.is_active",$fieldValue);
					}
					if($fieldName == "question"){
						$DB->where("faqs.question",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "answer"){
						$DB->where("faqs.answer",'like','%'.$fieldValue.'%');
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
        
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()
	
	/**
	* Function for add new category
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
* Function for save new category
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){
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
			$obj = new Faq;
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
				$faqDescription_obj					=  new FaqDescription();
				$faqDescription_obj->language_id	=	$language_id;
				$faqDescription_obj->parent_id		=	$last_id;
				$faqDescription_obj->question	    =	$value['question'];	
				$faqDescription_obj->answer	        =	$value['answer'];	
				$faqDescription_obj->save();
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
        $model				=	Faq::find($modelId);
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$FaqDescription	=	FaqDescription::where('parent_id', '=',  $modelId)->get();
        $multiLanguage		 	=	array();
        if(!empty($FaqDescription)){
			foreach($FaqDescription as $description) {
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
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage,"modelId"=>$modelId));
	} // end edit()
	
	
	/**
	* Function for update category 
	*
	* @param $modelId as id of category 
	*
	* @return redirect page. 
	*/
	function update(Request $request , $modelId){
		$model					=	Faq::findorFail($modelId);
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
            
            FaqDescription::where('parent_id', '=', $last_id)->delete();
            foreach ($thisData['data'] as $language_id => $value) {
				$faqDescription_obj					=  new FaqDescription();
				$faqDescription_obj->language_id	=	$language_id;
				$faqDescription_obj->parent_id		=	$last_id;
				$faqDescription_obj->question	    =	$value['question'];	
				$faqDescription_obj->answer	        =	$value['answer'];	
				$faqDescription_obj->save();
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
		$model	=	Faq::find($id); 
		if(empty($model)) {
			return Redirect::back();
		}
		if($id){
			Faq::where('id',$id)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	} // end delete()
    
}// end CategoryController
