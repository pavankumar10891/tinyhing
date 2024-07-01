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
use App\Model\VendorExportPayment;
use App\Imports\VendorImport;
use App\Imports\ImportTemplateExcel;
use App\Exports\ExportManufacturerRebate;
use App\Exports\ExportNoncompliance;
use App\Exports\ExportCustomerPaymentData;
use Illuminate\Http\Request;
use PDF;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel,DateTime, CustomHelper;

/**
* ExportPaymentsController Controller
*
* Add your methods in the class below
*
*/
class ExportDeductionCalculationReportController extends BaseController {

	public $model				=	'ExportDeductionCalculationReport';
	public $sectionName			=	'Export Deduction Calculation Report Data';
	public $sectionNameSingular	=	'Export Deduction Calculation Report';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $customers =   Customer::where('is_active', 1)->where('deleted_at', Null)->where('group_id', Session::get('group'))->pluck('corporate_name', 'id')->toArray();
	   $exportpfs= VendorExportPayment::where('type', 8)->where(['group_id' => Session::get('group')])->get();
	  return  View::make("admin.$this->model.exportform", compact('customers','exportpfs'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'start_customer'			=> 'required',
					'end_customer'				=> 'required',
					'start_date'				=> 'required',
					'end_date'					=> 'required',
					'dollar_amount'				=> 'required',
					'per_member_deduction'		=> 'required',
				),
				array(
					"start_customer.required"			=>	trans("The start customer is required."),
					"end_customer.required"				=>	trans("The end customer is required."),
					"start_date.required"				=>	trans("The start date is required."),
					"end_date.required"					=>	trans("The end date is required."),
					'dollar_amount.required'			=>  trans("The amount is required."),
					'per_member_deduction.required'		=>  trans("The per-member deduction amount is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$deductionType 	= 	$request->get("dollar_amount");
				$deductionAmt 	= 	$request->get("per_member_deduction");
				$startDate 		= 	date('Y-m-d', strtotime('01-'.$request->get('start_date')));
				$startDate 		= 	date("Y-m-t", strtotime($startDate));
				$endDate 		= 	date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate 		= 	date("Y-m-t", strtotime($endDate));
				$customerDetails=	DB::table("customers")
									->where("customers.is_active",1)
									->where("customers.is_deleted",0)
									->leftJoin("vendor_rebates","vendor_rebates.customer_id","customers.id")
									->where('vendor_rebates.group_id', Session::get('group'))
									->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
									->select("customers.id","customers.business_name","customers.corporate_name","customers.federal_tax_identification_number","customers.membership_date",
									DB::raw("GROUP_CONCAT(vendor_rebates.id SEPARATOR ',') as rebate_ids"),
									DB::raw("0 as purchases"),
									DB::raw("0 as rebates"),
									DB::raw("0 as expenses"),
									DB::raw("0 as divident"))
									->groupBy("customers.id");
									if(auth()->guard('admin')->user()->user_role == "super_admin"){
										$customerDetails = $customerDetails->get()->toArray();
									}else{
										$customerDetails = $customerDetails->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
									}
				$totalsArr 		= 	array();
				$totalsArr['purchase']	=	0;
				$totalsArr['rebates']	=	0;
				$totalsArr['divident']	=	0;
				$totalsArr['expenses']	=	0;
				if(!empty($customerDetails)){
					foreach($customerDetails as $customerDetail){
						$vencorIdArr 		= 	array();
						$monthDifference 	= 	0;
						$memprice 			= 	0;
						if($deductionType == "annual_fixed_dollar_amount"){
							$memprice 		= 	$deductionAmt/12;
						}elseif($deductionType == "annual_percent_amount"){
							$memprice 		= 	(($deductionAmt/12)*100);
						}elseif($deductionType == "quaterly_fixed_dollar_amount"){
							$memprice 		= 	$deductionAmt/4;
						}elseif($deductionType == "quaterly_percent_amount"){
							$memprice 		= 	(($deductionAmt/4)*100);
						}
						$expences 			= 	0;
						$vencorIdArr 		= 	explode(',',$customerDetail->rebate_ids);
						$purchases 			= 	DB::table("vendor_rebate_purchases")
												->whereIn("id",$vencorIdArr)
												->select(DB::raw("SUM(purchase) as total_p"),
												DB::raw("SUM(rebate) as total_r"))
												->first();
						if(!empty($purchases)){
							$customerDetail->purchases = !empty($purchases->total_p) ? $purchases->total_p : 0;
							$customerDetail->rebates = !empty($purchases->total_r) ? $purchases->total_r : 0;
							$customerDetail->expenses = !empty($purchases->total_r) ? $purchases->total_r : 0;
							$customerDetail->divident = $customerDetail->rebates-$customerDetail->expenses;
						}
						if(strtotime($customerDetail->membership_date) <= strtotime($startDate)){
							$d1			=	new DateTime($endDate); 
							$d2			=	new DateTime($startDate);
							$Months 	= 	$d2->diff($d1); 
							$monthDifference = $Months->m;
							if(($Months->d > 0) || ($Months->days)){
								$monthDifference += 1; 
							}
						}else{
							$d1			=	new DateTime($endDate); 
							$d2			=	new DateTime($customerDetail->membership_date);
							$Months 	= 	$d2->diff($d1); 
							$monthDifference = $Months->m;
							if(($Months->d > 0) || ($Months->days)){
								$monthDifference += 1; 
							}
						}
						$expences = $monthDifference*$memprice;
						if($customerDetail->rebates < $expences){
							$customerDetail->expenses = $expences;
						}

						$totalsArr['purchase'] += $customerDetail->purchases;
						$totalsArr['rebates'] += $customerDetail->rebates;
						$totalsArr['divident'] += $customerDetail->divident;
						$totalsArr['expenses'] += $customerDetail->expenses;
					}
				}
				//echo '<pre>';print_r($customerDetails);
				//echo '<pre>';print_r($totalsArr);die;
				$startCust 	= 	$request->start_customer;
				$endCust 	= 	$request->end_customer;

				$startCust1 = $this->getcustomerById($startCust);
				$EndCust1 = $this->getcustomerById($endCust); 

 				$checkExisData = VendorExportPayment::where('deposit_start_date', $startDate)
				->where('deposit_end_date', $endDate)
				->where('vendor_start_id', $startCust)
				->where('vendor_end_id',$endCust)
				->where('type', 8)
				->first();

				if(empty($checkExisData)){

					$startCust1 = $this->getcustomerById($startCust);
					$EndCust1 = $this->getcustomerById($endCust); 
 					$startCust = $startCust;
 					$endCust = $endCust;
					$startCust1 =  preg_replace('/[^A-Za-z0-9. -]/', '', $startCust1);
					$EndCust1 =  preg_replace('/[^A-Za-z0-9. -]/', '', $EndCust1); 
					$filename = $startCust1.'_'.$EndCust1.'_'.$startDate.'_'.$endDate.'_'.time().'.pdf';
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath = DEDUCTION_CALCULATION_PATH.$folderName;
					$pdf = PDF::loadView("admin.$this->model.export",array('customerDetails' => $customerDetails,'totalsArr' => $totalsArr ,'startDate' => $startDate ,'endDate' => $endDate));
					$customPaper = array(0, 0, 648, 864);
					//$pdf->setPaper($customPaper, 'landscape');
					if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$pdf->save($folderPath. '/' .$filename);
					$paymentExport 						=  new VendorExportPayment;
					$paymentExport->export_file 		= $folderName.$filename;
					$paymentExport->name 				= $filename;
					$paymentExport->deposit_start_date 	= $startDate;
					$paymentExport->deposit_end_date	= $endDate;
					$paymentExport->vendor_start_id		= $startCust;
					$paymentExport->vendor_end_id		= $endCust;
					$paymentExport->type				= 8;
					$paymentExport->admin_id			= Auth::guard('admin')->user()->id;
					$paymentExport->group_id			= Session::get('group');
					$paymentExport->save();
				}
				return Redirect::route('ExportDeductionCalculationReport.export');	

			}
		}
	}//end function 

	public function auditdeletePdf()
	{
		$datas =  VendorExportPayment::where('type', 8)->get();
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			VendorExportPayment::where('type',8)->where('id', $data->id)->delete();
		}
		return Redirect::route('ExportDeductionCalculationReport.export');
	}
	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
}// end ExportPaymentsController

