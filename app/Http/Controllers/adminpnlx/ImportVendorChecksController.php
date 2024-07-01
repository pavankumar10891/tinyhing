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
use App\Imports\ImportTemplateExcel;
use App\Model\Group;
use App\Model\ImportVendorChecks;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ImportVendorChecksController Controller
*
* Add your methods in the class below
*
*/
class ImportVendorChecksController extends BaseController {

	public $model		=	'ImportVendorChecks';
	public $sectionName	=	'Import Vendor Checks';
	public $sectionNameSingular	=	'Import';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}



	public function index(Request $request){  
		 
		$DB					=	ImportVendorChecks::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'import_vendor_checks.vendor_id')->select('import_vendor_checks.*', 'vendors.business_name');


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
			if((!empty($searchData['t_start_date'])) && (!empty($searchData['t_enddate_to']))){
				$dateS = $searchData['t_start_date'];
				$dateE = $searchData['t_enddate_to'];
				$searchExportItem['t_start_date']  = $dateS;
				$searchExportItem['t_enddate_to'] 	= $dateE;
				$DB->whereBetween('import_vendor_checks.transaction_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['t_start_date'])){
				$dateS = $searchData['t_start_date'];
				$searchExportItem['t_start_date'] = $dateS;
				$DB->where('import_vendor_checks.transaction_date','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['t_enddate_to'])){
				$dateE = $searchData['t_enddate_to'];
				$searchExportItem['t_enddate_to'] = $dateE;
				$DB->where('import_vendor_checks.transaction_date','<=' ,[$dateE." 00:00:00"]); 						
			}elseif((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('import_vendor_checks.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('import_vendor_checks.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('import_vendor_checks.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("vendors.business_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_name"){
						$searchExportItem['customer_name'] = $fieldValue;
						$DB->where("import_vendor_checks.cus_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_acc_no"){
						$searchExportItem['customer_acc_no'] = $fieldValue;
						$DB->where("import_vendor_checks.cus_acc_no",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("import_vendor_checks.deleted_at",NULL);
		}else{
		   $DB->where("import_vendor_checks.deleted_at",NULL)->where('customer_id', auth()->guard('admin')->user()->id)->where('import_vendor_checks.group_id', Session::get('group'));
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedVendorRecords = $DB->orderBy($sortBy,$order)->groupBy('transaction_date')->get();

		if(Session::has('vendors_export_all_data')) {
			Session::forget('vendors_export_all_data');
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
		$vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();;
		return  View::make("admin.$this->model.index",compact('groups','vendors','results','searchVariable','sortBy','order','query_string'));
	}// end index()

	public function exportAllDataToExcel() {
		return Excel::download(new ImportDataExport, 'importdata-information-'.time().'.xlsx');
	}

	public function importDataView($modelId = 0)
	{
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
		 	$model	=	ImportVendorChecks::leftJoin('vendors', 'vendors.id', ' import_vendor_checks.vendor_id')->select(' import_vendor_checks.*', 'vendors.business_name')->where(' import_vendor_checks.id',"$modelId")->first();
		}else{
			$model	=	ImportVendorChecks::leftJoin('vendors', 'vendors.id', ' import_vendor_checks.vendor_id')->select(' import_vendor_checks.*', 'vendors.business_name')->where(' import_vendor_checks.id',"$modelId")->where(' import_vendor_checks.group_id',Session::get('group'))->where('customer_id', auth()->guard('admin')->user()->id)->first();
		}
		return  View::make("admin.$this->model.importview",compact('model'));
	}
	// delete data by transaction date wise
	public function allDelete ($transaction_date)
	{
		$modelDetails	=	ImportVendorChecks::where('transaction_date',$transaction_date); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ImportDataList");
		}
		if($transaction_date){	
			ImportVendorChecks::where('transaction_date',$transaction_date)->where('group_id',Session::get('group'))->where('customer_id', auth()->guard('admin')->user()->id) ->update(array('deleted_at'=>date('Y-m-d h:i:s')));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}

	// delete data by id wise
	public function delete($id)
	{
		$modelDetails	=	ImportVendorChecks::find($id); 
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".ImportDataList");
		}
		if($id){	
			ImportVendorChecks::where('id',$id)->where('group_id',Session::get('group'))->where('customer_id', auth()->guard('admin')->user()->id) ->update(array('deleted_at'=>date('Y-m-d h:i:s')));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();	
	}




	//Function importVendorCheks data
	public function importVendorCheks(){
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.import", compact('vendors'));
	}//end 

	//Function save saveimportVendorCheks file 
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
					'file' 			=> 'required|mimes:jpg,jpeg',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"file.required"			=>	trans("The file is required."),
					"file.mimes"			=>	trans("Input only jpg file."),
					
				)
			);
			

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{

				$obj = new ImportVendorChecks;
				if($request->hasFile('file')){
					$extension 	=	 $request->file('file')->getClientOriginalExtension();
					$fileName	=	time().'-file.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	VENDOR_CHECKS_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('file')->move($folderPath, $fileName)){
						$obj->image	=	$folderName.$fileName;
					}
				}
				$date = date('Y-m-d', strtotime("01-".$request->input('start_date')));
				$transaction_date = date("Y-m-t", strtotime($date));
				$obj->vendor_id 		= $request->input('vendor');
				$obj->customer_id 		= auth()->guard('admin')->user()->id; 
				$obj->transaction_date 	= $transaction_date;
				$obj->group_id 			= Session::get('group');
				$obj->save();

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}//end function

	public function lookUp()
	{
	  $vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();;
	  return  View::make("admin.$this->model.lookUp", compact('vendors'));
	} 

	 
	public function getlookUpData(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'		=> 'required',
					'start_date'	=> 'required',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
				)
			);
			

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$DB					=	ImportVendorChecks::query();
				$searchVariable		=	array(); 
				$inputGet			=	$request->all();
				$DB->leftJoin('vendors', 'vendors.id', '=', 'import_vendor_checks.vendor_id')->select('import_vendor_checks.*', 'vendors.business_name');
				if($request->all()){
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
				}

				if(auth()->guard('admin')->user()->user_role == "super_admin") {
					$DB->where("import_vendor_checks.deleted_at",NULL);
				}else{
				   $DB->where("import_vendor_checks.deleted_at",NULL)->where('customer_id', auth()->guard('admin')->user()->id)->where('import_vendor_checks.group_id', Session::get('group'));
				}
				$dateS = date('Y-m-d', strtotime("01-".$request->input('start_date')));
				$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
				$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';

				$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
				$results = $DB->where('import_vendor_checks.vendor_id', $request->input('vendor'))->where('import_vendor_checks.transaction_date', '>=', $dateS)->where('import_vendor_checks.deleted_at', NULL)->orderBy($sortBy, $order)->paginate($records_per_page);
				$complete_string		=	$request->query();
				unset($complete_string["sortBy"]);
				unset($complete_string["order"]);
				$query_string			=	http_build_query($complete_string);
				$results->appends($inputGet)->render();
			

			   /* echo "<pre>";
			    print_r($results);
			    die;*/

				$vendors =   Vendor::where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
				return  View::make("admin.$this->model.lookUp",compact('vendors','results', 'sortBy', 'query_string'));

	  			//return  View::make("admin.$this->model.lookUp", compact('vendors'));
			   
			}
		}
	}//end function

	
}// end ImportVendorChecksController

