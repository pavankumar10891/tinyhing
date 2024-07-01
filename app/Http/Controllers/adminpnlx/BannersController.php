<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Banner;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Http\Request;

/**
* BannersController Controller
*
* Add your methods in the class below
*
*/
class BannersController extends BaseController {

	public $model		=	'Banner';
	public $sectionName	=	'Sliders';
	public $sectionNameSingular	=	'Slider';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all banners 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Banner::query();
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
		$results->appends($inputGet)->render();
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()

	
	/**
	* Function for add new banner
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		return  View::make("admin.$this->model.add");
	}// end add()
	
/**
* Function for save new banner
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
					'title'		    => 'required',
					'image'			=> 'required|mimes:'.IMAGE_EXTENSION,
					'description' 	=> 'required',
					'order' 		=> 'required|numeric|gt:0',
				),
				array(
					"image.required"	=> "The image field is required.",
					"image.mimes"		=> "The image must be a file of type: jpeg, jpg, png, gif, bmp.",
					'order.gt'      	=>  trans("The order should be greater than 0")
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				
				$obj 									=  new Banner;
				$obj->title 			=  $request->input('title');
				$obj->description 		=  $request->input('description');
				$obj->order_number 		=  $request->input('order');
				if($request->hasFile('image')){
					$extension 	=	 $request->file('image')->getClientOriginalExtension();
					$fileName	=	time().'-image.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	BANNER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('image')->move($folderPath, $fileName)){
						$obj->image	=	$folderName.$fileName;
					}
				}
				$obj->save();
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	/**
	* Function for update status
	*
	* @param $modelId as id of banner 
	* @param $status as status of banner 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('banners',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit banner
	*
	* @param $modelId id  of banner
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		$model					=	Banner::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		return  View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	
	/**
	* Function for update banner 
	*
	* @param $modelId as id of banner 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Banner::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'title'		    => 'required',
					'image'			=> ($request->file('image'))?'mimes:'.IMAGE_EXTENSION:'',
					'description' 	=> 'required',
					'order' 		=> 'required|numeric|gt:0',
				),
				array(
					"image.mimes"		=> "The image must be a file of type: jpeg, jpg, png, gif, bmp.",
					'order.gt'      =>  trans("The order should be greater than 0")
				)			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$obj =  $model;
				$obj->title 			=  $request->input('title');
				$obj->description 		=  $request->input('description');
				$obj->order_number 		=  $request->input('order');
				if($request->hasFile('image')){
					$extension 	=	 $request->file('image')->getClientOriginalExtension();
					$fileName	=	time().'-image.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	BANNER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('image')->move($folderPath, $fileName)){
						$obj->image	=	$folderName.$fileName;
					}
				}
				$obj->save();
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for update banner  status
	*
	* @param $modelId as id of id 
	* @param $modelStatus as status of status 
	*
	* @return redirect page. 
	*/	
	public function delete($id = 0){
		$model	=	Banner::find($id); 
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		if($model->image != "" && file_exists(BANNER_IMAGE_ROOT_PATH.$model->image)){
			unlink(BANNER_IMAGE_ROOT_PATH.$model->image);
		}
		Banner::where('id',$id)->delete();
		Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		return Redirect::back();
	}// end delete()
	
}// end BannersController
