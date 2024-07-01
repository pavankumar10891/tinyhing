<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\Vendor;
use App\Model\CustomerAddress;
use App\Model\EmailTemplate;
use App\Model\Admin_Country;
use App\Model\State;
use App\Model\City;
use App\Model\EmailAction;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Exports\CustomersVendorsExport;
use App\Model\SendCustomerToVendor;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* EmailCustomerVendorsController Controller
*
* Add your methods in the class below
*
*/
class EmailCustomerVendorsController extends BaseController {

	public $model		=	'EmailCustomerVendors';
	public $sectionName	=	'Email New Customers to Vendors';
	public $sectionNameSingular	=	'Email New Customers to Vendors';
	
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
		//get all active customers
		 $group = Session::get('group');
		$existingCustomers  = Customer::orderBy('corporate_name', 'asc')->where('group_id', $group)->where('is_active', 1)->where("customers.deleted_at", NULL)->pluck("corporate_name","corporate_name")->toArray();

		//get all active vendor
		$existingVendors  = Vendor::where('group_id', $group)->where('is_active', 1)->where("deleted_at", NULL)->where('email', '!=', '')->pluck("business_name","id")->toArray();
		return  View::make("admin.$this->model.index",compact('existingCustomers', 'existingVendors'));
	}// end index()

	/**
		* Function for export customer vendor list 
		*
		* @param null
		*
		* @return view page. 
		*/
	public function sendEemailCustomerVendor(Request $request) {
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor_id'			=> 'required|min:1',
					'message'			=> 'required',
				),
				array(
					"vendor_id.required"		=>	trans("You must select a vendor"),
					"vendor_id.min"				=>	trans("You must select at least 1 Vendor."),
					"message.required"			=>	trans("Message field required"),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				
				$customerArray = array();				
			    $customerId = $request->input('customer_id');

                $i= 1;
				
				if(!empty($customerId)){
					foreach($customerId as $customerIdkey => $customerIdvalue){
						//if($customerIdkey != 0){
							$customerArray[] = $i.'. '.$customerIdvalue;
							$i++;
					   // }
					}
			    }
			    $customer = '';
			    if($request->vendor_only == 1){
			    	$customer  = '';
			    }else{
			      $customer =  implode('<br>', $customerArray);
			    }
			    $vendorId 	=  $request->vendor_id;
			    $emailArray = [];
				if(!empty($vendorId)){
					foreach ($vendorId as $vendorIdkey => $vendorIdvalue) {
					  // if($vendorIdkey != 0){
							$settingsEmail 			=	Config::get('Site.email');
							$full_name				= 	'Vendor New Customers below:'; 
							$email					= 	$vendorIdvalue;
							$message				= 	$request->input('message');
							//$route_url     			= 	URL::to('/adminpnlx/login');
							//$click_link   			=   $route_url;
							$emailActions			= 	EmailAction::where('action','=','customer_to_vendor_email')->get()->toArray();
							$emailTemplates			= 	EmailTemplate::where('action','=','customer_to_vendor_email')->get(array('name','subject','action','body'))->toArray();
							$cons 					= 	explode(',',$emailActions[0]['options']);
							$constants 				= 	array();
							foreach($cons as $key => $val){
								$constants[] 		= 	'{'.$val.'}';
							}
							$subject 				= 	$emailTemplates[0]['subject'];
							$rep_Array 				= 	array($full_name,$message, $customer); 
							$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
							
							$mail	= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

							/*$senemailVendor  =  new SendCustomerToVendor;
							$senemailVendor->email = $vendorIdvalue;
							$senemailVendor->is_send = 1;
							$senemailVendor->customer = $customer;
							$senemailVendor->save();*/

					  // }
					}
			    }
			    Session::flash('success',trans($this->sectionNameSingular." has been sent successfully"));
				return Redirect::route($this->model.".index");
			   
			}
		}
		
	}
	
}// end EmailCustomerVendorsController
