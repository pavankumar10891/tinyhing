<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController; 
use App\Model\Category;
use App\Model\SubCategory;
use App\Model\SubCategoryQuestion;
use App\Model\SubCategoryDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* SubCategoryController Controller
*
* Add your methods in the class below
*
*/
class SubCategoryController extends BaseController {

	public $model		=	'SubCategory';
	public $sectionName	=	'Sub Categories';
	public $sectionNameSingular	=	'Sub Category';
	
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
		$DB					=	SubCategory::where('sub_categories.category_id', $categoryId);
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
					if($fieldName == "category_name"){
						$DB->where("sub_categories.category_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("sub_categories.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("sub_categories.is_deleted",0);
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
		
		return  View::make("admin.$this->model.add",compact('languages' ,'language_code',"categoryId"));
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
                'priority_order_fixed_price'      => $request->input('priority_order_fixed_price'),
                'priority_order_hourly_fee'       => $request->input('priority_order_hourly_fee'),
                'regular_day_order_hourly_fee'    => $request->input('regular_day_order_hourly_fee'),
				'working_day_order_price' 		  => $request->input('working_day_order_price'),
				'weekend_day_order_price' 		  => $request->input('weekend_day_order_price'),
				'category_order_by' 		      => $request->input('category_order_by'),
                'category_name' 	              => $dafaultLanguageArray['category_name'],
				'image' 		    	          => $request->file('image'),
				
			),
			array(
				'priority_order_fixed_price'     => 'required|numeric',
				'priority_order_hourly_fee' 	 => 'required|numeric',
                'regular_day_order_hourly_fee' 	 => 'required|numeric',
                'working_day_order_price' 		 => 'required|numeric',
                'weekend_day_order_price' 		 => 'required|numeric',
				'category_name' 		         => 'required',
				'category_order_by' 		     => 'required',
				'image'      				     => 'nullable|mimes:'.IMAGE_EXTENSION,
			),
			array(
				 "priority_order_fixed_price.required"	  	=>	trans("The fixed price field is required."),
				 "category_order_by.required"	   		    =>	trans("The category order field is required."),
				 "regular_day_order_hourly_fee.required"    =>	trans("The hourly fee field is required."),
				 "priority_order_hourly_fee.required"		=>	trans("The hourly fee field is required."),
				 "working_day_order_price.required"		    =>	trans("The working days price field is required."),
				 "weekend_day_order_price.required"		    =>	trans("The weekend price field is required."),
				 "category_name.required"			        =>	trans("The category name field is required."),
				 "image.mimes"								=>	trans("The image must be a file of type: 'jpeg, jpg, png, gif, bmp.'."),
				 "priority_order_fixed_price.numeric"	    =>	trans("The fixed price must be numeric."),
				 "priority_order_hourly_fee.numeric"	    =>	trans("The hourly fee must be numeric."),
				 "regular_day_order_hourly_fee.numeric"	    =>	trans("The hourly fee must be numeric."),
				 "working_day_order_price.numeric"		    =>	trans("The working days price must be numeric."),
				 "weekend_day_order_price.numeric"		    =>	trans("The weekend price must be numeric."),
				
			)

		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj = new SubCategory;
			$obj->slug      						= $this->getSlug($dafaultLanguageArray['category_name'],'category_name','SubCategory');
			$obj->category_id                       = $categoryId;
			$obj->priority_order_fixed_price        = $request->get('priority_order_fixed_price');
			$obj->regular_day_order_hourly_fee  	= $request->get('regular_day_order_hourly_fee');
			$obj->priority_order_hourly_fee    	    = $request->get('priority_order_hourly_fee');
			$obj->working_day_order_price       	= $request->input('working_day_order_price');
			$obj->weekend_day_order_price   	    = $request->input('weekend_day_order_price');
			$obj->category_order_by   		   	    = $request->input('category_order_by');
			$obj->category_name   	                = $dafaultLanguageArray['category_name'];
			$obj->description   	                = $dafaultLanguageArray['description'];

			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-Sub-categories.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	SUBCATEGORY_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$obj->image	=	$folderName.$fileName;
				}
			}
			
			
			$objSave				            = $obj->save();
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
			}
			$last_id			=	$obj->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$SubCategoryDescription_obj					=  new SubCategoryDescription();
				$SubCategoryDescription_obj->language_id	=	$language_id;
				$SubCategoryDescription_obj->parent_id		=	$last_id;
				$SubCategoryDescription_obj->category_name	=	$value['category_name'];	
				$SubCategoryDescription_obj->description	=	$value['description'];	
				$SubCategoryDescription_obj->save();
			}

			if(!empty($thisData['item_data'])){
				foreach($thisData['item_data'] as $key => $value) {
					if(!empty($value)){
						$questionOption 							= 	new SubCategoryQuestion;	
						$questionOption->sub_category_id			=	$last_id;
						$questionOption->question			    	=	$value["question"];
						
						$questionOption->save();	
					}
				}
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
			return Redirect::route($this->model.".index",$obj->category_id);
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
		$this->_update_all_status('sub_categories',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()

	
	
	public function changeStockStatus(Request $request){
		$id	=	$request->get("id");
		$value	=	$request->get("data");
		$record = Product::where('id',$id)->update(array('is_out_of_stock'=>$value));
	    die;
	}//end changeStockStatus


	
	/**
	* Function for display page for edit category
	*
	* @param $modelId id  of category
	*
	* @return view page. 
	*/
	public function edit($SubcategoryId){
        $model				=	SubCategory::find($SubcategoryId);
        $categoryId				=	$model->category_id;
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$SubCategoryDescription	=	SubCategoryDescription::where('parent_id', '=',  $SubcategoryId)->get();
        $multiLanguage		 	=	array();
        if(!empty($SubCategoryDescription)){
			foreach($SubCategoryDescription as $description) {
				$multiLanguage[$description->language_id]['category_name']			=	$description->category_name;			
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
		$QuestionData = DB::table('sub_category_questions')->where('sub_category_id',$SubcategoryId)->get()->toArray();					
       
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage,"SubcategoryId"=>$SubcategoryId,"categoryId"=>$categoryId,"QuestionData"=>$QuestionData));
	} // end edit()
	
	
	/**
	* Function for update category 
	*
	* @param $modelId as id of category 
	*
	* @return redirect page. 
	*/
	function update(Request $request , $modelId){
		$model					=	SubCategory::findorFail($modelId);
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
                'priority_order_fixed_price'      => $request->input('priority_order_fixed_price'),
                'priority_order_hourly_fee'       => $request->input('priority_order_hourly_fee'),
                'regular_day_order_hourly_fee'    => $request->input('regular_day_order_hourly_fee'),
				'working_day_order_price' 		  => $request->input('working_day_order_price'),
				'weekend_day_order_price' 		  => $request->input('weekend_day_order_price'),
				'category_order_by' 		      => $request->input('category_order_by'),
                'category_name' 	              => $dafaultLanguageArray['category_name'],
				'image' 		    	          => $request->file('image'),
				
			),
			array(
				'priority_order_fixed_price'     => 'required|numeric',
				'priority_order_hourly_fee' 	 => 'required|numeric',
                'regular_day_order_hourly_fee' 	 => 'required|numeric',
                'working_day_order_price' 		 => 'required|numeric',
                'weekend_day_order_price' 		 => 'required|numeric',
				'category_name' 		         => 'required',
				'category_order_by' 		     => 'required',
				'image'      				     => 'nullable|mimes:'.IMAGE_EXTENSION,
			),
			array(
				 "priority_order_fixed_price.required"	  	=>	trans("The fixed price field is required."),
				 "category_order_by.required"	   		    =>	trans("The category order field is required."),
				 "regular_day_order_hourly_fee.required"    =>	trans("The hourly fee field is required."),
				 "priority_order_hourly_fee.required"		=>	trans("The hourly fee field is required."),
				 "working_day_order_price.required"		    =>	trans("The working days price field is required."),
				 "weekend_day_order_price.required"		    =>	trans("The weekend price field is required."),
				 "category_name.required"			        =>	trans("The category name field is required."),
				 "image.mimes"								=>	trans("The image must be a file of type: 'jpeg, jpg, png, gif, bmp.'."),
				 "priority_order_fixed_price.numeric"	    =>	trans("The fixed price must be numeric."),
				 "priority_order_hourly_fee.numeric"	    =>	trans("The hourly fee must be numeric."),
				 "regular_day_order_hourly_fee.numeric"	    =>	trans("The hourly fee must be numeric."),
				 "working_day_order_price.numeric"		    =>	trans("The working days price must be numeric."),
				 "weekend_day_order_price.numeric"		    =>	trans("The weekend price must be numeric."),
				
			)
		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
            $obj                                    = $model;

			$obj->priority_order_fixed_price        = $request->get('priority_order_fixed_price');
			$obj->regular_day_order_hourly_fee  	= $request->get('regular_day_order_hourly_fee');
			$obj->priority_order_hourly_fee    	    = $request->get('priority_order_hourly_fee');
			$obj->working_day_order_price       	= $request->input('working_day_order_price');
			$obj->weekend_day_order_price   	    = $request->input('weekend_day_order_price');
			$obj->category_order_by   		   	    = $request->input('category_order_by');
			$obj->category_name   	                = $dafaultLanguageArray['category_name'];
			$obj->description   	                = $dafaultLanguageArray['description'];

			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-Sub-categories.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	SUBCATEGORY_IMAGE_ROOT_PATH.$folderName;
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				if($request->file('image')->move($folderPath, $fileName)){
					$obj->image	=	$folderName.$fileName;
				}
			}
			
			$objSave				            = $obj->save();
            
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route($this->model.".index");
            }
            
            $last_id			=	$obj->id;
            
            SubCategoryDescription::where('parent_id', '=', $last_id)->delete();
           foreach ($thisData['data'] as $language_id => $value) {
				$SubCategoryDescription_obj					=  new SubCategoryDescription();
				$SubCategoryDescription_obj->language_id	=	$language_id;
				$SubCategoryDescription_obj->parent_id		=	$last_id;
				$SubCategoryDescription_obj->category_name	=	$value['category_name'];	
				$SubCategoryDescription_obj->description	=	$value['description'];	
				$SubCategoryDescription_obj->save();
			}

			$ids=	array();
				if(!empty($thisData['item_data'])){
					foreach($thisData['item_data'] as $value) {
						if(!empty($value)){
							if(!empty($value["id"])){
								$SubCategoryQuestion	=	SubCategoryQuestion::where("id",$value["id"])->first();
								
								$questionOption 						= 	$SubCategoryQuestion;	
								$questionOption->sub_category_id		=	$last_id;
								$questionOption->question			    	=	$value["question"];
								
								$questionOption->save();	
								$ids[]	=	$questionOption->id;
							}else {
								$questionOption 						= 	new SubCategoryQuestion;	
								$questionOption->sub_category_id		=	$last_id;
								$questionOption->question			    	=	$value["question"];
								
								$questionOption->save();	
								$ids[]	=	$questionOption->id;
							}
							
							
						}
					}	
					SubCategoryQuestion::whereNotIn("id",$ids)->where("sub_category_id",$modelId)->delete();
				}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
			return Redirect::route($this->model.".index",$obj->category_id);
		}
	}// end update()


	public function questionaddMoreDetailRow(Request $request){
        $request->replace($this->arrayStripTags($request->all()));
        $thisData						=	$request->all();
       
        $count = $thisData['id'];
		
        return View::make('admin.SubCategory.add_more_question_detail_row', compact('count'));
    }
	
	public function deleteItem(Request $request){
		$modelId  = $request->get('id'); 
		$delete_item = SubCategoryQuestion::where('id',$modelId)->delete();
		return response()->json($delete_item);
	 }
	
	
	/**
	* Function for mark a couse as deleted 
	*
	* @param $userId as id of couse
	*
	* @return redirect page. 
	*/
	public function delete($id = 0){
		$model	=	SubCategory::find($id); 
		if(empty($model)) {
			return Redirect::back();
		}
		if($id){
			SubCategory::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	} // end delete()


	public function changePopularStatus(Request $request,$modelId=0){
		$id	=	$request->get("id");
		$value	=	$request->get("data");
		$record = SubCategory::where('id',$id)->update(array('is_popular'=>$value));
	    die;
	}//end changePopularStatus
	
    
    
    public function view($SubcategoryId = 0){
		$model				=	SubCategory::where('id',"$SubcategoryId")->select('sub_categories.*')->first(); 
		$categoryId = $model->category_id;						
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$SubCategoryQuestion  = SubCategoryQuestion::where('sub_category_id',$SubcategoryId)->get();
		return  View::make("admin.$this->model.view",array('model' => $model,'SubcategoryId' => $SubcategoryId,'categoryId' => $categoryId,'SubCategoryQuestion'=>$SubCategoryQuestion));
	}

}// end SubCategoryController
