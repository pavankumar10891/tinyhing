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
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\VendorRebate;
use Illuminate\Http\Request;
use App\Exports\VendorJournal;
use App\Exports\CustomersVendorsExport;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel, CustomHelper;

/**
* VendorTransactionJournalController Controller
*
* Add your methods in the class below
*
*/
class VendorTransactionJournalController extends BaseController {

	public $model		=	'VendorTransactionJournal';
	public $sectionName	=	'Vendor Transaction Journal';
	public $sectionNameSingular	=	'Vendor Transaction Journal';
	
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

		$existingVendors  = Vendor::orderBy('business_name', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();

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
					'start_date'			=> 'required',	
					'end_date'				=> 'required',	
				),
				array(
					"start_customer_id.required"		=>	trans("You must select a Starting Customer."),
					"end_customer_id.required"			=>	trans("You must select a Ending Customer."),
					"vendor_id.required"				=>	trans("You must select a vendor."),
					"start_date.required"				=>	trans("You must select a start date."),
					"end_date.required"					=>	trans("You must select a end date"),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startCustomer 	=  $request->start_customer_id;
				$endCustomer 	=  $request->end_customer_id;
				$startDate = date('Y-m-d', strtotime("01-".$request->start_date));
				//echo $startDate = date("Y-m-t", strtotime($startDate));die;
				$endDate = date('Y-m-d', strtotime("01-".$request->end_date));
				$endDate = date("Y-m-t", strtotime($endDate));
				$vendor 		=  $request->vendor_id;
				$rebatesonly 	=  $request->manufacture_rebat;
				
				//echo "<pre>";print_r($dataarray);die;
				$data = array('startCustomer' => $startCustomer, 'endCustomer' => $endCustomer, 'startDate' => $startDate, 'startEnddate' => $endDate, 'vendor' => $vendor, 'rebatesonly' => $rebatesonly); 
				return Excel::download(new VendorJournal($data), 'VendorJournal-information'.time().'.xlsx');
			   
			}
		}
		
	}

	public function getCustomer(Request $request) 
	{ 
	 	return CustomHelper::getCustomer($request->id);
	}
	
}// end VendorTransactionJournalController
