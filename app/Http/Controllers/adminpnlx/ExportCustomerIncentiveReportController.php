<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\VendorRebate;
use App\Model\Vendor;
use App\Model\ImportTemplate;
use App\Model\ImportManufacturer;
use App\Exports\ExportCustomerIncentiveData;
use Illuminate\Http\Request;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ExportCustomerIncentiveReport Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerIncentiveReportController extends BaseController {
	public $model				=	'ExportCustomerIncentiveReport';
	public $sectionName			=	'Customer Incentive Report';
	public $sectionNameSingular	=	'Customer Incentive Report Information';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
	  $vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	  $data = array();
	  $result = 0;
	  return  View::make("admin.$this->model.exportform", compact('result','vendors','data'));
	}//end function

	public function CustomerIncentiveReport(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData				=	$request->all();
		$searchVariable			=	array(); 
		foreach($formData as $fieldName => $fieldValue){
			$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
		}
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'			=> 'required',
					'start_date'		=> 'required',
					'end_date'			=> 'required',
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
				//$startDate = date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate = date("Y-m-t", strtotime($request->get('start_date')));
				//$endDate   = date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate   = date("Y-m-t", strtotime($request->get('end_date')));
				
				$customerIncentiveData    =     DB::table("vendor_rebates")
												->leftJoin("customers","customers.id","vendor_rebates.customer_id")
												->leftJoin("vendors","vendor_rebates.vendor_id","vendors.id")
												->leftJoin("customer_address","customers.id","customer_address.id")
												->where("vendor_rebates.vendor_id",$request->get('vendor'))
												->where("vendor_rebates.group_id",Session::get('group'))
												->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
												->select("vendor_rebates.id","customers.customer_code","customers.business_name as customer_business_name","vendors.business_name as vendor_business_name","vendor_rebates.total_purchases",
												DB::raw("null as purchase_rebate_data"));
												if(auth()->guard('admin')->user()->user_role == "super_admin"){
													$customerIncentiveData = $customerIncentiveData->get()->toArray();
												}else{
													$customerIncentiveData = $customerIncentiveData->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->get()->toArray();
												}

				$columnCount = 0;
				if(!empty($customerIncentiveData)){
					foreach($customerIncentiveData as &$dt){
						$vendorRebatePurchaseData    =     DB::table("vendor_rebate_purchases")
															->where("vendor_rebate_purchases.vendor_rebate_id",$dt->id)
															->where("vendor_rebate_purchases.type",1)
															->select("vendor_rebate_purchases.purchase","vendor_rebate_purchases.rebate")
															->get()->toArray();

						if(count($vendorRebatePurchaseData) > $columnCount){
							$columnCount = count($vendorRebatePurchaseData);
						}
						$dt->purchase_rebate_data = $vendorRebatePurchaseData;
					}
				}

				$formExportItem = array('start_date' => $startDate ,'end_date' => $endDate, 'vendor' => $request->get('vendor'),'customerIncentiveData'=>$customerIncentiveData,'columnCount'=>$columnCount); 
				if(Session::has('export_incentive_data')) {
					Session::forget('export_incentive_data');
				}
				Session::put('export_incentive_data', $formExportItem);

				//start code for Data for listing on import page
				$exportDataKey = 0;
				$exportData = [];
				$Data = [];
				if(!empty($customerIncentiveData)){
					$total_purchases = 0;
					$total_purchase_data = 0;
					$total_rebate_data = 0;
				    foreach($customerIncentiveData as $dt){
				        $exportData[$exportDataKey]['customer_code']            = $dt->customer_code;
				        $exportData[$exportDataKey]['customer_business_name']   = $dt->customer_business_name;
				        $exportData[$exportDataKey]['total_purchases']      	= $dt->total_purchases;
				        $total_purchases                                       += $dt->total_purchases;
				        $vendorRebatePurchaseData    						    = $dt->purchase_rebate_data; 

				        if(!empty($vendorRebatePurchaseData)){
				        	foreach($vendorRebatePurchaseData as $value){
					            $exportData[$exportDataKey]['data'][]    = $value->purchase;
					            $exportData[$exportDataKey]['data'][]    = $value->rebate;
				        	}
				        	$totalcolumnCount = count($exportData[$exportDataKey]['data']);
							$finalTotalColumnCount = $totalcolumnCount/2;
				        	if($finalTotalColumnCount > $columnCount){
				        		$totalnewcolumn = $finalTotalColumnCount - $columnCount;
								for($i=0;$i<$totalnewcolumn;$i++){
					            	$exportData[$exportDataKey]['data'][]    = 0;
					            	$exportData[$exportDataKey]['data'][]    = 0;
						    	}
							}elseif($finalTotalColumnCount < $columnCount){
				        		$totalnewcolumn = $columnCount - $finalTotalColumnCount;
								for($i=0;$i<$totalnewcolumn;$i++){
					            	$exportData[$exportDataKey]['data'][]    = 0;
					            	$exportData[$exportDataKey]['data'][]    = 0;
						    	}
							}
				        }else{
				        	if($columnCount > 0) {
					            for($i=0;$i<$columnCount;$i++){
						            $exportData[$exportDataKey]['data'][]    = 0;
						            $exportData[$exportDataKey]['data'][]    = 0;
						    	}
					    	}
				        }
				        $exportDataKey++;
				    } 
				    $Data[] ='Totals';
		            $Data[] ='';
		            $Data[] = $total_purchases;

		            if($columnCount > 0) {
			            for($i=0;$i<$columnCount;$i++){
			            	$total_purchase_data = 0;
			            	$total_rebate_data = 0;
				            foreach($customerIncentiveData as $dt){
				            	$vendorRebatePurchaseData  =  $dt->purchase_rebate_data;
					            foreach($vendorRebatePurchaseData as $key => $value){
					            	if($key == $i){
					            		$total_purchase_data += $value->purchase;
						                $total_rebate_data += $value->rebate;
					            	}
					            } 
				        	}
				        	$Data[] = $total_purchase_data;
					        $Data[] = $total_rebate_data;
				    	}
			    	}
				}         
				$result = 1; 
				//end code for Data for listing on import page
			return  View::make("admin.$this->model.exportform",compact('result','customerIncentiveData','vendors','exportData','columnCount','Data','searchVariable'));
			}
		}
	}//end CustomerIncentiveReport function 

	public function export(Request $request){
		if(Session::has('export_incentive_data')){
			$export_incentive_data = Session::get('export_incentive_data');
			return Excel::download(new ExportCustomerIncentiveData($export_incentive_data), 'customer-incentive-report-excel-'.time().'.xlsx');	
		}
	}//end export function 
}// end ExportCustomerIncentiveReportController

