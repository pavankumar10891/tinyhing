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
use App\Exports\ExportCustomerIncentiveSummary;
use Illuminate\Http\Request;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ExportCustomerIncentiveSummay Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerIncentiveSummayController extends BaseController {

	public $model				=	'ExportCustomerIncentiveSummay';
	public $sectionName			=	'Customer Incentive Summary';
	public $sectionNameSingular	=	'Customer Incentive Summary Information';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	   $result = 0;
	  return  View::make("admin.$this->model.exportform", compact('vendors','result'));
	}//end function

	public function CustomerIncentiveSummary(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData				=	$request->all();
		$searchVariable			=	array(); 
		foreach($formData as $fieldName => $fieldValue){
			$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
		}
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'start_date'		=> 'required',
					'end_date'			=> 'required',
				),
				array(
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				//$startDate = date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$newstartDate = date("Y-m-t", strtotime($request->get('start_date')));
				//$endDate = date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$newendDate = date("Y-m-t", strtotime($request->get('end_date')));
				$data 			= 	array('start_date' => $newstartDate ,'end_date' => $newendDate); 
				
				$customers      =   DB::table("vendors")
		                            ->leftJoin("vendor_rebates","vendor_rebates.vendor_id","vendors.id")
		                            //->leftJoin("vendors","vendors.id","vendor_rebates.vendor_id")
		                            ->whereBetween('vendor_rebates.transaction_date', [$newstartDate, $newendDate])
		                            ->where("vendors.is_active",1)
		                            ->where("vendors.deleted_at", Null)
		                            ->where("vendor_rebates.is_deleted", 0)
		                            ->where("vendor_rebates.group_id",Session::get('group'))
		                            ->whereBetween('vendor_rebates.transaction_date', [$newstartDate, $newendDate])
		                            ->select("vendors.id","vendors.business_name","vendors.address_line_1",
		                            DB::raw("(SELECT SUM(vendor_rebate_purchases.purchase) FROM vendor_rebate_purchases WHERE vendor_rebate_id = vendor_rebates.id) as total_purchases"),
		                            DB::raw("(SELECT SUM(vendor_rebate_purchases.rebate) FROM vendor_rebate_purchases WHERE vendor_rebate_id = vendor_rebates.id) as total_rebates"),
		                            DB::raw("(SELECT COUNT(t.id) FROM vendor_rebates as t WHERE t.vendor_id = vendors.id AND t.transaction_date BETWEEN '".$newstartDate."' AND '".$newendDate."') as transactions"),
		                            DB::raw("(SELECT COUNT(c.customer_id) FROM vendor_rebates as c WHERE c.vendor_id = vendors.id AND c.transaction_date BETWEEN '".$newstartDate."' AND '".$newendDate."') as customers")
									);
		                            if(auth()->guard('admin')->user()->user_role == "super_admin"){
										$customers = $customers->groupBy('vendor_id')->get()->toArray();
									}else{
										$customers = $customers->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->groupBy('vendor_id')->get()->toArray();
									}  
         
                $total_sales = 0;
		        $total_rebates = 0;
		        $total_transactions = 0;
		        $total_customers = 0;   
                foreach($customers as $record){  
		            $total_sales                                 +=   !empty($record->total_purchases)?$record->total_purchases:'0.00';
		            $total_rebates                               +=   !empty($record->total_rebates)?$record->total_rebates:'0.00';
		            $total_transactions                          +=   !empty($record->transactions)?$record->transactions:'0.00';
		            $total_customers                             +=   !empty($record->customers)?$record->customers:'0.00';  
		        }     
		        $Data[] ='Totals';
	            $Data[] ='';
	            $Data[] ='';
	            $Data[] = $total_sales;  
	            $Data[] = $total_rebates;
	            $Data[] = $total_transactions;
	            $Data[] = $total_customers;     
	            $exportData = array();
	            $exportData['data'] = $data;
	            $exportData['customers'] = $customers;

	            if(Session::has('export_incentive_summary_data')) {
					Session::forget('export_incentive_summary_data');
				}
				Session::put('export_incentive_summary_data', $exportData);
				$result = 1; 
				return  View::make("admin.$this->model.exportform",compact('result','customers','searchVariable','Data'));
			}
		}
	}//end CustomerIncentiveReport function 

	public function export(Request $request){
		if(Session::has('export_incentive_summary_data')){
			$export_incentive_summary_data = Session::get('export_incentive_summary_data');
			return Excel::download(new ExportCustomerIncentiveSummary($export_incentive_summary_data), 'customer-incentive-summary-report-'.time().'.xlsx');
		}
	}//end export function 
}// end ExportCustomerIncentiveSummayController

