<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\Vendor;
use App\Model\VendorRebate;
use App\Model\ImportTemplate;
use App\Model\ImportManufacturer;
use App\Imports\VendorImport;
use App\Imports\ImportTemplateExcel;
use App\Exports\ExportManufacturerRebate;
use App\Exports\ExportPurchaseRebateReport;
use App\Exports\ExportCustomerPaymentData;
use App\Model\VendorRebatePurchase;
use Illuminate\Http\Request;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ExportPaymentsController Controller
*
* Add your methods in the class below
*
*/
class ExportPurchaseRebateReportController extends BaseController {

	public $model				=	'ExportPurchaseRebateReport';
	public $sectionName			=	'Export Purchase Rebate Report Data';
	public $sectionNameSingular	=	'Export Purchase Rebate Report';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}


		 

	public function ExportForm(){
	  $customers =   Customer::where('is_active', 1)->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  return  View::make("admin.$this->model.exportform", compact('customers','vendors'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'			=> 'required',
					'customer'			=> 'required',
					'start_date'		=> 'required',
					'end_date'		=> 'required',
				),
				array(
					"vendor.required"		=>	trans("The vendor is required."),
					"customer.required"		=>	trans("The customer is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startDate 	= 	date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate 	= 	date("Y-m-t", strtotime($startDate));
				$endDate 	= 	date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate 	= 	date("Y-m-t", strtotime($endDate));
				$data 		= 	array('start_date' => $startDate ,'end_date' => $endDate, 'vendor' => $request->vendor, 'customer' => $request->customer); 
				return Excel::download(new ExportPurchaseRebateReport($data), 'customer-purchase-rebare-report'.time().'.xlsx');	
			}
		}
	}//end function 
}// end ExportPaymentsController

