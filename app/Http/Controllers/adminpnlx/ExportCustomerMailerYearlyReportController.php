<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\VendorRebatePurchase;
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
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel,CustomHelper;
use PDF;

/**
* ExportPaymentsController Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerMailerYearlyReportController extends BaseController {

	public $model				=	'ExportCustomerMailerYearlyReport';
	public $sectionName			=	'Export Customer Mailer Yearly Data';
	public $sectionNameSingular	=	'Export Customer Mailer Yearly';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $customers =   Customer::where('is_active', 1)->where('group_id', Session::get('group'))->where('deleted_at', Null)->pluck('corporate_name', 'id')->toArray();
	  if(auth()->guard('admin')->user()->user_role == "super_admin"){
		$exportpfs= VendorExportPayment::where('type', 5)->where(['group_id' => Session::get('group')])->get();
	  }else{
		$exportpfs= VendorExportPayment::where('type', 5)->where(['admin_id' => Auth::guard('admin')->user()->id, 'group_id' => Session::get('group')])->get();

	  }
	  return  View::make("admin.$this->model.exportform", compact('customers','exportpfs'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'start_customer'	=> 'required',
					'end_customer'		=> 'required',
					'start_date'		=> 'required',
					'end_date'			=> 'required',
					'deduction_per'		=> 'required',
				),
				array(
					"start_customer.required"		=>	trans("The start customer is required."),
					"end_customer.required"		=>	trans("The end customer is required."),
					"start_date.required"	=>	trans("The start date is required."),
					"end_date.required"		=>	trans("The end date is required."),
					"deduction_per.required"		=>	trans("The deduction percent to compute below is required."),
				)
			);

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startCust  =   $request->get('start_customer');
				$endCust    =   $request->get('end_customer');
				$startDate  =   date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate  =   date("Y-m-t", strtotime($startDate));
				$endDate    =   date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate    =   date("Y-m-t", strtotime($endDate));
				$data 		= 	array('start_date' => $startDate ,'end_date' => $endDate, 'customer_id' => $request->customer_id); 
				$deductionPer    =  $request->get('deduction_per');
			
				$customerArray = array();
				$customerDetails    =   DB::table("customers")
										->where("customers.is_deleted",0)
										->where("customers.is_active",1)
										->whereBetween('customers.id', [$startCust, $endCust])
										->leftJoin("customer_address","customer_address.customer_id","customers.id")
										->where('customers.group_id', Session::get('group'))
										->select("customers.customer_code","customers.corporate_name","customer_address.physical_address_line_1","customer_address.physical_address_city","customer_address.physical_address_state_code","customer_address.physical_address_postal_code","customer_address.physical_address_country","customers.id",
										DB::raw("null as purchase_sub_total"),
										DB::raw("null as dropsize_sub_total"),
										DB::raw("null as mfr_rebate_sub_total"),
										DB::raw("null as mfr_actual_sub_total"),
										DB::raw("null as composite_saving"),
										DB::raw("null as ytd_detail"),
										DB::raw("null as year_start_date"),
										DB::raw("null as year_end_date")
										);
										if(auth()->guard('admin')->user()->user_role == "super_admin"){
											$customerDetails = $customerDetails->get()->toArray();
										}else{
											$customerDetails = $customerDetails->where("customers.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
										}
										

				if(!empty($customerDetails)){
					foreach($customerDetails as &$customerDetail){
						$customerDetail->customer_code = str_repeat('*', strlen($customerDetail->customer_code) - 4) . substr($customerDetail->customer_code, -4);
						$compositeSavingAmt = 0;
						$compositeSavingPer = 0;
						$purchaseSubTotal   = 0;
						$dropsizeSubTotal   = 0;
						$mfrRebateSubTotal  = 0;
						$mfractPriceSubTotal= 0;
						$vendorPurDetails	=	DB::table("vendor_rebates")
												->where("vendor_rebates.customer_id",$customerDetail->id)
												->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
												->where('vendor_rebates.group_id', Session::get('group'))
												->leftJoin("vendors","vendors.id","vendor_rebates.vendor_id")
												->select("vendor_rebates.vendor_id","vendors.business_name","vendor_rebates.customer_id",
												DB::raw("SUM(vendor_rebates.total_purchases) as total_purchases"),
												DB::raw("SUM(vendor_rebates.dropsize) as dropsize"),
												DB::raw("(SELECT GROUP_CONCAT(manufacturer_rebates.id SEPARATOR ',') FROM manufacturer_rebates WHERE manufacturer_rebates.transaction_date BETWEEN '$startDate' and '$endDate' AND manufacturer_rebates.vendor_id = vendor_rebates.vendor_id) as mfr_ids"),
												DB::raw("(SELECT sum(manufature_vendor_rebates.total_purchases) FROM manufature_vendor_rebates WHERE vendor_id=vendor_rebates.vendor_id AND customer_id = vendor_rebates.customer_id) as mfr_rebate"),
												DB::raw("( SELECT 
												SUM(manufacturer_rebate_details.amount) 
												FROM 
												manufacturer_rebate_details 
												LEFT JOIN manufacturer_rebates on manufacturer_rebates.id = manufacturer_rebate_details.manufacturer_rebate_id 
												WHERE 
												manufacturer_rebate_details.customer_id = vendor_rebates.customer_id) as mfr_actual_price"))
												->groupby("vendor_rebates.vendor_id");
												if(auth()->guard('admin')->user()->user_role == "super_admin"){
													$vendorPurDetails = $vendorPurDetails->get()->toArray();
												}else{
													$vendorPurDetails = $vendorPurDetails->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
												}
						if(!empty($vendorPurDetails)){
							foreach($vendorPurDetails as $vendorPurDetail){
								$purchaseSubTotal   +=   $vendorPurDetail->total_purchases;
								$dropsizeSubTotal   +=   $vendorPurDetail->dropsize;
								$mfrRebateSubTotal  +=   $vendorPurDetail->mfr_rebate;
								$mfractPriceSubTotal    +=   $vendorPurDetail->mfr_actual_price;
							}
						}
						$customerDetail->vender_details = $vendorPurDetails;
						$customerDetail->purchase_sub_total     = $purchaseSubTotal;
						$customerDetail->dropsize_sub_total     = $dropsizeSubTotal;
						$customerDetail->mfr_rebate_sub_total   = $mfrRebateSubTotal;
						$customerDetail->mfr_actual_sub_total   = $mfractPriceSubTotal;
						$ytdSummaryStartDate 	=   date("Y").'-10-01';
						$ytdSummaryEndTime 		= 	strtotime($ytdSummaryStartDate.' -1 year -1 day');
						$ytdSummaryEndDate 		=   date('Y-m-d',$ytdSummaryEndTime);	
						$customerDetail->year_start_date   = $ytdSummaryEndDate;
						$customerDetail->year_end_date   = $ytdSummaryStartDate;
						$ytdsummary				=	DB::table("vendor_rebates")
													->where("vendor_rebates.customer_id",$customerDetail->id)
													->whereBetween('vendor_rebates.transaction_date', [$ytdSummaryEndDate,$ytdSummaryStartDate])
													->where('vendor_rebates.admin_id', Auth::guard('admin')->user()->id)
													->where('vendor_rebates.group_id', Session::get('group'))
													 ->select(
													DB::raw("SUM(vendor_rebates.total_purchases) as total_purchases"),
													DB::raw("SUM(vendor_rebates.dropsize) as dropsize"),
													DB::raw("(SELECT sum(manufature_vendor_rebates.total_purchases) FROM manufature_vendor_rebates WHERE customer_id = vendor_rebates.customer_id) as mfr_rebate"),
													DB::raw("( SELECT SUM(manufacturer_rebate_details.amount) FROM manufacturer_rebate_details LEFT JOIN manufacturer_rebates on manufacturer_rebates.id = manufacturer_rebate_details.manufacturer_rebate_id WHERE manufacturer_rebate_details.customer_id = vendor_rebates.customer_id) as mfr_actual_price",
													DB::raw("0 as composit_saving")));
													if(auth()->guard('admin')->user()->user_role == "super_admin"){
														$ytdsummary = $ytdsummary->first();
													}else{
														$ytdsummary = $ytdsummary->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->first();
													}
													 
		
													//  select , ,,  from `vendor_rebates` where `vendor_rebates`.`customer_id` = 136 and `vendor_rebates`.`transaction_date` between '2020-30-09' and '2021-10-01' and `vendor_rebates`.`admin_id` = 12 and `vendor_rebates`.`group_id` = 1
													 
						if(!empty($ytdsummary)){
							$compositeSavingAmt= $ytdsummary->total_purchases - $ytdsummary->dropsize;
							$compositeSavingPer = (($compositeSavingAmt/100)*$deductionPer);
							$ytdsummary->composit_saving   = $compositeSavingPer;
							$customerDetail->ytd_detail = $ytdsummary;
						}
					}
				}
				//return  View::make("admin.$this->model.export",compact('customerDetails'));
				$startCust1 = $this->getcustomerById($startCust);
				$EndCust1 = $this->getcustomerById($endCust); 

 				$checkExisData = VendorExportPayment::where('deposit_start_date', $startDate)
				->where('deposit_end_date', $endDate)
				->where('vendor_start_id', $startCust)
				->where('vendor_end_id',$endCust)
				->where('type', 5)
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
					$folderPath = CUSTOMER_YEARLY_PATH.$folderName;
					$pdf = PDF::loadView("admin.$this->model.export",array('customerDetails' => $customerDetails));
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
					$paymentExport->type				= 5;
					$paymentExport->admin_id			= Auth::guard('admin')->user()->id;
					$paymentExport->group_id			= Session::get('group');
					$paymentExport->save();
				}
				return Redirect::route('ExportCustomerMailerYearlyReport.export');		
			}
		}
	}//end function 

	public function auditdeletePdf()
	{
		$datas =  VendorExportPayment::where('type', 5)->get();
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			VendorExportPayment::where('type',5)->where('id', $data->id)->delete();
		}
		return Redirect::route('ExportCustomerMailerYearlyReport.export');
	}

	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
}// end ExportPaymentsController

