<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController; 
use App\Model\Category;
use App\Model\CategoryDescription;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* CategoryController Controller
*
* Add your methods in the class below
*
*/
class CategoryController extends BaseController {

	public $model		=	'Category';
	public $sectionName	=	'Categories';
	public $sectionNameSingular	=	'Category';
	
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
		$DB					=	Category::query();
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
						$DB->where("categories.category_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "country"){
						$DB->where("countries.id",$fieldValue);
					}
					if($fieldName == "is_active"){
						$DB->where("categories.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("categories.is_deleted",0);
        $DB->leftjoin('countries','categories.country_id','countries.id');
        $DB->leftjoin('country_vats','categories.country_vat_id','country_vats.id');
        $DB->select("categories.*",'countries.country_name','country_vats.vat');
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($request->all())->render();
        
		$countries      	=	DB::table("countries")->where("is_active",1)->where("is_deleted",0)->pluck("country_name","id")->toArray();
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string','countries'));
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
		
        $countries      	=	DB::table("countries")->where("is_active",1)->where("is_deleted",0)->pluck("country_name","id")->toArray();
        $countries_vat      =   array();
		return  View::make("admin.$this->model.add",compact('languages' ,'language_code',"countries","countries_vat"));
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
                'country'                         => $request->input('country'),
                'vat'                             => $request->input('vat'),
                'service_fee_urgent'              => $request->input('service_fee_urgent'),
				'service_fee_regular' 		      => $request->input('service_fee_regular'),
				'commission' 		              => $request->input('commission'),
				'category_order_by' 		      => $request->input('category_order_by'),
				'category_name' 	              => $dafaultLanguageArray['category_name'],
				'image' 		    	          => $request->file('image'),
				
			),
			array(
				'country'                        => 'required',
				'vat' 			                 => 'required',
                'service_fee_urgent' 		     => 'required|numeric',
                'service_fee_regular' 			 => 'required|numeric',
                'commission' 			         => 'required|numeric',
				'category_name' 		         => 'required',
				'category_order_by' 		     => 'required',
				'image'      				     => 'required|mimes:'.IMAGE_EXTENSION,
			),
			array(
				 "country.required"	            	=>	trans("The country field is required."),
				 "category_order_by.required"	    =>	trans("The category order field is required."),
				 "image.required"	            	=>	trans("The image field is required."),
				 "image.mimes"						=>	trans("The image must be a file of type: 'jpeg, jpg, png, gif, bmp.'."),
				 "service_fee_urgent.required"	    =>	trans("The service fee urgent field is required."),
				 "service_fee_urgent.numeric"		=>	trans("The service fee urgent must be numeric."),
				 "vat.required"				        =>	trans("The vat field is required."),
				 "service_fee_regular.required"		=>	trans("The service fee regular field is required."),
				 "service_fee_regular.numeric"		=>	trans("The service fee regular must be numeric."),
				 "commission.required"				=>	trans("The commission field is required."),
				 "commission.numeric"				=>	trans("The commission must be numeric."),
				 "category_name.required"			=>	trans("The category name field is required."),
				
			)

		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj = new Category;
			$obj->slug      					= $this->getSlug($dafaultLanguageArray['category_name'],'category_name','Category');
			$obj->country_id    			    = $request->get('country');
			$obj->country_vat_id  	    		= $request->get('vat');
			$obj->service_fee_urgent    	    = $request->get('service_fee_urgent');
			$obj->service_fee_regular       	=  $request->input('service_fee_regular');
			$obj->commission   		            = $request->input('commission');
			$obj->category_order_by   		    = $request->input('category_order_by');
			$obj->category_name   	            = $dafaultLanguageArray['category_name'];
			$obj->description   	            = $dafaultLanguageArray['description'];
			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-categories.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	CATEGORY_IMAGE_ROOT_PATH.$folderName;
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
				$CategoryDescription_obj					=  new CategoryDescription();
				$CategoryDescription_obj->language_id	=	$language_id;
				$CategoryDescription_obj->parent_id		=	$last_id;
				$CategoryDescription_obj->category_name	=	$value['category_name'];	
				$CategoryDescription_obj->description	=	$value['description'];	
				$CategoryDescription_obj->save();
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
		$this->_update_all_status('categories',$modelId,$status);
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
        $model				=	Category::find($modelId);
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$CategoryDescription	=	CategoryDescription::where('parent_id', '=',  $modelId)->get();
        $multiLanguage		 	=	array();
        if(!empty($CategoryDescription)){
			foreach($CategoryDescription as $description) {
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
        $countries      	=	DB::table("countries")->where("is_active",1)->where("is_deleted",0)->pluck("country_name","id")->toArray();
        $countries_vat      =   array();
		return  View::make("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'model' => $model,'multiLanguage' => $multiLanguage,"countries"=>$countries,"countries_vat"=>$countries_vat));
	} // end edit()
	
	
	/**
	* Function for update category 
	*
	* @param $modelId as id of category 
	*
	* @return redirect page. 
	*/
	function update(Request $request , $modelId){
		$model					=	Category::findorFail($modelId);
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
                'country'                         => $request->input('country'),
                'vat'                             => $request->input('vat'),
                'service_fee_urgent'              => $request->input('service_fee_urgent'),
				'service_fee_regular' 		      => $request->input('service_fee_regular'),
				'commission' 		              => $request->input('commission'),
				'category_order_by' 		      => $request->input('category_order_by'),
                'category_name' 	              => $dafaultLanguageArray['category_name'],
				'image' 		    	          => $request->file('image'),
			),
			array(
				'country'                        => 'required',
				'vat' 			                 => 'required',
                'service_fee_urgent' 		     => 'required|numeric',
                'service_fee_regular' 			 => 'required|numeric',
                'commission' 			         => 'required|numeric',
				'category_name' 		         => 'required',
				'category_order_by' 		     => 'required',
				'image'      				     => 'nullable|mimes:'.IMAGE_EXTENSION,
			),
			array(
				"country.required"	            	=>	trans("The country field is required."),
				"category_order_by.required"	    =>	trans("The category order field is required."),
				"image.mimes"						=>	trans("The image must be a file of type: 'jpeg, jpg, png, gif, bmp.'."),
				"service_fee_urgent.required"	    =>	trans("The service fee urgent field is required."),
				"service_fee_urgent.numeric"		=>	trans("The service fee urgent must be numeric."),
				"vat.required"				        =>	trans("The vat field is required."),
				"service_fee_regular.required"		=>	trans("The service fee regular field is required."),
				"service_fee_regular.numeric"		=>	trans("The service fee regular must be numeric."),
				"commission.required"				=>	trans("The commission field is required."),
				"commission.numeric"				=>	trans("The commission must be numeric."),
				"category_name.required"			=>	trans("The category name field is required."),
			   
		   )
		);
		
		if ($validator->fails()) {	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj                = $model;
			
			
            $obj->country_id    			    = $request->get('country');
			$obj->country_vat_id  	    		= $request->get('vat');
			$obj->service_fee_urgent    	    = $request->get('service_fee_urgent');
			$obj->service_fee_regular       	=  $request->input('service_fee_regular');
			$obj->commission   		            = $request->input('commission');
			$obj->category_order_by   		    = $request->input('category_order_by');
			$obj->category_name   	            = $dafaultLanguageArray['category_name'];
			$obj->description   	            = $dafaultLanguageArray['description'];

			if($request->hasFile('image')){
				$extension 	=	 $request->file('image')->getClientOriginalExtension();
				$fileName	=	time().'-categories.'.$extension;
				
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath			=	CATEGORY_IMAGE_ROOT_PATH.$folderName;
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
            
            CategoryDescription::where('parent_id', '=', $last_id)->delete();
            foreach ($thisData['data'] as $language_id => $value) {
				$CategoryDescription_obj					=  new CategoryDescription();
				$CategoryDescription_obj->language_id	=	$language_id;
				$CategoryDescription_obj->parent_id		=	$last_id;
				$CategoryDescription_obj->category_name	=	$value['category_name'];	
				$CategoryDescription_obj->description	=	$value['description'];	
				$CategoryDescription_obj->save();
			}
			DB::commit();
			Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
			return Redirect::route($this->model.".index");
		}
	}// end update()
	
	
	/**
	* Function for mark a couse as deleted 
	*
	* @param $userId as id of couse
	*
	* @return redirect page. 
	*/
	public function delete($id = 0){
		$model	=	Category::find($id); 
		if(empty($model)) {
			return Redirect::back();
		}
		if($id){
			Category::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	} // end delete()


	public function changePopularStatus(Request $request){
		$id	=	$request->get("id");
		$value	=	$request->get("data");
		$record = Category::where('id',$id)->update(array('is_popular'=>$value));
	    die;
	}//end changePopularStatus

	
    
    public function getVatByCountry(Request $request){
		$countryid	=	$request->input("countryid");
		$selctedid	=	$request->input("selctedid");
		$country_vats = DB::table('country_vats')->where("country_id",$countryid)->get()->toArray();
		return  View::make("admin.$this->model.add_more_vatdetail",compact('country_vats','selctedid'));
	}

	
    public function view($modelId = 0){
		$model				=	Category::find($modelId)
								->where('categories.id', '=',  $modelId)
								->leftjoin('countries','categories.country_id','countries.id')
                                ->leftjoin('country_vats','categories.country_vat_id','country_vats.id')
                                ->select("categories.*",'countries.country_name','country_vats.vat')->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",array('model' => $model));
	}

}// end CategoryController
