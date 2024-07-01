<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Model\Group;
use View, Session, Config, Validator, Redirect, File;

class GroupsController extends BaseController
{
    public $model		=	'Group';
	public $sectionName	=	'Groups';
	public $sectionNameSingular	=	'Group';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Users 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Group::query();
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
				$DB->whereBetween('groups.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('groups.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('groups.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("groups.name",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
        
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'groups.created_at';
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
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		return  View::make("admin.$this->model.add");
	}// end add()
	
/**
* Function for save new customer
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
					'name'	=> 'required',
					'logo'	=> 'required|mimes:'.IMAGE_EXTENSION
				),
				array(
					"nane.required"		=>	trans("The first name field is required."),
					"logo.required"			=>	trans("The logo field is required."),
					"logo.mimes"				=> 	trans("The logo must be a file of type: jpeg, jpg, png, gif,bmp.")
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 			=  new Group;
                $obj->name 		=  $request->input('name');
                
                // Upload logo
				if($request->hasFile('logo')){
					$extension 	=	 $request->file('logo')->getClientOriginalExtension();
					$fileName	=	time().'-logo.'.$extension;
					
					$folderPath			=	GROUP_LOGO_IMAGE_ROOT_PATH;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('logo')->move($folderPath, $fileName)){
						$obj->logo	=	$fileName;
					}
                }
                
				$obj->save();
				$groupId					=	$obj->id;
                
                if(!$groupId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
                }

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		$model					=	Group::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		
		return  View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Group::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'				=> 'required',
					'logo'				=>  'mimes:'.IMAGE_EXTENSION
				),
				array(
					"name.required"		=>	trans("The name field is required."),
					"logo.mimes"		=> 	trans("The logo must be a file of type: jpeg, jpg, png, gif,bmp.")
				)
            );
            
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 			=  $model;
                $obj->name 		=  $request->input('name');
                
                // Upload logo
				if($request->hasFile('logo')){
					$extension 	=	 $request->file('logo')->getClientOriginalExtension();
					$fileName	=	time().'-logo.'.$extension;
					
					$folderPath			=	GROUP_LOGO_IMAGE_ROOT_PATH;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('logo')->move($folderPath, $fileName)){
						$obj->logo	=	$fileName;
					}
                }

				$obj->save();
				$groupId					=	$obj->id;

				if(!$groupId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
                }
                
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for update Currency  status
	*
	* @param $modelId as id of Currency 
	* @param $modelStatus as status of Currency 
	*
	* @return redirect page. 
	*/	
	public function delete($userId = 0){
        $userDetails	=	Group::find($userId);
         
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){		
            Group::where('id',$userId)->update(array('deleted_at'=>date('Y-m-d h:i:s')));
            
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()
}
