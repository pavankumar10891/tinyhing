<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Vendor;
use Illuminate\Http\Request;
use App\Exports\VendorsExport;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Imports\VendorImport;
use App\Exports\ImportDataExport;
use App\Exports\ExportTransactionData;
use App\Imports\ImportTemplateExcel;
use App\Model\Group;
use App\Model\VendorRebate;
use App\Model\VendorRebatePurchase;
use App\Model\Customer;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ImportExportController Controller
*
* Add your methods in the class below
*
*/
class ImportExportController extends BaseController {

	public $model		=	'ImportExport';
	public $sectionName	=	'Import Data';
	public $sectionNameSingular	=	'Import';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}



	public function importDatalist(Request $request){  
		 
		$DB					=	VendorRebate::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'vendor_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'vendor_rebates.customer_id')->select('vendor_rebates.*', 'vendors.business_name as vendor', 'customers.business_name as customer_name', 'customers.customer_code as customer_acc', );

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
				$dateS = date('Y-m-d', strtotime("1".$searchData['date_from']));
				$dateE = $searchData['date_to'];
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('vendor_rebates.transaction_date', [$dateS, $dateE]); 											
			}elseif(!empty($searchData['date_from'])){
				//echo"01-".$searchData['date_from'];die;
				$dateS = date('Y-m-d', strtotime("01-".$searchData['date_from']));
				$searchExportItem['date_from'] = $dateS;
				$DB->where('vendor_rebates.transaction_date','>=' ,[$dateS]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('vendor_rebates.transaction_date','<=' ,[$dateE]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("vendor_rebates.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("vendor_rebates.vendor_id",'like','%'.$fieldValue.'%');
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
			$DB->where("vendor_rebates.is_deleted",0);
		}else{
		   $DB->where("vendor_rebates.is_deleted",0)->where('vendor_rebates.group_id', Session::get('group'));
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
		$results = $DB->orderBy($sortBy, $order)->groupBy('vendor_rebates.transaction_date')->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		
		/*echo "<pre>";
		print_r($results);die;*/
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		return  View::make("admin.$this->model.importindex",compact('groups','results','searchVariable','sortBy','order','query_string', 'vendors'));
	}// end index()

	public function ViewImportDataList(Request $request, $transaction_date){  
		 
		$DB					=	VendorRebate::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'vendor_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'vendor_rebates.customer_id')->select('vendor_rebates.*', 'vendors.business_name as vendor', 'customers.business_name as customer_name', 'customers.customer_code as customer_acc')->where('vendor_rebates.transaction_date', $transaction_date);

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
				$DB->whereBetween('vendor_rebates.created_at', [$dateS, $dateE]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('vendor_rebates.created_at','>=' ,[$dateS]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('vendor_rebates.created_at','<=' ,[$dateE]); 						
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
		if(auth()->guard('admin')->user()->user_role == "super_admin"){
			$DB->where("vendor_rebates.is_deleted",0);
		}else{
		   $DB->where("vendor_rebates.is_deleted",0)->where('vendor_rebates.group_id', Session::get('group'));
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
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		
		/*echo "<pre>";
		print_r($results);die;*/
		return  View::make("admin.$this->model.view-import-data-list",compact('groups','results','searchVariable','sortBy','order','query_string', 'transaction_date'));
	}// end index()

	public function exportAllDataToExcel() {
		return Excel::download(new ImportDataExport, 'importdata-information-'.time().'.xlsx');
	}

	public function exportTransactionDataToExcel($transaction_date) {
		return Excel::download(new ExportTransactionData($transaction_date), 'importdata-information-'.time().'.xlsx');
	}

	public function importDataView($modelId = 0)
	{
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
		 	$model	=	VendorRebate::leftJoin('vendors', 'vendors.id', 'vendor_rebates.vendor_id')->select('vendor_rebates.*', 'vendors.business_name')->where('vendor_rebates.id',"$modelId")->first();
		}else{
			$model	=	VendorRebate::leftJoin('vendors', 'vendors.id', '=', 'vendor_rebates.vendor_id')->leftJoin('customers', 'customers.id', '=', 'vendor_rebates.customer_id')->select('vendor_rebates.*', 'vendors.business_name as vendor', 'customers.business_name as customer_name', 'customers.customer_code as customer_acc', )->where('vendor_rebates.id',$modelId)->where('vendor_rebates.group_id',Session::get('group'))->first();
		}

		$rebates = VendorRebatePurchase::where('vendor_rebate_id',$modelId)->get();

		return  View::make("admin.$this->model.importview",compact('model', 'rebates'));
	}

	public function deleteImport($transaction_date)
	{
		
		$modelDetails	=	VendorRebate::where('transaction_date',$transaction_date); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ImportDataList");
		}
		if($transaction_date){
			if(auth()->guard('admin')->user()->user_role == "super_admin"){	
				VendorRebate::where('transaction_date',$transaction_date)->where('group_id',Session::get('group'))->update(array('is_deleted'=>1));

			}else{
				VendorRebate::where('transaction_date',$transaction_date)->where('group_id',Session::get('group'))->where('admin_id', auth()->guard('admin')->user()->id)->update(array('is_deleted'=>1));

			}
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}

	public function deleteTransaction($id)
	{
		
		$modelDetails	=	VendorRebate::find($id); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ImportDataList");
		}
		if($id){	
			VendorRebate::where('id',$id)->where('group_id',Session::get('group'))->where('admin_id', auth()->guard('admin')->user()->id) ->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}
	
    //Function import data
	public function importData(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.import", compact('vendors'));
	}//end function

	////Function save import Vendor excel file 
	public function saveImportData(Request $request)
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
					'file.*' 		=> 'required|file|max:5000|mimes:xlsx, xls',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
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
				$import = new ImportTemplateExcel($vendor_id,$transaction_date);
				$import->import($path);
				$failedRecords = array();
				if(!empty($import->failures())){
					foreach ($import->failures() as $failure) {
						if(array_key_exists($failure->row(),$failedRecords)){
							$failedRecords[$failure->row()]['message'] .=  $failure->errors()[0]."\r\n";
							$failedRecords[$failure->row()]['data'] =  $failure->values();
						}else{
							$failedRecords[$failure->row()]['message'] 	=  $failure->errors()[0]."\r\n";
							$failedRecords[$failure->row()]['data'] 	=  $failure->values();
						}
					}
				}
				
				$failedRecords = array_values($failedRecords);
				$successArray = array();
				
				if(!empty($import->data)){
					foreach ($import->data as $data) {
						$singleArray = array();
						$customer = Customer::where('id', $data->customer_id)->first(); 
						$singleArray['customer_name'] = $customer->business_name;
						$singleArray['customer_acc'] = $customer->customer_code;
						$singleArray['total_purchase'] = $data->total_purchases;
						$singleArray['none_quality_purchases'] = $data->none_quality_purchases;
						$singleArray['dropsize'] = $data->dropsize;
						$singleArray['id'] = $data->id;
						$successArray[] = $singleArray; 
					}
				}

				/* $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray(); */

				$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();

	  			return  View::make("admin.$this->model.import", compact('vendors', 'failedRecords', 'successArray'));
			}
		}
	}
	public function saveImportDataOld(Request $request)
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
					'file.*' 		=> 'required|file|max:5000|mimes:xlsx, xls',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
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
				$vendor_id = $request->get('vendor');
				// echo $request->get('start_date');die;
				// echo date("Y-m-t", strtotime($request->get('start_date')));die;
				// $transaction_date = $request->get('start_date');
				$transaction_date = '31-01-2021';
				$import = new ImportTemplateExcel($vendor_id,$transaction_date);
				$import->import($path);
				$failedRecords = array();
				if(!empty($import->failures())){
					foreach ($import->failures() as $failure) {
						if(array_key_exists($failure->row(),$failedRecords)){
							$failedRecords[$failure->row()]['message'] .=  $failure->errors()[0]."\n";
							$failedRecords[$failure->row()]['data'] =  $failure->values();
							// print_r($failure->row()); // row that went wrong
							// echo "<br>";
							// print_r( $failure->attribute()); // either heading key (if using heading row concern) or column index
							// echo "<br>";
							// print_r( $failure->errors()); // Actual error messages from Laravel validator
							// echo "<br>";
							// print_r( $failure->values()); // The values of the row that has failed.
							// echo "<br>";
						}else{
							$failedRecords[$failure->row()]['message'] =  $failure->errors()[0]."\n";
							$failedRecords[$failure->row()]['data'] =  $failure->values();
						}
					}
				}
			//	echo "<pre>";
			//	print_r(array_values($failedRecords));
				if(!empty($import->data)){
					foreach ($import->data as $data) {
						print_r( $data);
					}
				}
				die;
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}
	//end function

	//Function importVendorCheks data
	public function importVendorCheks(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.importvendorchecks", compact('vendors'));
	}//end function

////Function save saveimportVendorCheks file 
	public function saveimportVendorCheks(Request $request)
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
					'file' 			=> 'required|max:2000|mimes:jpg',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
					"file.required"			=>	trans("The file is required."),
					"file.image"			=>	trans("Input only jpg file."),
					"file.mimes"			=>	trans("Input only jpg file."),
					"file.max"				=>	trans("Input only 2mb file."),
				)
			);
			

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				echo "success";
				die;
				$path = $request->file('file')->getRealPath();
				$import = Excel::import(new VendorImport,$path);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}//end function

	public function deleteData(){
		
	 if(auth()->guard('admin')->user()->user_role == "super_admin"){	
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	 }else{
		$vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();

	 }
	  return  View::make("admin.$this->model.deletedata", compact('vendors'));
	}//end function

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
				echo "success";
				die;
				$path = $request->file('file')->getRealPath();
				
				$import = Excel::import(new VendorImport,$path);

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}//end function 

	//Function import invoice details
	public function invoiceDetails(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.invoice_details", compact('vendors'));
	}//end function

	////Function save invoice details exel format
	public function saveinvoiceDetails(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'		=> 'required',
					'file'			=> 'required',
					'file.*' 		=> 'required|file|max:5000|mimes:xlsx',

					
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
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
				echo "success";
				die;
				$path = $request->file('file')->getRealPath();
				
				$import = Excel::import(new VendorImport,$path);

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}

	//Function import manufacturer 
	public function importManufacturerRebate(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.import_manufacturer_rebate", compact('vendors'));
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
					'end_date'		=> 'required',
					'file'			=> 'required',
					'file.*' 		=> 'required|file|max:5000|mimes:xlsx',

					
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
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
				echo "success";
				die;
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

	// delete ManufacturerRebate form
	public function deleteManufacturerRebate(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
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
				echo "success";
				die;
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
	  $data = VendorRebatePurchase::where('vendor_rebate_id',$id)->get();
	  $purchase = '';
	  $rebates = '';
      $html = '';
	  foreach ($data as $key => $value) {
	  	  $html .= '<tr><td>'.$value->purchase.'<td><td>'.$value->rebate.'</td></tr>';
	  }
	  return $html;
	} 


	/**
	* Function for add manufacture data
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add($date = ""){  
		//echo $date;die;
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		//$purchaseRebates = VendorRebatePurchase::where('type', 1)->get();
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		return  View::make("admin.$this->model.add",compact('vendors','date'));
	}// end add()


	/**
	* Function for save new manufacture data
	*
	* @param null
	*
	* @return redirect page. 
	*/
	public function save($date,Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		//echo "<pre>";
		//print_r($formData);die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor_id'				=> 'required',
					'customer_code'			=> 'required',
					//'transaction_date'		=> 'required',
				),
				array(
					"vendor_id.required"		=>	trans("The business name field is required."),
					"customer_code.required"	=>	trans("The customer account number field is required."),
					//"transaction_date.required"	=>	trans("The transaction date field is required."),
				)
			);
			if($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$CustomerData = Customer::where('customer_code',$request->input('customer_code'))->first();
				if(empty($CustomerData)){
					Session::flash('error', trans("Customer account number not exist.")); 
					return Redirect::back()->withInput();
				}else{
					if($CustomerData->group_id == Session::get('group')){
						/*echo $request->get('transaction_date');die;
						$transactiondate = date('Y-m-d', strtotime("01-".$request->get('transaction_date')));
						$transaction_date = date("Y-m-t", strtotime($transactiondate));
						echo $transaction_date;die;*/

						$obj 							=  new VendorRebate();
						$obj->vendor_id 				=  $request->input('vendor_id');
						$obj->transaction_date 			=  $date;
						$obj->customer_id 				=  $CustomerData->id;
						$obj->total_purchases 			=  $request->input('total_purchases');
						$obj->none_quality_purchases 	=  $request->input('none_quality_purchases');
						$obj->dropsize 					=  $request->input('dropsize');
						$obj->group_id 					=  Session::get('group');
						$obj->admin_id 					=  auth()->guard('admin')->user()->id;
						$obj->save();
						$userId							=  $obj->id;

						if(!empty($userId)){	
							if(isset($formData['totalrebates']) && !empty($formData['totalrebates'])){
								//VendorRebatePurchase::where('vendor_rebate_id',$userId)->where('type', 1)->delete();
								for($i = 0; $i < count($formData['totalrebates']); $i++){
									$modelO								=  new VendorRebatePurchase();
									$modelO->vendor_rebate_id			=  $userId;
									$modelO->purchase					=  $formData['purchase'][$i];
									$modelO->rebate						=  $formData['rebate'][$i];
									$modelO->type						=  1;
									$modelO->save();
								}
							}
							Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
							return Redirect::to("adminpnlx/import-data/view-import-list/".$date);

						}else{
							echo "heloooo";die;
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}else{
						Session::flash('error', trans("Customer account number does not exist with this group.")); 
						return Redirect::back()->withInput();
					}
				} 
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::to("adminpnlx/import-data/view-import-list/".$date);
			}
		}
	}//end save()


	public function edit($modelId = 0,$date= '', Request $request){
		$model					=	VendorRebate::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		$Customercode =  Customer::where('id', $model->customer_id)->first();
		$purchaseRebates = VendorRebatePurchase::where('vendor_rebate_id',$modelId)->where('type', 1)->get();
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		return  View::make("admin.$this->model.edit",compact('model','vendors', 'purchaseRebates','date','Customercode'));
	} // end edit()

	function update($modelId,$date,Request $request){
		$model					=	VendorRebate::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor_id'				=> 'required',
				),
				array(
					"vendor_id.required"		=>	trans("The business name field is required."),
					//"website.regex"					=>	trans("The website url must be a valid url.")
				)
			);

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 							=  $model;
				$obj->vendor_id 				=  $request->input('vendor_id');
				$obj->total_purchases 			=  $request->input('total_purchases');
				$obj->none_quality_purchases 	=  $request->input('none_quality_purchases');
				$obj->dropsize 					=  $request->input('dropsize');
				$obj->save();
				$userId							=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				//Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					//return Redirect::route($this->model.".ImportDataList", $obj->vendor_id);

				if(!empty($userId)){	
					if(isset($formData['totalrebates']) && !empty($formData['totalrebates'])){
						//echo "<pre>";
						//print_r($formData);die;
						VendorRebatePurchase::where('vendor_rebate_id',$userId)->where('type', 1)->delete();
						for($i = 0; $i < count($formData['purchase']); $i++){
			                $modelO								=  new VendorRebatePurchase();
							$modelO->vendor_rebate_id			=  $userId;
							$modelO->purchase					=  $formData['purchase'][$i];
							$modelO->rebate						=  $formData['rebate'][$i];
							$modelO->type						=  1;
							$modelO->save();
			            }
					}
					Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::to("adminpnlx/import-data/view-import-list/".$date);

				}else{
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
			}
		}
	}// end update()

	public function addMore(){
		$id 	= $_POST['id'];
		$output = 0;
		if(!empty($id)){	
			return  View::make("admin.$this->model.addMore", compact('id'));
		}
	}

	public function deleteVendorRebatePurchase($modelId,$date)
	{
		$modelDetails	=	VendorRebate::where('id',$modelId); 
		if(empty($modelDetails)) {
			return Redirect::to("adminpnlx/import-data/view-import-list/".$date);
		}
		if($modelId){	
			VendorRebate::where('id',$modelId)->delete();
			VendorRebatePurchase::where('vendor_rebate_id',$modelId)->where('type', 1)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end deleteRebate()


}// end ImportExportController

