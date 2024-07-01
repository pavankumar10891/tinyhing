<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\VendorRebate;
use App\Model\ImportTemplate;
use App\Model\ImportManufacturer;
use App\Imports\VendorImport;
use App\Imports\ImportTemplateExcel;
use App\Exports\ExportManufacturerRebate;
use App\Exports\ExportNoncompliance;
use App\Exports\ExportCustomerPaymentData;
use Illuminate\Http\Request;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ExportPaymentsController Controller
*
* Add your methods in the class below
*
*/
class ExportPaymentsController extends BaseController {

	public $model				=	'ExportPayments';
	public $sectionName			=	'Export Customer Payment Data';
	public $sectionNameSingular	=	'Export Payment';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}



	public function index(Request $request){  
		 
		$DB					=	ImportManufacturer::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'import_manufature_data.vendor_id')->select('import_manufature_data.*', 'vendors.corporate_name');

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
				$DB->whereBetween('import_manufature_data.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('import_manufature_data.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('import_manufature_data.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("vendors.corporate_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_name"){
						$searchExportItem['customer_name'] = $fieldValue;
						$DB->where("import_manufature_data.cus_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_acc_no"){
						$searchExportItem['customer_acc_no'] = $fieldValue;
						$DB->where("import_manufature_data.cus_acc_no",$fieldValue);
					}

				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("import_manufature_data.deleted_at",NULL);
		}else{
		   $DB->where("import_manufature_data.deleted_at",NULL)->where('customer_id', auth()->guard('admin')->user()->id)->where('import_manufature_data.group_id', Session::get('group'));
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
		
		return  View::make("admin.$this->model.index",compact('groups','results','searchVariable','sortBy','order','query_string'));
	}// end index()

	public function ExportForm(){
	  $customers =   Customer::where('is_active', 1)->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();
	  return  View::make("admin.$this->model.exportform", compact('customers'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'customer_id'			=> 'required',
					'start_date'		=> 'required',
					'end_date'		=> 'required',
				),
				array(
					"customer_id.required"		=>	trans("The customer is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startDate = date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate = date("Y-m-t", strtotime($startDate));
				$endDate = date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate = date("Y-m-t", strtotime($endDate));
				$data 			= 	array('start_date' => $startDate ,'end_date' => $endDate, 'customer_id' => $request->customer_id); 
				return Excel::download(new ExportCustomerPaymentData($data), 'customer-payment-rebate-data-'.time().'.xlsx');			
			}
		}
	}//end function 
}// end ExportPaymentsController

