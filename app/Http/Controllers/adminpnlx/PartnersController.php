<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use App\Model\Partners;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* PartnersController Controller
*
* Add your methods in the class below
*
*/
class PartnersController extends BaseController {

	public $model		=	'Partners';
	public $sectionName	=	'Partners';
	public $sectionNameSingular	= 'Partner';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Partners
	*
	* @param null
	*
	* @return view page. 
	*/

	public function index(Request $request){  
		$DB					=	Partners::query();
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


    // ADD And SAVE Functions
    public function add(Request $request){
		return  View::make("admin.$this->model.add");
	}// end add()
	
	public function save(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){	
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'		        => 'required',
                    'logo'				=> 'mimes:'.IMAGE_EXTENSION,
                ),
                array(
                    "name.required"		=>	trans("The name field is required"),
					"logo.mimes"	    =>  trans("The logo must be in: 'jpeg, jpg, png, gif, bmp formats'"),
                )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();

			}else{
				$obj 			        =   new Partners;
				$obj->name 		        =   ucfirst($request->input('name'));

				if($request->hasFile('logo')){
                    $extension 	        =	$request->file('logo')->getClientOriginalExtension();
                    $fileName	        =	time().'-partner-logo.'.$extension;
                    $folderName     	= 	strtoupper(date('M'). date('Y'))."/";
                    $folderPath			=	PARTNER_LOGO_ROOT_PATH.$folderName;
                    if(!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777,true);
                    }
                    if($request->file('logo')->move($folderPath, $fileName)){
                        $obj->logo	=	$folderName.$fileName;
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
		$model		=	Partners::where('id',$modelId)->first();
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
		$model					=	Partners::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'				    => 'required',
                    'image'				    => 'nullable|mimes:'.IMAGE_EXTENSION,
				),
				array(
					"name.required"			=>	trans("The name field is required."),
                    "logo.mimes"	        =>  trans("The logo must be in: 'jpeg, jpg, png, gif, bmp formats'"),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 		        =  Partners::find($modelId);
				$obj->name 			=  ucfirst($request->input('name'));
			
                if($request->hasFile('logo')){ 
					if(File::exists(PARTNER_LOGO_ROOT_PATH.$obj->logo)) {
						File::delete(PARTNER_LOGO_ROOT_PATH.$obj->logo);	
					}
					$extension 		=	$request->file('logo')->getClientOriginalExtension();
					$fileName		=	time().'-partner-logo.'.$extension;
					$folderName     = 	strtoupper(date('M'). date('Y'))."/";
					$folderPath		=	PARTNER_LOGO_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('logo')->move($folderPath, $fileName)){
						$obj->logo =	$folderName.$fileName;
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
		$model	=	Partners::where('id',"$modelId")->select('*')->first();      
		if($model->is_deleted =='1') {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()

    
    public function delete($userId = 0){
		$userDetails = Partners::find($userId);
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$deleteDate = date("Y-m-d H:i:s");
			Partners::where('id',$userId)->update(array('is_deleted'=>1, 'deleted_at' => $deleteDate));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

}// end HowItWorkController