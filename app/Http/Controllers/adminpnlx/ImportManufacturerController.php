<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Exports\ExportManufacturerRebate;
use App\Imports\VendorImport;
use App\Imports\ImportManufacturereExcel;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\ImportTemplate;
use App\Model\ImportManufacturer;
use App\Model\Customer;
use App\Model\ImportManufacturerRebate;
use App\Model\ImportManufacturerRebateDetails;
use App\Model\Vendor;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ImportManufacturerController Controller
*
* Add your methods in the class below
*
*/
class ImportManufacturerController extends BaseController {

	public $model					=	'ImportManufacturer';
	public $sectionName				=	'Import Manufacturer Rebate Data';
	public $sectionNameSingular		=	'Import Manufacturer Rebate Data';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function index(Request $request){ 
		$DB					=	ImportManufacturerRebate::query();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'manufacturer_rebates.vendor_id')->select('manufacturer_rebates.*', 'vendors.business_name as vendor');
		$searchVariable		=	array();
		$inputGet			=	$request->all();
		//export item by seach namevendors
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
			/*if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = date('Y-m-d', strtotime("01-".$searchData['date_from']));
				$dateE = $searchData['date_to'];
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('manufacturer_rebates.transaction_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = date('Y-m-d', strtotime("28-".$searchData['date_from']));
				$searchExportItem['date_from'] = $dateS;
				$DB->where('manufacturer_rebates.transaction_date','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('manufacturer_rebates.transaction_date','<=' ,[$dateE." 00:00:00"]); 						
			}*/
			if((!empty($searchData['date_from']))){
				$dateS = date('Y-m-d', strtotime("01-".$searchData['date_from']));
				$dateE = date('Y-m-d', strtotime("31-".$searchData['date_from']));;
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('manufacturer_rebates.transaction_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("manufacturer_rebates.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("manufacturer_rebates.vendor_id",'like','%'.$fieldValue.'%');
					}
					
					if($fieldName == "customer_acc_no"){
						$searchExportItem['customer_acc_no'] = $fieldValue;
						$DB->where("customers.customer_code",$fieldValue);
					}

				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		// To export all to PDF as well as Excel
		$exportedVendorRecords = $DB->orderBy($sortBy,$order)->get();
		if(Session::has('manufacturer_rebate_Date')) {
			Session::forget('manufacturer_rebate_Date');
		}
		Session::put('importdata_export_all_data', $searchExportItem);
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		$vendors = Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		return  View::make("admin.$this->model.index",compact('groups','vendors','results','searchVariable','sortBy','order','query_string'));
	}// end index()

	public function ViewManufacturerDataList($id,Request $request){  
		$DB					=	ImportManufacturerRebateDetails::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('manufacturer_rebates', 'manufacturer_rebates.id', '=', 'manufacturer_rebate_details.manufacturer_rebate_id')->leftJoin('vendors', 'vendors.id', '=', 'manufacturer_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'manufacturer_rebate_details.customer_id')->select('manufacturer_rebate_details.*', 'vendors.business_name as vendor', 'customers.customer_code as customer_acc','manufacturer_rebates.transaction_date');
		//export item by seach namevendors
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
				$DB->whereBetween('manufacturer_rebates.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('manufacturer_rebates.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('manufacturer_rebates.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("vendors.business_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_name"){
						$searchExportItem['customer_name'] = $fieldValue;
						$DB->where("customers.business_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_acc_no"){
						$searchExportItem['customer_acc_no'] = $fieldValue;
						$DB->where("customers.customer_code",$fieldValue);
					}

				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB;
		}else{
		   $DB->where('manufacturer_rebates.group_id', Session::get('group'));
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedVendorRecords = $DB->orderBy($sortBy,$order)->get();

		if(Session::has('importdata_export_all_data')) {
			Session::forget('importdata_export_all_data');
		}

		Session::put('importdata_export_all_data', $searchExportItem);
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->where('manufacturer_rebate_details.manufacturer_rebate_id',$id)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		//echo "<pre>";//print_r($results);die;
		return  View::make("admin.$this->model.ViewManufacturerDataList",compact('groups','results','searchVariable','sortBy','order','query_string', 'id'));
	}// end ViewManufacturerDataList()


	/**
	* Function for add manufacture data
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add($Id = 0){  
		//echo $Id;die;
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		return  View::make("admin.$this->model.add",compact('Id'));
	}// end add()


	/**
	* Function for save new manufacture data
	*
	* @param null
	*
	* @return redirect page. 
	*/
	public function save($Id,Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'customer_code'				=> 'required',
					'customer_name'				=> 'required',
					'amount'					=> 'required',
				),
				array(
					"customer_code.required"	=>	trans("The customer account number field is required."),
					"customer_name.required"	=>	trans("The customer name field is required."),
					"amount.required"			=>	trans("The amount field is required."),
				)
			);
			if($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$CustomerData = Customer::where('customer_code',$request->input('customer_code'))->first();
				if(empty($CustomerData)){
					Session::flash('error', trans("Customer account not exist.")); 
					return Redirect::back();
				}else{
					if($CustomerData->group_id == Session::get('group')){
						$obj 							=  new ImportManufacturerRebateDetails;
						$obj->manufacturer_rebate_id 	=  $Id;
						$obj->customer_id 				=  $CustomerData->id;
						$obj->customer_code 			=  $request->input('customer_code');
						$obj->customer_name 			=  $request->input('customer_name');
						$obj->amount 					=  $request->input('amount');
						$obj->save();
			
						$manufactureDataId				=	$obj->id;
						
						if(!$manufactureDataId){
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}else{
						Session::flash('error', trans("Customer account not exist with this group.")); 
						return Redirect::back()->withInput();
					}
				} 
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".ViewManufacturerDataList", $Id);
			}
		}
	}//end save()


