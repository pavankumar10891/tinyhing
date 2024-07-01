<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\CustomerAddress;
use App\Model\EmailTemplate;
use App\Model\AdminCountry;
use App\Model\State;
use App\Model\Vendor;
use App\Model\EmailAction;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\CustomerContact;
use App\Model\VendorAdmin;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Exports\CustomersContactExport;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;
use App\Model\Group;

/**
* VendorAdminController Controller
*
* Add your methods in the class below
*
*/
class VendorAdminController extends BaseController {

	public $model		=	'VendorAdmin';
	public $sectionName	=	"Customer's Vendor Admin";
	public $sectionNameSingular	=	"Customer's Vendor Admin";
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Vendor Admin 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index($customerId='', Request $request){
	    $items = $request->per_page ?? Config::get("Reading.records_per_page"); 
		$DB					=	VendorAdmin::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();

		$DB->leftJoin('vendors', 'vendors.id', '=', 'cus_vendor_admins.vendor_id');
		$DB->leftJoin('customers', 'customers.id', '=', 'cus_vendor_admins.customer_id');
		$DB->select('cus_vendor_admins.id','cus_vendor_admins.customer_id',  'cus_vendor_admins.vendor_code', 'customers.corporate_name', 'vendors.business_name as current_vendaor', 'cus_vendor_admins.is_active', 'cus_vendor_admins.created_at', 'cus_vendor_admins.updated_at');

		if ($request->all()) {
			$searchData			=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('cus_vendor_admins.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('cus_vendor_admins.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('cus_vendor_admins.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}elseif(!empty($searchData['membership_date'])){
				$membershipDate = $searchData['membership_date'];
				$DB->where('cus_vendor_admins.membership_date','=' ,[$membershipDate." 00:00:00"]);	
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$DB->where("customers.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_code"){
						$DB->where("cus_vendor_admins.vendor_code",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "corporate_name"){
						$DB->where("customers.corporate_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "current_vendaor"){
						$DB->where("vendors.corporate_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("cus_vendor_admins.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		

		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("cus_vendor_admins.is_deleted", 0);
		}else{
			$DB->where("cus_vendor_admins.is_deleted", 0)->where('cus_vendor_admins.customer_id',$customerId)->where('customers.group_id', Session::get('group'));
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'cus_vendor_admins.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedCustomerRecords = $DB->orderBy($sortBy,$order)->get();

		if(Session::has('customers_export_all_data')) {
			Session::forget('customers_export_all_data');
		}

		Session::put('customers_export_all_data', $exportedCustomerRecords);
		
		//$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($items);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();

		//echo "<pre>";print_r($results);die;
		// echo "<pre>";
		// print_r($results);die;
		return  View::make("admin.$this->model.index",compact('groups','results','searchVariable','sortBy','order','query_string', 'customerId'));
	}// end index()


	public function exportAllDataToExcel($customerId) {
		return Excel::download(new CustomersContactExport($customerId), 'customer-information-'.time().'.xlsx');
	}
	
	/**
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add($customerId){
		if(!$customerId){
			Session::flash('error', trans("Something went wrong.")); 
			return Redirect::back()->withInput();
		}

		$customers  = Customer::leftjoin('customer_address', 'customer_address.customer_id', 'customers.id' )->where('customers.id', $customerId)->where('customers.group_id', Session::get('group'))->where("customers.deleted_at", NULL)->first();
		
		$paddrss1 			= !empty($customers->physical_address_line_1) ? $customers->physical_address_line_1:'';
		$paddrss2 			= !empty($customers->physical_address_line_2) ? $customers->physical_address_line_2:'';
		$pcity 		= !empty($customers->physical_address_city) ? $customers->physical_address_city:'';
		$pstate 		= !empty($customers->physical_address_state_code) ? $customers->physical_address_state_code:'';
		$pzipcode 	= !empty($customers->physical_address_postal_code) ? $customers->physical_address_postal_code:'';

		$maddrss1 			= !empty($customers->physical_address_line_1) ? $customers->physical_address_line_1:'';
		$maddrss2 			= !empty($customers->physical_address_line_2) ? $customers->physical_address_line_2:'';
		$mcity 		= !empty($customers->physical_address_city) ? $customers->physical_address_city:'';
		$mstate 		= !empty($customers->physical_address_state_code) ? $customers->physical_address_state_code:'';
		$mzipcode 	= !empty($customers->physical_address_postal_code) ? $customers->physical_address_postal_code:'';
		
		$physicalAddress 	= $paddrss1.' '.$paddrss2.' '.$pcity.' '.$pstate.' '.$pzipcode;

	    $mailingAddress 	= $maddrss1.' '.$maddrss2.' '.$mcity.' '.$mstate.' '.$mzipcode;

		$currentVendor =  VendorAdmin::leftjoin('vendors', 'vendors.id', '=', 'cus_vendor_admins.vendor_id')->select('cus_vendor_admins.vendor_code', 'vendors.business_name')->where('cus_vendor_admins.customer_id', $customerId)->where('vendors.group_id', Session::get('group'))->where('is_deleted', 0)->get();
		$vendors = Vendor::orderBy('business_name', 'asc')->where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		$status = config()->get('status');
		return  View::make("admin.$this->model.add",compact('customerId', 'vendors', 'status', 'customers', 'physicalAddress', 'mailingAddress', 'currentVendor'));
	}// end add()
	
/**
* Function for save new Vendor Admin
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){

		$customerId = $request->input('customer_id'); 
		$vendorId =  $request->input('vendor');
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		Validator::extend('invalid_vendor', function($attribute, $value, $parameters, $validator) {
			$vendorId = $parameters[0];
			$vendor = VendorAdmin::where('vendor_code', $value)->where('vendor_id', $vendorId)->where('is_deleted', 0)->first();
			if(!empty($vendor)){
				return false;
			}else{
				return true;
			}
		});

		Validator::extend('invalid_customer', function($attribute, $value, $parameters, $validator) {
			$cstomerId = $parameters[0];
			$vendor = VendorAdmin::where('vendor_code', $value)->where('customer_id', $cstomerId)->where('is_deleted', 0)->first();
			if(!empty($vendor)){
				return false;
			}else{
				return true;
			}
		});
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'			 => 'required',
					'customer_vendor_id' => 'required|invalid_vendor:vendor|invalid_customer:'.$customerId,
				),
				array(
					"vendor.required"  						=>	trans("The vendor field is required."),
					"customer_vendor_id.required"  			=>	trans("The vendor field is required."),
					"customer_vendor_id.invalid_customer"  	=>	trans("The Vendor's Customer ID is a duplicate."),
					"customer_vendor_id.invalid_vendor"  	=>	trans("The Vendor's Customer ID is a duplicate."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$checkReord =  VendorAdmin::where('customer_id', $customerId)->where('vendor_id', $vendorId)->first();
				if(!empty($checkReord )){
				  $obj = VendorAdmin::where('customer_id', $customerId)->where('vendor_id', $vendorId)->update(['vendor_code' => $request->input('customer_vendor_id')]);
				  $userId						=	$checkReord->id;
				}else{
					$obj 						=  new VendorAdmin;
					$obj->vendor_id 			=  $request->input('vendor');
					$obj->customer_id 			=  $request->input('customer_id');
					$obj->vendor_code 			=  $request->input('customer_vendor_id');
					$obj->save();
					$userId						=	$obj->id;
				}
				

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				// if(!empty($userId)){
				// 	Customer::where('id', $obj->customer_id)->update(['customer_code' => $request->customer_vendor_id]);
				// }

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index", $customerId);
			}
		}
	}//end save()
	
	/**
	* Function for update status
	*
	* @param $modelId as id of Vendor Admin 
	* @param $status as status of Vendor Admin 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0,$customerId){ 
		
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		VendorAdmin::where(['id' => $modelId])->update(['is_active' => $status]);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit Vendor Admin
	*
	* @param $modelId id  of Vendor Admin
	*
	* @return view page. 
	*/
	public function edit($modelId = 0, $customerId){
		$model					=	VendorAdmin::findorFail($modelId);

		if(empty($model)) {
			return Redirect::back();
		}
		$customers  = Customer::leftjoin('customer_address', 'customer_address.customer_id', 'customers.id' )->where('customers.id', $customerId)->where('customers.group_id', Session::get('group'))->where("customers.deleted_at", NULL)->first();

		$paddrss1 			= !empty($customers->physical_address_line_1) ? $customers->physical_address_line_1:'';
		$paddrss2 			= !empty($customers->physical_address_line_2) ? $customers->physical_address_line_2:'';
		$pcity 		= !empty($customers->physical_address_city) ? $customers->physical_address_city:'';
		$pstate 		= !empty($customers->physical_address_state_code) ? $customers->physical_address_state_code:'';
		$pzipcode 	= !empty($customers->physical_address_postal_code) ? $customers->physical_address_postal_code:'';

		$maddrss1 			= !empty($customers->physical_address_line_1) ? $customers->physical_address_line_1:'';
		$maddrss2 			= !empty($customers->physical_address_line_2) ? $customers->physical_address_line_2:'';
		$mcity 		= !empty($customers->physical_address_city) ? $customers->physical_address_city:'';
		$mstate 		= !empty($customers->physical_address_state_code) ? $customers->physical_address_state_code:'';
		$mzipcode 	= !empty($customers->physical_address_postal_code) ? $customers->physical_address_postal_code:'';
		
		$physicalAddress 	= $paddrss1.' '.$paddrss2.' '.$pcity.' '.$pstate.' '.$pzipcode;

	    $mailingAddress 	= $maddrss1.' '.$maddrss2.' '.$mcity.' '.$mstate.' '.$mzipcode;

		$currentVendor =  VendorAdmin::leftjoin('vendors', 'vendors.id', '=', 'cus_vendor_admins.vendor_id')->select('cus_vendor_admins.vendor_code', 'vendors.business_name')->where('cus_vendor_admins.customer_id', $customerId)->where('is_deleted', 0)->get();
	
		$vendors = Vendor::orderBy('business_name', 'asc')->where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
		//$customersAddress = CustomerAddress::where('customer_id', $customerId)->first();
		$status = config()->get('status');
		return  View::make("admin.$this->model.edit",compact('model','status','customers', 'vendors', 'customerId', 'currentVendor', 'physicalAddress', 'mailingAddress'));
	} // end edit()
	
	
	/**
	* Function for update Vendor Admin 
	*
	* @param $modelId as id of Vendor Admin 
	*
	* @return redirect page. 
	*/
	function update($modelId,$customerId,Request $request){

		$model		= VendorAdmin::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'			=> 'required',
					'customer_vendor_id' => 'required|invalid_vendor:vendor|invalid_customer:'.$customerId,
				),
				array(
					"vendor.required"  				=>	trans("The vendor field is required."),
					"customer_vendor_id.required"   =>	trans("The vendor field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 						=  $model;
				$obj->vendor_id 			=  $request->input('vendor');
				$obj->customer_id 			=  $customerId;
				$obj->is_active 			=  $request->input('is_active');
				$obj->save();
				
				$userId					=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index", $customerId);
			}
		}
	}// end update()
	 
	/**
	* Function for delete Vendor Admim  status
	*
	* @param $id as id of Vendor Admim 
	*
	* @return redirect page. 
	*/	
	public function delete($id = 0, $customerId=0){
		$userDetails	=	VendorAdmin::find($id); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($id){		
			VendorAdmin::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()


	/*Funtion get customer code by id*/
	public function customerCode($customerId, $vendorId)
	{
		$vendor_code = '';
		$cutomer = VendorAdmin::where('customer_id', $customerId)->where('vendor_id', $vendorId)->where('is_deleted', 0)->first();
		if(!empty($cutomer)){
			$vendor_code = $cutomer->vendor_code;
		}
		return response()->json(['success' => true, 'customer_code' => $vendor_code]);
	}//end customer code
	
}// end VendorAdminController
