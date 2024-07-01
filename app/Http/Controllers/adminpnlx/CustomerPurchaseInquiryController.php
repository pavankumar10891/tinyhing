<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\CustomerAddress;
use App\Model\EmailTemplate;
use App\Model\Admin_Country;
use App\Model\State;
use App\Model\City;
use App\Model\Vendor;
use App\Model\EmailAction;
use App\Model\VendorRebate;
use App\Model\VendorRebatePurchase;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Exports\CustomersVendorsExport;
use App\Exports\CustomersPurchaseInquiryExport;


use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel,CustomHelper;

/**
* CustomerPurchaseInquiryController Controller
*
* Add your methods in the class below
*
*/
class CustomerPurchaseInquiryController extends BaseController {

	public $model		=	'CustomerPurchaseInquiry';
	public $sectionName	=	'Customer Purchasing Inquiry';
	public $sectionNameSingular	=	'Customer Purchasing Inquiry';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for customer vendor list 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  

		$existingCustomers  = Customer::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where("customers.deleted_at", NULL)->pluck("corporate_name","id")->toArray();

		$existingVendors  = Vendor::orderBy('id', 'asc')->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();

		return  View::make("admin.$this->model.index",compact('existingCustomers', 'existingVendors'));
	}// end index()


	/**
		* Function for export customer vendor list 
		*
		* @param null
		*
		* @return view page. 
		*/
	public function exportAllDataToExcel(Request $request) {
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'start_customer_id'		=> 'required',
					'end_customer_id'		=> 'required',
					'vendor_id'				=> 'required',
				),
				array(
					"start_customer_id.required"	=>	trans("You must select a Starting Customer."),
					"end_customer_id.required"		=>	trans("You must select a Ending Customer."),
					'vendor_id.required'		    =>  trans("You must select a vendor."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$startCustomerId  	= $request->input('start_customer_id');
				$endCustomerId    	= $request->input('end_customer_id');
				$vendor  			= $request->input('vendor_id');
				$startDate    		= !empty($request->input('start_date')) ? '01-'.$request->input('start_date') :'';
				$startDate 			= date("Y-m-d", strtotime($startDate)); 
				$endDate    		= !empty($request->input('end_date')) ? date('Y-m-d',strtotime('1-'.$request->input('end_date'))): '';
				$endDate 			= date("Y-m-t", strtotime($endDate));
				$ddArray = array('0' => $startCustomerId, '1' => $endCustomerId, '2' => $vendor, '3' => $startDate, '4' => $endDate);
				return Excel::download(new CustomersPurchaseInquiryExport($ddArray), 'customer-purchase-inquiry-'.time().'.xlsx');
			}
		}
		
	}

	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
	
}// end CustomerPurchaseInquiryController
