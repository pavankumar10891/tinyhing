<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
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

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel, CustomHelper;

/**
* CustomerVendorsController Controller
*
* Add your methods in the class below
*
*/
class CustomerVendorsController extends BaseController {

	public $model		=	'CustomerVendors';
	public $sectionName	=	'Customer Vendor List';
	public $sectionNameSingular	=	'Customer Vendor';
	
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

		$existingCustomers  = Customer::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("customers.deleted_at", NULL)->pluck("corporate_name","id")->toArray();

		return  View::make("admin.$this->model.index",compact('existingCustomers'));
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
					'end_customer_id'	=> 'required',
				),
				array(
					"start_customer_id.required"		=>	trans("You must select a Starting Customer."),
					"end_customer_id.required"		=>	trans("You must select a Ending Customer."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$startCustomerId  = $request->input('start_customer_id');
				$endCustomerId    = $request->input('end_customer_id');
				if($startCustomerId == $endCustomerId){
				   return Excel::download(new CustomersVendorsExport(array($startCustomerId, 0)), 'customer-vendor-list-'.time().'.xlsx');
				}else{
				   return Excel::download(new CustomersVendorsExport(array($startCustomerId, $endCustomerId)), 'customer-vendor-list-'.time().'.xlsx');
				}
			   
			}
		}
		
	}

	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
	
}// end CustomerVendorsController
