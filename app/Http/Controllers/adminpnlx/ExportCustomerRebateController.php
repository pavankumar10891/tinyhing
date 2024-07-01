<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\VendorRebate;
use App\Exports\ExportCustomerRebateData;
use Illuminate\Http\Request;
use App\Model\VendorExportPayment;
use PDF;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel, CustomHelper;

/**
* ExportCustomerRebate Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerRebateController extends BaseController {

	public $model				=	'ExportCustomerRebate';
	public $sectionName			=	'Export Customer Rebate Data';
	public $sectionNameSingular	=	'Export Customer Rebate';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $customers =   Customer::where('is_active', 1)->where('deleted_at', Null)->where('group_id', Session::get('group'))->pluck('corporate_name', 'id')->toArray();
	  $exportpfs= VendorExportPayment::where('type', 7)->where(['group_id' => Session::get('group')])->get(); 
	  return  View::make("admin.$this->model.exportform", compact('customers', 'exportpfs'));
	  
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
				),
				array(
					"start_customer.required"	=>	trans("The start customer is required."),
					"end_customer.required"		=>	trans("The end customer is required."),
					"start_date.required"		=>	trans("The start date is required."),
					"end_date.required"			=>	trans("The end date is required."),
				)
			);

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startCustomer 	= 	$request->get('start_customer');
				$endCustomer 	= 	$request->get('end_customer');
				$startDate 		= 	date("Y-m-d", strtotime($request->get('start_date')));
				$endDate   		= 	date("Y-m-d", strtotime($request->get('end_date')));
				$customerDetailsArr	=	array();
				 
				$customerDetails    =   DB::table("customers")
										->where("customers.is_deleted",0)
										->where("customers.is_active",1)
										->whereBetween('customers.id', [$startCustomer, $endCustomer])
										->leftJoin("customer_address","customer_address.customer_id","customers.id")
										->where('customers.group_id', Session::get('group'))
										->select("customers.customer_code","customers.corporate_name","customer_address.physical_address_line_1","customer_address.physical_address_city","customer_address.physical_address_state_code","customer_address.physical_address_postal_code","customer_address.physical_address_country","customers.id",
										DB::raw("null as purchase_sub_total"),
										DB::raw("null as rebate_sub_total"),
										DB::raw("null as vender_details"));
										if(auth()->guard('admin')->user()->user_role == "super_admin"){
											$customerDetails = $customerDetails->get()->toArray();
										}else{
											$customerDetails = $customerDetails->where("customers.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
										}
 				if(!empty($customerDetails)){
					foreach($customerDetails as &$customerDetail){
						$purchaseSubTotal	= 0;
						$rebateSubTotal		= 0;
						$customerDetail->customer_code = str_repeat('*', strlen($customerDetail->customer_code) - 4) . substr($customerDetail->customer_code, -4);
						$vendorPurDetails	=	DB::table("vendor_rebates")
												->where("vendor_rebates.customer_id",$customerDetail->id)
												->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
												->where('vendor_rebates.group_id', Session::get('group'))
												->leftJoin("vendors","vendors.id","vendor_rebates.vendor_id")
												->select("vendor_rebates.vendor_id","vendors.business_name","vendor_rebates.customer_id",
  												DB::raw("(SELECT sum(vendor_rebate_purchases.purchase) FROM vendor_rebate_purchases WHERE vendor_rebate_id=vendor_rebates.id) as total_purchases"),
												DB::raw("(SELECT sum(vendor_rebate_purchases.rebate) FROM vendor_rebate_purchases WHERE vendor_rebate_id=vendor_rebates.id) as total_rebates"),
												DB::raw("0 as percentage"))
												->groupby("vendor_rebates.vendor_id")
												->get()
												->toArray(); 
						if(!empty($vendorPurDetails)){
							foreach($vendorPurDetails as $vendorPurDetail){
								$rebatePercentage = 0; 
								
								// echo $rebatePercentage = number_format(($vendorPurDetail->total_purchases*($vendorPurDetail->total_rebates/100)),2);echo '<br/>';
								if($vendorPurDetail->total_purchases != 0){
									$rebatePercentage =number_format((($vendorPurDetail->total_rebates/$vendorPurDetail->total_purchases)*100),2);
								}
								$vendorPurDetail->percentage =$rebatePercentage;
								$purchaseSubTotal 	+=	$vendorPurDetail->total_purchases;
								$rebateSubTotal		+=	$vendorPurDetail->total_rebates;
							}
						}
						$customerDetail->vender_details = $vendorPurDetails;
						$customerDetail->purchase_sub_total = $purchaseSubTotal;
						$customerDetail->rebate_sub_total = $rebateSubTotal;
					}
				}
				//echo '<pre>';print_r($customerDetails);
				//die;
				$data = 	array('start_date' => $startDate ,'end_date' => $endDate,'start_customer' => $startCustomer,'end_customer' => $endCustomer);
				//return  View::make("admin.$this->model.export", compact('customerDetails', 'startDate', 'endDate'));

				$checkExisData = VendorExportPayment::where('deposit_start_date', $startDate)
				->where('deposit_end_date', $endDate)
				->where('vendor_start_id', $startCustomer)
				->where('vendor_end_id',$endCustomer)
				->where('type', 6)
				->first();
				if(empty($checkExisData)){
					$startCustomer = $this->getcustomerById($startCustomer);
					$EndCustomer = $this->getcustomerById($endCustomer);   

					$filename = $startCustomer.'_'.$EndCustomer.'_'.$startDate.'_'.$endDate.'_'.time().'.pdf';
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath = CUSTOMER_REBATE_PERCENT_PATH.$folderName;
					
					$pdf = PDF::loadView("admin.$this->model.export",array('customerDetails' => $customerDetails, 'startDate' => $startDate, 'endDate' => $endDate));
					//$pdf->setPaper('a4', 'landscape');
					$pdf->stream();
					if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$pdf->save($folderPath . '/' . $filename);
					$paymentExport 						=  new VendorExportPayment;
					$paymentExport->export_file 		= $folderName.$filename;
					$paymentExport->name 				= $filename;
					$paymentExport->deposit_start_date 	= $request->input('start_date');
					$paymentExport->deposit_end_date	= $request->input('end_date');
					$paymentExport->vendor_start_id		= $startCustomer;
					$paymentExport->vendor_end_id		= $endCustomer;
					$paymentExport->type				= 7;
					$paymentExport->admin_id			= Auth::guard('admin')->user()->id;
					$paymentExport->group_id			= Session::get('group');
					$paymentExport->save();
				}
				return Redirect::route('ExportCustomerRebate.export');
							
			}
		}
	}//end function 

	public function deletePdf()
	{
		

		$datas =  VendorExportPayment::where('type', 7)->where(['admin_id' => Auth::guard('admin')->user()->id, 'group_id' => Session::get('group')])->get();
		
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			
			VendorExportPayment::where('type', 7)->where('id', $data->id)->where(['admin_id' => Auth::guard('admin')->user()->id, 'group_id' => Session::get('group')])->delete();
		}

		return Redirect::route('ExportCustomerRebate.export');

	}

	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
}// end ExportCustomerRebateController

