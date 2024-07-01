<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Vendor;
use Illuminate\Http\Request;
use App\Exports\VendorsExport;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Imports\VendorImport;
use App\Model\Group;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* VendorsController Controller
*
* Add your methods in the class below
*
*/
class VendorsController extends BaseController {

	public $model		=	'Vendors';
	public $sectionName	=	'Vendor Admin';
	public $sectionNameSingular	=	'Vendor Admin';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Vendors 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		 $group = '';
		if(!empty(Session::get('group'))){
		 	$group = Session::get('group');
		}
		$DB					=	Vendor::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();

		//export item by seach name
		$searchExportItem = array();

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
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('vendors.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('vendors.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('vendors.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("vendors.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "business_name"){
						$searchExportItem['business_name'] = $fieldValue;
						$DB->where("vendors.business_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "corporate_name"){
						$searchExportItem['corporate_name'] = $fieldValue;
						$DB->where("vendors.corporate_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$searchExportItem['is_active'] = $fieldValue;
						$DB->where("customers.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			if(!empty($group)){
				$DB->where("vendors.deleted_at",NULL)->where('group_id', $group);
			}else{
				$DB->where("vendors.deleted_at",NULL);
			}
			 
		}else{
		   $DB->where("vendors.deleted_at",NULL)->where('group_id', $group);
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'vendors.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedVendorRecords = $DB->orderBy($sortBy,$order)->get();

		if(Session::has('vendors_export_all_data')) {
			Session::forget('vendors_export_all_data');
		}

		Session::put('vendors_export_all_data', $searchExportItem);
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		
		return  View::make("admin.$this->model.index",compact('groups','results','searchVariable','sortBy','order','query_string'));
	}// end index()

	public function exportAllDataToExcel() {
		return Excel::download(new VendorsExport, 'vendor-information-'.time().'.xlsx');
	}

	/**
	* Function for add new Vendors
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		$states	=	config()->get('states');
		$vendorTypes	=	config()->get('vendor_types');
		$status = config()->get('status');

		return  View::make("admin.$this->model.add",compact('states', 'vendorTypes', 'status'));
	}// end add()
	
/**
* Function for save new Vendors
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
					'business_name'				=> 'required',
					'corporate_name'			=> 'required',
					'address_line_1'			=> 'required',
					'city'						=> 'required',
					'postal_code'				=> 'required',
					'is_active'					=> 'required',
					'email'						=> 'email',
					'website'					=> ['regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],

				),
				array(
					"business_name.required"		=>	trans("The business name field is required."),
					"corporate_name.required"		=>	trans("The corporate name field is required."),
					"address_line_1.required"		=>	trans("The address1 field is required."),
					"city.required"					=>	trans("The city field is required."),
					"postal_code.required"			=>	trans("The zip code field is required."),
					"is_active.required"			=>	trans("The statu code field is required."),
					//"website.regex"					=>	trans("The website url must be a valid url.")
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 										=  new Vendor;
				$obj->business_name 						=  $request->input('business_name');
				$obj->email 								=  $request->input('email');
				$obj->corporate_name 						=  $request->input('corporate_name');
				$obj->address_line_1 						=  $request->input('address_line_1');
				$obj->address_line_2 						=  $request->input('address_line_2');
				$obj->address_line_3 						=  $request->input('address_line_3');
				$obj->city 									=  $request->input('city');
				$obj->state_code 							=  $request->input('state_code');
				$obj->postal_code 							=  $request->input('postal_code');
				$obj->country 								=  $request->input('country');
				$obj->website 								=  $request->input('website');
				$obj->type 									=  $request->input('type');
				$obj->federal_tax_identification_number 	=  $request->input('federal_tax_identification_number');
				$obj->is_active 								=  $request->input('is_active');
				$obj->admin_id									=  Auth::guard('admin')->user()->id;
				$obj->group_id									=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->save();
				$userId										=	$obj->id;
		

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}
	
	/**
	* Function for display page for edit Vendors
	*
	* @param $modelId id  of Vendors
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		$model					=	Vendor::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$states	=	config()->get('states');
		$vendorTypes	=	config()->get('vendor_types');
		$status = config()->get('status');
		$internal_notes =	InternalNotes::where('vendor_id', $modelId)->get();
		$general_notes 	=	GeneralNotes::where('vendor_id', $modelId)->get();
		
		return  View::make("admin.$this->model.edit",compact('model','states','vendorTypes','status', 'internal_notes', 'general_notes'));
	} // end edit()
	
	
	/**
	* Function for update Vendors 
	*
	* @param $modelId as id of Vendors 
	*
	* @return redirect page. 
	*/
	
function update($modelId,Request $request){
		$model					=	Vendor::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'business_name'				=> 'required',
					'corporate_name'			=> 'required',
					'address_line_1'			=> 'required',
					'city'						=> 'required',
					'postal_code'				=> 'required',	
					'is_active'					=> 'required',
					'email'					=> 'email',
					'website'	=> ['regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],

				),
				array(
					"business_name.required"		=>	trans("The business name field is required."),
					"corporate_name.required"		=>	trans("The corporate name field is required."),
					"address_line_1.required"		=>	trans("The address1 field is required."),
					"city.required"					=>	trans("The city field is required."),
					"postal_code.required"			=>	trans("The zip code field is required."),
					"is_active.required"			=>	trans("The statu code field is required."),
					//"website.regex"					=>	trans("The website url must be a valid url.")
				)
			);

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 										=  $model;
				$obj->business_name 						=  $request->input('business_name');
				$obj->email 								=  $request->input('email');
				$obj->corporate_name 						=  $request->input('corporate_name');
				$obj->address_line_1 						=  $request->input('address_line_1');
				$obj->address_line_2 						=  $request->input('address_line_2');
				$obj->address_line_3 						=  $request->input('address_line_3');
				$obj->city 									=  $request->input('city');
				$obj->state_code 							=  $request->input('state_code');
				$obj->postal_code 							=  $request->input('postal_code');
				$obj->country 								=  $request->input('country');
				$obj->website 								=  $request->input('website');
				$obj->type 									=  $request->input('type');
				$obj->federal_tax_identification_number 	=  $request->input('federal_tax_identification_number');
				$obj->is_active 								=  $request->input('is_active');
				$obj->group_id									=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->save();
				$userId					=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(!empty($userId)){	
					if(isset($formData['internal_notes']) && !empty($formData['internal_notes'])){
						$deletenotes = InternalNotes::where('vendor_id',$userId)->delete();
						
							foreach ($formData['internal_notes'] as $data){
								$modelO							=  new InternalNotes();
								$modelO->vendor_id				=	$userId;
								$modelO->internal_note			=	$data['name'];	
								$modelO->save();
							}
					}

					if(isset($formData['general_note']) && !empty($formData['general_note'])){
						GeneralNotes::where('vendor_id',$userId)->delete();
						foreach ($formData['general_note'] as $data){
							$modelO								=  new GeneralNotes();
							$modelO->vendor_id					=	$userId;
							$modelO->general_note				=	$data['name'];	
							$modelO->save();
						}
					}

					Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::route($this->model.".index", $obj->vendor_id);

				}else{
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
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
		$userDetails	=	Vendor::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){	
			Vendor::where('id',$userId)->where('group_id',Session::get('group'))->where('admin_id', auth()->guard('admin')->user()->id) ->update(array('deleted_at'=>date('Y-m-d h:i:s')));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
		 	$model	=	Vendor::where('id',"$modelId")->first();
		}else{
			$model	=	Vendor::where('id',"$modelId")->where('group_id',Session::get('group'))->first();
		}
		
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}

		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()

	/**
	* Function for update status
	*
	* @param $modelId as id of Vendors 
	* @param $status as status of Vendors 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('vendors',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()


    //Function import Vendor data
	public function import(){
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		return  View::make("admin.$this->model.import");
	}//end function

	////Function save import Vendor csv file 
	public function importSaveCSV(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'file'		=> 'required',
					
				),
				array(
					"file.required"		=>	trans("The file is required."),
				)
			);
			if($request->has('file')){
				if(($request->file('file')->getClientOriginalExtension() != 'csv')){
					$errors 				=	$validator->messages();
					$errors->add('file', trans("The file must be only csv"));
					return Redirect::back()->withErrors($errors)->withInput();
				}
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('file')->getRealPath();
				
				$import = Excel::import(new VendorImport,$path);

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}
	//end function

////Function save import Vendor excel file 
	public function importSaveExcel(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'exel_file'		=> 'required',
					
				),
				array(
					"exel_file.required"		=>	trans("The file is required."),
				)
			);
			if($request->has('file')){
				if(($request->file('exel_file')->getClientOriginalExtension() != 'xlsx')){
					$errors 				=	$validator->messages();
					$errors->add('exel_file', trans("The file must be only excel"));
					return Redirect::back()->withErrors($errors)->withInput();
				}
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				//$path = $request->file('exel_file')->getRealPath();
				$path1 = $request->file('exel_file')->store('temp'); 
				$path=storage_path('app').'/'.$path1;
				$import = Excel::import(new VendorImport,$path);

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}//end function
}// end VendorsController