	public function edit($modelId = 0,$Id = 0, Request $request){
		$model					=	ImportManufacturerRebateDetails::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		return  View::make("admin.$this->model.edit",compact('model','modelId'));
	}// end edit()

	public function update($modelId,$Id,Request $request){
		$model					=	ImportManufacturerRebateDetails::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'customer_code'			=> 'required',
					'customer_name'			=> 'required',
					'amount'				=> 'required',
				),
				array(
					"customer_code.required" =>	trans("The customer account number field is required."),
					"customer_name.required" =>	trans("The customer name field is required."),
					"amount.required"		 =>	trans("The amount field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 							=  $model;
				$obj->customer_code 			=  $request->input('customer_code');
				$obj->customer_name 			=  $request->input('customer_name');
				$obj->amount 					=  $request->input('amount');
				$obj->save();
				$userId							=	$obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".ViewManufacturerDataList", $Id);
			}
		}
	}// end update()


	public function deleteRebate($modelId,$Id)
	{
		
		$modelDetails	=	ImportManufacturerRebateDetails::where('id',$modelId); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ViewManufacturerDataList", $Id);
		}
		if($modelId){	
			ImportManufacturerRebateDetails::where('id',$modelId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end deleteRebate()


	public function exportAllDataToExcel() {
		return Excel::download(new ExportManufacturerRebate, 'manufacturer-rabate-data-information-'.time().'.xlsx');
	}// end exportAllDataToExcel()

	public function delete($id)
	{ 
		$modelDetails	=	ImportManufacturerRebate::where('id',$id); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($id){
			if(auth()->guard('admin')->user()->user_role == "super_admin"){
				ImportManufacturerRebate::where('id',$id)->where('group_id',Session::get('group'))->delete();

			}else{
				ImportManufacturerRebate::where('id',$id)->where('group_id',Session::get('group'))->where('admin_id', auth()->guard('admin')->user()->id)->delete();
			}
			ImportManufacturerRebateDetails::where('manufacturer_rebate_id',$id)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}// end delete()

	public function deleteTransaction($id)
	{
		$modelDetails	=	ImportManufacturer::find($id); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ImportDataList");
		}
		if($id){	
			ImportManufacturer::where('id',$id)->where('group_id',Session::get('group'))->where('admin_id', auth()->guard('admin')->user()->id) ->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}// end deleteTransaction()

	public function view($modelId = 0)
	{
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$model	=	ImportManufacturerRebateDetails::leftJoin('manufacturer_rebates', 'manufacturer_rebates.id', '=', 'manufacturer_rebate_details.manufacturer_rebate_id')->leftJoin('vendors', 'vendors.id', '=', 'manufacturer_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'manufacturer_rebate_details.customer_id')->select('manufacturer_rebate_details.*','vendors.business_name as vendor','manufacturer_rebates.transaction_date as transaction_date')->where('manufacturer_rebate_details.id',"$modelId")->first();
		}else{
			$model	=	ImportManufacturerRebateDetails::leftJoin('manufacturer_rebates', 'manufacturer_rebates.id', '=', 'manufacturer_rebate_details.manufacturer_rebate_id')->leftJoin('vendors', 'vendors.id', '=', 'manufacturer_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'manufacturer_rebate_details.customer_id')->select('manufacturer_rebate_details.*','vendors.business_name as vendor','manufacturer_rebates.transaction_date as transaction_date')->where('manufacturer_rebate_details.id',"$modelId")->first();
		}
		return  View::make("admin.$this->model.view",compact('model'));
	}// end view()
 
	public function deleteData(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();
	  return  View::make("admin.$this->model.deletedata", compact('vendors'));
	}//end deleteData

	public function savedeleteData(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'		=> 'required',
					'start_date'	=> 'required',
					'end_date'		=> 'required',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('file')->getRealPath();
				$import = Excel::import(new VendorImport,$path);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			}
		}
	}//end function 

	//Function import manufacturer 
	public function importManufacturerRebate(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();;
	  return  View::make("admin.$this->model.import", compact('vendors'));
	}//end function

	////Function save import manufacturer exel file 
	public function saveimportManufacturerRebate(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'		=> 'required',
					'start_date'	=> 'required',
					'file'			=> 'required',
					'file.*' 		=> 'required|file|max:5000|mimes:xlsx',

					
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The transaction date is required."),
					"file.required"			=>	trans("The file is required."),
					"file.file"				=>	trans("Input only Exel file."),
					"file.mimes"			=>	trans("Input only Exel file."),
				)
			);
			if($request->has('file')){
				if(($request->file('file')->getClientOriginalExtension() != 'xlsx')){
					$errors 				=	$validator->messages();
					$errors->add('file', trans("The file must be only xlsx"));
					return Redirect::back()->withErrors($errors)->withInput();
				}
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('file')->getRealPath();
				$path1 = $request->file('file')->store('temp'); 
				$path=storage_path('app').'/'.$path1;
				$vendor_id = $request->get('vendor');
				$date = date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$transaction_date = date("Y-m-t", strtotime($date));

				$obj                          = new ImportManufacturerRebate;
				$obj->group_id                = Session::get('group');
				$obj->admin_id                = auth()->guard('admin')->user()->id;
				$obj->vendor_id               = $vendor_id;
				$obj->transaction_date        = $transaction_date;
				$obj->save();

				$manufacturere_rebate_id = $obj->id;
				$import = new ImportManufacturereExcel($vendor_id,$transaction_date,$manufacturere_rebate_id);
				$import->import($path);
				$failedRecords = array();

				if(!empty($import->failures())){
					foreach ($import->failures() as $failure) {
						if(array_key_exists($failure->row(),$failedRecords)){
							$failedRecords[$failure->row()]['message'] .=  $failure->errors()[0]."\n";
							$failedRecords[$failure->row()]['data'] =  $failure->values();
							 //print_r($failure->row()); // row that went wrong
							 //echo "<br>";
							 //print_r( $failure->attribute()); // either heading key (if using heading row concern) or column index
							 //echo "<br>";
							 //print_r( $failure->errors()); // Actual error messages from Laravel validator
							 //echo "<br>";
							 //print_r( $failure->values()); // The values of the row that has failed.
							 //echo "<br>";
						}else{
							$failedRecords[$failure->row()]['message'] 	=  $failure->errors()[0]."\n";
							$failedRecords[$failure->row()]['data'] 	=  $failure->values();
						}
					}
				}
				$failedRecords = array_values($failedRecords);
				$successArray = array();
				if(!empty($import->data)){
					foreach ($import->data as $data) {
						$singleArray = array();
						$manufactureRebate = ImportManufacturerRebateDetails::where('customer_id', $data->customer_id)->first(); 
						$singleArray['customer_code'] = $manufactureRebate->customer_code;
						$singleArray['customer_name'] = $manufactureRebate->customer_name;
						$singleArray['amount'] = $manufactureRebate->amount;
						$singleArray['id'] = $data->id;
						$successArray[] = $singleArray; 
					}
				}
				
				$vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();

				return  View::make("admin.$this->model.import", compact('vendors', 'failedRecords', 'successArray'));
			}
		}
	}
	//end function

	// delete ManufacturerRebate form
	public function deleteManufacturerRebate(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();;
	  return  View::make("admin.$this->model.delete_manufacturer_rebate", compact('vendors'));
	}//end function

	// delete ManufacturerRebate
	public function savedeleteManufacturerRebate(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'		=> 'required',
					'start_date'	=> 'required',
					'end_date'		=> 'required',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
				)
			);
			
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
	}//end function 

	public function PurchaseRebates($id){
	  $data = VendorRebatePurchase::where('type', 2)->where('vendor_rebate_id',$id)->get();
	  $purchase = '';
	  $rebates = '';
	  $html = "";
	  foreach ($data as $key => $value) {
	  	$html .= '<tr><td>'.$value->purchase.'<td><td>'.$value->rebate.'</td></tr>'; 
	  }
	  return $html;
	} 
}// end ImportManufacturerController

