<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Model\VendorRebate;
use App\Model\Vendor;
use App\Exports\ExportCustomerContactByVendor;
use Illuminate\Http\Request;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* ExportCustomerContactByVendor Controller
*
* Add your methods in the class below
*
*/
class ExportCustomerContactByVendorController extends BaseController {

	public $model				=	'ExportCustomerContactByVendor';
	public $sectionName			=	'Customer Contact By Vendor Report';
	public $sectionNameSingular	=	'Customer Contact By Vendor Report Information';
	
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
	  return  View::make("admin.$this->model.exportform", compact('result','vendors'));
	}//end ExportForm function

	public function CustomerContactByVendorReport(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		$formData						=	$request->all();
		$searchVariable			=	array(); 
		foreach($formData as $fieldName => $fieldValue){
			$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
		}
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'			=> 'required',
					'start_date'		=> 'required',
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
				$startDate = date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate = date("Y-m-t", strtotime($startDate));
				$endDate = date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate = date("Y-m-t", strtotime($endDate));
				$data 			= 	array('start_date' => $startDate ,'end_date' => $endDate, 'vendor' => $request->vendor);

				if(Session::has('export_contact_by_vendor_data')) {
					Session::forget('export_contact_by_vendor_data');
				}
				Session::put('export_contact_by_vendor_data', $data); 

				//start code for Data for listing on import page
				$customers      =   DB::table("vendors")
		                            ->leftJoin("vendor_rebates","vendor_rebates.vendor_id","vendors.id")
		                            ->leftJoin("customers","customers.id","vendor_rebates.customer_id")
		                            ->leftJoin("customer_address","customer_address.customer_id","customers.id")
		                            ->leftJoin("customer_contacts","customer_contacts.customer_id","customers.id")
		                            ->whereBetween('vendor_rebates.transaction_date', [$startDate, $endDate])
		                            ->where("vendors.id",$request->vendor)
		                            ->where("vendors.is_active",1)
		                            ->where("vendors.deleted_at", Null)
		                            ->where("vendor_rebates.is_deleted", 0)
		                            ->where("vendor_rebates.group_id",Session::get('group'))
		                            ->select("vendor_rebates.id as vendor_rebate_id","customers.customer_code","customers.business_name","vendors.corporate_name","vendors.id","vendor_rebates.customer_id","customer_address.physical_address_line_1","customer_address.physical_address_line_2","customer_address.physical_address_city","customer_address.physical_address_state_code","customer_address.physical_address_postal_code","customer_address.mailing_address_line_1","customer_address.mailing_address_line_2","customer_address.mailing_address_city","customer_address.mailing_address_state_code","customer_address.mailing_address_postal_code","customer_contacts.title","customer_contacts.last_name","customer_contacts.first_name","customer_contacts.email","customer_contacts.business_phone","customer_contacts.cell_phone",
		                            DB::raw("(SELECT SUM(vendor_rebate_purchases.purchase) FROM vendor_rebate_purchases WHERE vendor_rebate_id = vendor_rebates.id) as total_purchases"),
		                            DB::raw("(SELECT SUM(vendor_rebate_purchases.rebate) FROM vendor_rebate_purchases WHERE vendor_rebate_id = vendor_rebates.id) as total_rebates"));
									
									if(auth()->guard('admin')->user()->user_role == "super_admin"){
										$customers = $customers->groupBy('vendor_rebates.customer_id')->get()->toArray();
									}else{
										$customers = $customers->where("vendor_rebates.admin_id",auth()->guard('admin')->user()->id)->groupBy('vendor_rebates.customer_id')->get()->toArray();
									}
									
		        $result = 1;                                 
		        //end code for Data for listing on import page                    
				return  View::make("admin.$this->model.exportform",compact('result','customers','vendors','searchVariable'));			
			}
		}
	}//end CustomerContactByVendorReport function 

	public function export(Request $request){
		if(Session::has('export_contact_by_vendor_data')){
			$export_contact_by_vendor_data = Session::get('export_contact_by_vendor_data');
			return Excel::download(new ExportCustomerContactByVendor($export_contact_by_vendor_data), 'customer-contact-by-vendor-data-'.time().'.xlsx');
		}
	}//end export function 
}// end ExportCustomerContactByVendorController

