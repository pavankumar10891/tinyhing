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
use App\Exports\ExportNoncompliance;
use App\Exports\ExportCustomerPaymentData;
use Illuminate\Http\Request;
use App\Model\VendorRebatePurchase;
use App\Model\VendorExportPayment;
use PDF;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel, CustomHelper;

/**
* ExportPaymentsController Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerAuditReportController extends BaseController {
	public $model				=	'ExportCustomerAuditReport';
	public $sectionName			=	'Export Customer Audit Report Data';
	public $sectionNameSingular	=	'Export Customer Audit Report';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  $exportpfs= VendorExportPayment::where('type', 4)->where(['group_id' => Session::get('group')])->get();
	  
	  return  View::make("admin.$this->model.exportform", compact('vendors', 'exportpfs'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'start_vendor'			=> 'required',
					'end_vendor'			=> 'required',
					'start_date'			=> 'required',
					'end_date'				=> 'required',
				),
				array(
					"start_vendor.required"		=>	trans("The start vendor is required."),
					"end_vendor.required"		=>	trans("The end vendor is required."),
					"start_date.required"		=>	trans("The start date is required."),
					"end_date.required"			=>	trans("The end date is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$auditReportArr	=	array();
				$auditReportArr['grandtotal']['amount1total'] = 0;
				$auditReportArr['grandtotal']['amount2total'] = 0;
				$auditReportArr['grandtotal']['amount3total'] = 0;
				$auditReportArr['grandtotal']['amount4total'] = 0;
				$auditReportArr['grandtotal']['computetotal'] = 0;
				$auditReportArr['grandtotal']['actualtotal']  = 0;
				$venderStart	=	!empty($request->get('start_vendor'))?$request->get('start_vendor'):'';
				$venderEnd		=	!empty($request->get('end_vendor')) ? $request->get('end_vendor'):'';
				$startDate		=	!empty($request->get('start_date')) ? $request->get('start_date'):'';
				$endDate		=	!empty($request->get('end_date')) ? $request->get('end_date'):'';
				$venderDetails	=	DB::table("vendors")
									->whereBetween('vendors.id', [$venderStart, $venderEnd])
									->where("vendors.is_active",1)
		                            ->where("vendors.deleted_at", Null)
									->where("vendors.group_id",Session::get('group'))
									->leftJoin("vendor_rebates","vendor_rebates.vendor_id","vendors.id")
									->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
									->groupBy("vendors.id")
									->groupBy("vendor_rebates.transaction_date")
									->select("vendors.business_name","vendors.id","vendor_rebates.transaction_date");
									if(auth()->guard('admin')->user()->user_role == "super_admin"){
										$venderDetails = $venderDetails->get()->toArray();
									}else{
										$venderDetails = $venderDetails->where("vendors.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
									}

				if(!empty($venderDetails)){
					foreach($venderDetails as $venderDetail){ 
						$auditReportArr['grandtotal']['amount1total'] = 0;
						$auditReportArr['grandtotal']['amount2total'] = 0;
						$auditReportArr['grandtotal']['amount3total'] = 0;
						$auditReportArr['grandtotal']['amount4total'] = 0;
						$auditReportArr['grandtotal']['computetotal'] = 0;
						$auditReportArr['grandtotal']['actualtotal']  = 0;
						$transactionData			=	DB::table("vendor_rebates")
														->where("vendor_rebates.vendor_id",$venderDetail->id)
														->leftJoin("vendor_rebate_purchases","vendor_rebate_purchases.vendor_rebate_id","vendor_rebates.id")
														->where('vendor_rebates.transaction_date', $venderDetail->transaction_date)
														->select("vendor_rebate_purchases.rebate",
														DB::raw("GROUP_CONCAT(vendor_rebate_purchases.rebate SEPARATOR ',') as rebate_str"))
														->orderBy('vendor_rebates.transaction_date',"DESC")
														->first(); 
						if(!empty($transactionData)){
							$rebatarra 		= 	array();
							$rebatarra 		=	explode(',',$transactionData->rebate_str);
							$transactoiDArr['business_name'] = $venderDetail->business_name;
							$transactoiDArr['amount1'] 	= 	!empty($rebatarra[0]) ? $rebatarra[0] : 0;
							$transactoiDArr['amount2'] 	= 	!empty($rebatarra[1]) ? $rebatarra[1] : 0;
							$transactoiDArr['amount3'] 	= 	!empty($rebatarra[2]) ? $rebatarra[2] : 0;
							$transactoiDArr['amount4'] 	= 	!empty($rebatarra[3]) ? $rebatarra[3] : 0;
							$transactoiDArr['computed']	= 	$transactoiDArr['amount1']+$transactoiDArr['amount2']+$transactoiDArr['amount3']+$transactoiDArr['amount4'];
							$transactoiDArr['actual']  	= 	$transactoiDArr['amount1']+$transactoiDArr['amount2']+$transactoiDArr['amount3']+$transactoiDArr['amount4'];
							 
							$auditReportArr[$venderDetail->transaction_date]['data'][] 		= 	$transactoiDArr;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['amount1subtotal'] 		= 	0;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['amount2subtotal'] 		= 	0;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['amount3subtotal'] 		= 	0;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['amount4subtotal'] 		= 	0;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['computesubtotal'] 		= 	0;
							$auditReportArr[$venderDetail->transaction_date]['subtotal']['actualsubtotal'] 			= 	0;
						}
					}
					if(!empty($auditReportArr)){
						$amount1grandtota = 0;
						$amount2grandtota = 0;
						$amount3grandtota = 0;
						$amount4grandtota = 0;
						$computegrandtota = 0;
						$actualgrandtota = 0;
						foreach($auditReportArr as $key => $value){
							if(!empty($value['data'])){
								foreach($value as $k => $v){
									foreach($v as $data){
										if(!empty($data['amount1'])){
											$auditReportArr[$key]['subtotal']['amount1subtotal']+=$data['amount1'];
											$auditReportArr[$key]['subtotal']['amount2subtotal']+=$data['amount2'];
											$auditReportArr[$key]['subtotal']['amount3subtotal']+=$data['amount3'];
											$auditReportArr[$key]['subtotal']['amount4subtotal']+=$data['amount4'];
											$auditReportArr[$key]['subtotal']['computesubtotal']+=$data['computed'];
											$auditReportArr[$key]['subtotal']['actualsubtotal'] +=$data['actual'];
										}
									}
								}
								$auditReportArr[$key]['subtotal']['amount1subtotal'];echo '<br/>';
								$amount1grandtota +=$auditReportArr[$key]['subtotal']['amount1subtotal'];
								$amount2grandtota +=$auditReportArr[$key]['subtotal']['amount2subtotal'];
								$amount3grandtota +=$auditReportArr[$key]['subtotal']['amount3subtotal'];
								$amount4grandtota +=$auditReportArr[$key]['subtotal']['amount4subtotal'];
								$computegrandtota +=$auditReportArr[$key]['subtotal']['computesubtotal'];
								$actualgrandtota  +=$auditReportArr[$key]['subtotal']['actualsubtotal'];
							}
						}
						$auditReportArr['grandtotal']['amount1total'] = $amount1grandtota;
						$auditReportArr['grandtotal']['amount2total'] = $amount2grandtota;
						$auditReportArr['grandtotal']['amount3total'] = $amount3grandtota;
						$auditReportArr['grandtotal']['amount4total'] = $amount4grandtota;
						$auditReportArr['grandtotal']['computetotal'] = $computegrandtota;
						$auditReportArr['grandtotal']['actualtotal'] =  $actualgrandtota;
					}
				}
				 $startVendor = $this->getvendorById($venderStart);
				 $EndVendor   =  $this->getvendorById($venderEnd);
				// die;
				// echo '<pre>';print_r($auditReportArr);die;
				//return  View::make("admin.$this->model.export",compact('auditReportArr'));
				//die;
				$checkExisData = VendorExportPayment::where('deposit_start_date', $startDate)
				->where('deposit_end_date', $endDate)
				->where('vendor_start_id', $venderStart)
				->where('vendor_end_id',$venderEnd)
				->where('type', 4)
				->first();
				if(empty($checkExisData)){
					

					$startVendor = $this->getvendorById($venderStart);
					$EndVendor = $this->getvendorById($venderEnd);   
					$startVendor =  preg_replace('/[^A-Za-z0-9. -]/', '', $startVendor);
					$EndVendor =  preg_replace('/[^A-Za-z0-9. -]/', '', $EndVendor);
					
					$filename = $startVendor.'_'.$EndVendor.'_'.$startDate.'_'.$endDate.'_'.time().'.pdf';
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath = VENDOR_AUDIT_PATH.$folderName;
					$pdf = PDF::loadView("admin.$this->model.export",array('auditReportArr' => $auditReportArr));
				//	$customPaper = array(0, 0, 648, 864)
					//$pdf->setPaper($customPaper, 'portrait');
					if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$pdf->save($folderPath . '/' . $filename);
					$paymentExport 						=  new VendorExportPayment;
					$paymentExport->export_file 		= $folderName.$filename;
					$paymentExport->name 				= $filename;
					$paymentExport->deposit_start_date 	= $request->input('start_date');
					$paymentExport->deposit_end_date	= $request->input('end_date');
					$paymentExport->vendor_start_id		= $request->input('start_vendor');
					$paymentExport->vendor_end_id		= $request->input('end_vendor');
					$paymentExport->type				= 4;
					$paymentExport->admin_id			= Auth::guard('admin')->user()->id;
					$paymentExport->group_id			= Session::get('group');
					$paymentExport->save();
				}
				return Redirect::route('ExportCustomerAuditReport.export');
				
			}
		}
	}//end function 


	public function auditdeletePdf()
	{
		

		$datas =  VendorExportPayment::where('type', 4)->where(['group_id' => Session::get('group')])->get();
		
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			
			VendorExportPayment::where('type', 4)->where('id', $data->id)->delete();
		}

		return Redirect::route('ExportCustomerAuditReport.export');

	}

	public function getVendor(Request $request) 
	{ 
	 	return CustomHelper::getVendor($request->id);
	}
}// end ExportPaymentsController

