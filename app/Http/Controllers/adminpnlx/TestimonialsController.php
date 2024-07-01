<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController; 
use App\Model\Testimonial;
use App\Model\TestimonialDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* ReadyMadeProductsController Controller
*
* Add your methods in the class below
*
*/
class TestimonialsController extends BaseController {

	public $model		=	'Testimonials';
	public $sectionName	=	'Testimonials';
	public $sectionNameSingular	=	'Testimonial';
	
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
		$DB					=	Testimonial::query();
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
					if($fieldName == "name"){
						$DB->where("testimonials.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("testimonials.is_active",$fieldValue);
					}                  
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results = $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
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
                'name'                 	     => $dafaultLanguageArray['name'],
                'description'                => $dafaultLanguageArray['description'],
				'image' 		             => $request->file('image'),
			),
			array(
                'description' 			        => 'required',  
                'name' 			                => 'required',
				'image' 			            =>  'required:mimes:'.IMAGE_EXTENSION,
			),
			array(
				"image.required"				=>	trans("The image field is required."),
				 "image.mimes"				    =>	trans("The image should be in jpg, jpeg, png format."),
				 "name.required"				=>	trans("The name field is required."),			
				 "description.required"			=>	trans("The description field is required."),
			)
		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj = new Testimonial;
            $obj->description   			= $dafaultLanguageArray['description'];
            $obj->name   					= $dafaultLanguageArray['name'];
			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-testimonials.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	TESTIMONIAL_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$obj->image	=	$folderName.$fileName;
				}
			}
			$objSave				= $obj->save();
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
			}
			$last_id			=	$obj->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$TestimonialDescription_obj					=  new TestimonialDescription();
				$TestimonialDescription_obj->language_id	=	$language_id;
				$TestimonialDescription_obj->parent_id		=	$last_id;
                $TestimonialDescription_obj->name			=	$value['name'];
                $TestimonialDescription_obj->description	=	$value['description'];		
				$TestimonialDescription_obj->save();
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
			return Redirect::route($this->model.".index");
		}
	}//end save()
	
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
		$this->_update_all_status('testimonials',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit category
	*
	* @param $modelId id  of category
	*
	* @return view page. 
	*/
	public function edit($modelId = 0){
		$model				=	Testimonial::find($modelId);
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$TestimonialDescription	=	TestimonialDescription::where('parent_id', '=',  $modelId)->get();
        $multiLanguage		 	=	array();
        if(!empty($TestimonialDescription)){
			foreach($TestimonialDescription as $description) {
                $multiLanguage[$description->language_id]['name']			        =	$description->name;	
                $multiLanguage[$description->language_id]['description']			=	$description->description;			
				
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
       
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage));
	} // end edit()
	
	
	/**
	* Function for update category 
	*
	* @param $modelId as id of category 
	*
	* @return redirect page. 
	*/
	function update(Request $request , $modelId){
		$model					=	Testimonial::findorFail($modelId);
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
                'name'                 	     => $dafaultLanguageArray['name'],
                'description'                => $dafaultLanguageArray['description'],
				'image' 		             => $request->file('image'),
			),
			array(							
                'description' 			        => 'required',    
                'name' 			                => 'required',
				'image' 			            =>  'nullable:mimes:'.IMAGE_EXTENSION,
			),
			array(
				 "image.mimes"				    =>	trans("The image should be in jpg, jpeg, png format."),
				 "name.required"				=>	trans("The name field is required."),
				 "description.required"			=>	trans("The description field is required."),	
			)
		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj = $model;
            $obj->description   			= $dafaultLanguageArray['description'];
            $obj->name   					= $dafaultLanguageArray['name'];
			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-testimonials.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	TESTIMONIAL_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$obj->image	=	$folderName.$fileName;
				}
			}
			$objSave				= $obj->save();
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
			}
			$last_id			=	$obj->id;
			  TestimonialDescription::where('parent_id', '=', $last_id)->delete();
			foreach ($thisData['data'] as $language_id => $value) {
				$TestimonialDescription_obj					=  new TestimonialDescription();
				$TestimonialDescription_obj->language_id	=	$language_id;
				$TestimonialDescription_obj->parent_id		=	$last_id;
                $TestimonialDescription_obj->name			=	$value['name'];
                $TestimonialDescription_obj->description	=	$value['description'];		
				$TestimonialDescription_obj->save();
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
			return Redirect::route($this->model.".index");
		}
	}// end update()
	
	
	// /**
	// * Function for mark a couse as deleted 
	// *
	// * @param $userId as id of couse
	// *
	// * @return redirect page. 
	// */
	// public function delete($id = 0){
	// 	$model	=	Testimonial::find($id); 
	// 	if(empty($model)) {
	// 		return Redirect::back();
	// 	}
	// 	if($id){
	// 		Testimonial::where('id',$id)->update(array('is_deleted'=>1));
	// 		Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
	// 	}
	// 	return Redirect::back();
	// } // end delete()
	
    
    
    public function view($modelId = 0){
        $model				=	Testimonial::where('id',"$modelId")->select('testimonials.*')->first(); 
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}

		return  View::make("admin.$this->model.view",array('model' => $model));
	}
	
	
	
    
    
	/**
	* Function for copy a Testimonial
	*
	* @param $id as id of couse
	*
	* @return redirect page. 
	*/
	
	 
}// end ReadyMadeProductController
