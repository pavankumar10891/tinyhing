<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\WhyChooseUs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* WhyChooseUsController Controller
*
* Add your methods in the class below
*
*/
class WhyChooseUsController extends BaseController {

	public $model		=	'WhyChooseUs';
	public $sectionName	=	'Why Choose Us';
	public $sectionNameSingular	= 'Why Choose Us';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all WhyChooseUs
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	WhyChooseUs::query();
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
			if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "order_number"){
						$DB->where("order_number",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("is_deleted",0);
		$DB->select("*");
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
	 * Function for add new HowItWork
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
					'name'		        => 'required',
					'description' 	    => 'required',
					'order_number' 		=> 'required|numeric|gt:0',
                    'image'				=> 'mimes:'.IMAGE_EXTENSION,
                ),
                array(
					"name.required"			=>	trans("The name field is required"),
					"description.required"	=>	trans("The description field is required"),
					"image.mimes"	    	=>  trans("The image must be in: 'jpeg, jpg, png, gif, bmp formats'"),
                    'order_number.numeric'  =>  trans("The order field must be numeric"),
                    'order_number.required' =>  trans("The order field is required"),
                    'order_number.gt'       =>  trans("The order should be greater than 0"),
                )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 					=  new WhyChooseUs;
				$obj->name 				=  ucfirst($request->input('name'));
				$obj->description 		=  $request->input('description');
				$obj->order_number 		=  $request->input('order_number');

				if($request->hasFile('image')){
                    $extension 			=	 $request->file('image')->getClientOriginalExtension();
                    $fileName			=	time().'-why-choose-us-.'.$extension;
                    $folderName     	= 	strtoupper(date('M'). date('Y'))."/";
                    $folderPath			=	WHYCHOOSEUS_IMAGE_ROOT_PATH.$folderName;
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
		$model		=	WhyChooseUs::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
	 	return View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/

	public function update($modelId,Request $request){
		$model					=	WhyChooseUs::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		        => 'required',
					'description' 	    => 'required',
					'order_number' 		=> 'required|numeric|gt:0',
                    'image'				=> 'mimes:'.IMAGE_EXTENSION,
                ),
                array(
					"name.required"			=>	trans("The name field is required"),
					"description.required"	=>	trans("The description field is required"),
					"image.mimes"	    	=>  trans("The image must be in: 'jpeg, jpg, png, gif, bmp formats'"),
                    'order_number.numeric'  =>  trans("The order field must be numeric"),
                    'order_number.required' =>  trans("The order field is required"),
                    'order_number.gt'       =>  trans("The order should be greater than 0"),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 		        	=  WhyChooseUs::find($modelId);

				$obj->name 				=  ucfirst($request->input('name'));
				$obj->description 		=  $request->input('description');
				$obj->order_number 		=  $request->input('order_number');

				if($request->hasFile('image')){ 
					if(File::exists(WHYCHOOSEUS_IMAGE_ROOT_PATH.$obj->image)) {
						File::delete(WHYCHOOSEUS_IMAGE_ROOT_PATH.$obj->image);	
					}
					$extension 		=	$request->file('image')->getClientOriginalExtension();
					$fileName		=	time().'-why-choose-us.'.$extension;
					$folderName     = 	strtoupper(date('M'). date('Y'))."/";
					$folderPath		=	WHYCHOOSEUS_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('image')->move($folderPath, $fileName)){
						$obj->image =	$folderName.$fileName;
					}
				} 
				$obj->save();
				$userId						=	$obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()


	public function view($modelId = 0){
		$model	=	WhyChooseUs::where('id',"$modelId")->select('*')->first();      
		if($model->is_deleted =='1') {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()

	 
    public function delete($userId = 0){
		$userDetails = WhyChooseUs::find($userId);
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$deleteDate = date("Y-m-d H:i:s");
			WhyChooseUs::where('id',$userId)->update(array('is_deleted'=>1, 'deleted_at' => $deleteDate));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()
}// end HowItWorkController