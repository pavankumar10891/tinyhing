<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* TestimonialController Controller
*
* Add your methods in the class below
*
*/
class TestimonialController extends BaseController {

	public $model		=	'Testimonial';
	public $sectionName	=	'Testimonials';
	public $sectionNameSingular	= 'Testimonial';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Testimonial 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Testimonial::query();
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
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("name",'like','%'.$fieldValue.'%');
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
	 * Function for add new Testimonial
	 *
	 * @param null
	 *
	 * @return view page. 
	 */


	public function add(Request $request){ 
		
		return  View::make("admin.$this->model.add");
	}// end add()
	

	
/**
* Function for save new Mechanic
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){	
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		    => 'required',
                    'image'				    => 'mimes:'.IMAGE_EXTENSION,
					'description' 				=> 'required',
					'designation' 			=> 'required',
                )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				
				$obj 						=  new Testimonial;
				$obj->name 			=  $request->input('name');
				$obj->description 					=  $request->input('description');
				$obj->designation 				=  $request->input('designation');

				if($request->hasFile('image')){
                    $extension 	=	 $request->file('image')->getClientOriginalExtension();
                    $fileName	=	time().'-image.'.$extension;
                    
                    $folderName     	= 	strtoupper(date('M'). date('Y'))."/";
                    $folderPath			=	TESTIMONIAL_IMAGE_ROOT_PATH.$folderName;
                    if(!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777,true);
                    }
                    if($request->file('image')->move($folderPath, $fileName)){
                        $obj->image	=	$folderName.$fileName;
                    }
                }
				$obj->save();
				$userId					=	$obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully."));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()

	

	public function edit($modelId = 0,Request $request){
		$model		=	Testimonial::where('id',$modelId)->first();
		
		if(empty($model)) {
			return Redirect::back();
		}
	 	return View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	/**
	* Function for update mechanic 
	*
	* @param $modelId as id of mechanic 
	*
	* @return redirect page. 
	*/

	function update($modelId,Request $request){
		$model					=	Testimonial::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
                    'name'		    => 'required',
                    'image'				    => 'nullable|mimes:'.IMAGE_EXTENSION,
					'description' 				=> 'required',
					'designation' 			=> 'required',
				),
				
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

                $obj 						=  Testimonial::find($modelId);
                $obj->name 			=  $request->input('name');
				$obj->description 					=  $request->input('description');
				$obj->designation 				=  $request->input('designation');
                
				if($request->hasFile('image')){ 
					if(File::exists(TESTIMONIAL_IMAGE_ROOT_PATH.$obj->image)) {
						File::delete(TESTIMONIAL_IMAGE_ROOT_PATH.$obj->image);	
					}
					$extension 		=	$request->file('image')->getClientOriginalExtension();
					$fileName		=	time().'-image.'.$extension;
					$folderName     = 	strtoupper(date('M'). date('Y'))."/";
					$folderPath		=	TESTIMONIAL_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('image')->move($folderPath, $fileName)){
						$obj->image =	$folderName.$fileName;
					}
				} 
			   
				$obj->save();
				$userId				=	$obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for update Testimonial  status
	*
	* @param $modelId as id of Testimonial 
	* @param $modelStatus as status of Testimonial 
	*
	* @return redirect page. 
	*/	

	 public function delete($modelId = 0){
		$details = Testimonial::find($modelId);
		if(empty($details)) {
			return Redirect::route($this->model.".index");
		}
		if($details){
			if(File::exists(TESTIMONIAL_IMAGE_ROOT_PATH.$details->image)) {
				File::delete(TESTIMONIAL_IMAGE_ROOT_PATH.$details->image);	
			}
			Testimonial::where('id',$modelId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Testimonial::where('id',$modelId)->select('*')->first();      
		if($model->is_deleted =='1') {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()
}// end TestimonialController