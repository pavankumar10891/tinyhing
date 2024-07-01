<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Customer;
use App\Model\CustomerAddress;
use App\Model\EmailTemplate;
use App\Model\AdminCountry;
use App\Model\State;
use App\Model\EmailAction;
use App\Model\InternalNotes;
use App\Model\GeneralNotes;
use App\Model\Group;
use App\Exports\CustomersExport;
use App\Exports\CustomersByIdExport;
use App\Imports\CustomerImport;
use App\Imports\CustomerVendorImport;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* CustomersController Controller
*
* Add your methods in the class below
*
*/
class CustomersController extends BaseController {

	public $model		=	'Users';
	public $sectionName	=	'Customer Admin';
	public $sectionNameSingular	=	'Customer Admin';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Users 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){ 
		$this->generateCustomerCode();
		$items = $request->per_page ?? DEFAULT_PAGE_LIMIT;
		$group = Session::get('group');
		$adminId = auth()->guard('admin')->user()->id;
		$groupId = auth()->guard('admin')->user()->login_user_gouup_id;
		$DB					=	Customer::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('customer_address', 'customers.id', '=', 'customer_address.customer_id');
		$DB->select('customers.*','customer_address.physical_address_line_1', 'customer_address.physical_address_line_2', 'customer_address.physical_address_city', 'customer_address.physical_address_state_code', 'customer_address.physical_address_postal_code', 'customer_address.physical_address_country');

		//export item by seach name
		$searchExportItem = array();
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
				$searchExportItem['date_from'] = $dateS;
				$searchExportItem['date_to'] = $dateE;
				$DB->whereBetween('customers.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('customers.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('customers.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}elseif(!empty($searchData['membership_date'])){
				$membershipDate = $searchData['membership_date'];
				$searchExportItem['membership_date'] = $membershipDate;
				$DB->where('customers.membership_date','=' ,[$membershipDate." 00:00:00"]);	
			}elseif(!empty($searchData['subcription_startdate_from'])){
				$membershipDate = $searchData['subcription_startdate_from'];
				$searchExportItem['subcription_startdate_from'] = $membershipDate;
				$DB->where('customers.subcription_start_date','=' ,[$membershipDate." 00:00:00"]);	
			}elseif(!empty($searchData['subcription_enddate_to'])){
				$dateE = $searchData['subcription_enddate_to'];
				$searchExportItem['subcription_enddate_to'] = $dateE;
				$DB->where('customers.subcription_end_date','<=' ,[$dateE." 00:00:00"]); 						
			}
			
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("customers.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "dormant_status"){
						$searchExportItem['dormant_status'] = $fieldValue;
						$DB->where("customers.dormant_status",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "name"){
						$searchExportItem['business_name'] = $fieldValue;
						$DB->where("customers.business_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "address_line_1"){
						$searchExportItem['physical_address_line_1'] = $fieldValue;
						$DB->where("customer_address.physical_address_line_1",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "address_line_2"){
						$searchExportItem['physical_address_line_2'] = $fieldValue;
						$DB->where("customer_address.physical_address_line_2",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "city"){
						$searchExportItem['physical_address_city'] = $fieldValue;
						$DB->where("customer_address.physical_address_city",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "state"){
						$searchExportItem['physical_address_state_code'] = $fieldValue;
						$DB->where("customer_address.physical_address_state_code",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "zip"){
						$searchExportItem['physical_address_postal_code'] = $fieldValue;
						$DB->where("customer_address.physical_address_postal_code",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "corporate_name"){
						$searchExportItem['corporate_name'] = $fieldValue;
						$DB->where("customers.corporate_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$searchExportItem['is_active'] = $fieldValue;
						$DB->where("customers.is_active",$fieldValue);
					}
				}

				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			if(!empty($group)){
				$DB->where("customers.deleted_at", NULL)->where('customers.group_id', $group);
			}else{
				$DB->where("customers.deleted_at", NULL);
			}
			
		}else{
			$DB->where("customers.deleted_at", NULL)->where('customers.group_id', $group);
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'customers.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedCustomerRecords = $DB->groupBy('customer_id')->orderBy($sortBy,$order)->get();

		if(Session::has('customers_export_all_data')) {
			Session::forget('customers_export_all_data');
		}

		Session::put('customers_export_all_data', $searchExportItem);
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($items);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$states	=	config()->get('states');
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		
		return  View::make("admin.$this->model.index",compact('groups','results','searchVariable','sortBy','order','query_string', 'states'));
	}// end index()

	// all customer exports
	public function exportAllDataToExcel() {
		return Excel::download(new CustomersExport(), 'customer-information-'.time().'.xlsx');
	}

	// customers export by ids
	public function exportCustomers(Request $request){
		return Excel::download(new CustomersByIdExport($request->uids), 'customer-information-'.time().'.xlsx');
	}

	
	
	/**
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		$existingCustomers = Customer::orderBy('business_name', 'asc')->pluck('business_name', 'id')->toArray();
		$status = config()->get('status');
		$membershipTypes = config()->get('customer_membership_type');
		$beverageTypes = config()->get('beverage_type');
		$customerTypes = config()->get('customer_types');
		//$countries = AdminCountry::pluck('name', 'id')->toArray();
		$states	=	config()->get('states');
		return  View::make("admin.$this->model.add",compact('existingCustomers', 'status', 'membershipTypes', 'beverageTypes', 'customerTypes','states'));
	}// end add()
	
/**
* Function for save new customer
*
* @param null
*
* @return redirect page. 
*/
	function save(Request $request){
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		$regesx = '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$';
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'business_name'		=> 'required',
					'corporate_name'	=> 'required',
					'membership_date'	=> 'required',
					'physical_address_line_1'	=> 'required',
					'physical_address_city'	=> 'required',
					'physical_address_postal_code'	=> 'required',
					'monthly_food_amount'	=> 'numeric',
					'website'	=> ['regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
					
				),
				array(
					"business_name.required"		=>	trans("The business name field is required."),
					"corporate_name.required"		=>	trans("The corporate name field is required."),
					"membership_date.required"		=>	trans("The membership date field is required."),
					"physical_address_line_1.required"		=>	trans("The address line 1 field is required."),
					"physical_address_city.required"		=>	trans("The city field is required."),
					"physical_address_postal_code.required"		=>	trans("The zip code field is required."),
					"monthly_food_amount.numeric"		=>	trans("Monthly Food purchases amount numeric only"),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$customercode 									= $this->fun_generate_customer_code($request->input('business_name'));
				$obj 											=  new Customer;
				$obj->business_name 							=  $request->input('business_name');
				$obj->corporate_name 							=  $request->input('corporate_name');
				$obj->membership_date 							=  $request->input('membership_date');
				$obj->parent_customer_id 						=  $request->input('parent_customer_id');
				$obj->check_description 						=  $request->input('check_description');
				$obj->is_active 								=  $request->input('is_active');
				$obj->restaurant_type 							=  $request->input('restaurant_type');
				$obj->membership_type 							=  $request->input('membership_type');
				$obj->beverage_program 							=  $request->input('beverage_program');
				$obj->website 									=  $request->input('website');
				$obj->type 										=  $request->input('type');
				$obj->federal_tax_identification_number 						=  $request->input('federal_tax_identification_number');
				$obj->lead_source 								=  $request->input('lead_source');
				$obj->dormant_status 							=  (! empty($request->input('dormant_status'))) ? $request->input('dormant_status') : 0;
				$obj->show_dormat_acc 							=  (! empty($request->input('show_dormat_acc'))) ? $request->input('show_dormat_acc') : 0;
				$obj->paid_status 								=  (! empty($request->input('paid_status'))) ? $request->input('paid_status') : 0;
				$obj->exclude_vendor_emails_status 				=  (! empty($request->input('exclude_vendor_emails_status'))) ? $request->input('exclude_vendor_emails_status') : 0;
				$obj->distributor_information 					=  $request->input('distributor_information');
				$obj->brand_information 						=  $request->input('brand_information');
				$obj->customer_code								=  $customercode;
				$obj->admin_id									=  Auth::guard('admin')->user()->id;
				$obj->group_id									=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->subcription_start_date									=  !empty($request->input('subscription_start_date')) ? $request->input('subscription_start_date'):'';
				$obj->subcription_end_date									=  !empty($request->input('subscription_end_date')) ? $request->input('subscription_end_date'):'';
				$obj->monthly_food_amount									=  !empty($request->input('monthly_food_amount')) ? $request->input('monthly_food_amount'):0;

				$obj->save();
				
				$userId					=	$obj->id;

				$customerAddressObj									=  new CustomerAddress;
				$customerAddressObj->customer_id 						=  $userId;
				$customerAddressObj->physical_address_line_1 						=  $request->input('physical_address_line_1');
				$customerAddressObj->physical_address_line_2 						=  $request->input('physical_address_line_2');
				$customerAddressObj->physical_address_city 						=  $request->input('physical_address_city');
				$customerAddressObj->physical_address_state_code 						=  $request->input('physical_address_state_code');
				$customerAddressObj->physical_address_postal_code 						=  $request->input('physical_address_postal_code');
				$customerAddressObj->physical_address_country 						=  $request->input('physical_address_country');
				$customerAddressObj->mailing_address_line_1 						=  $request->input('mailing_address_line_1');
				$customerAddressObj->mailing_address_line_2 						=  $request->input('mailing_address_line_2');
				$customerAddressObj->mailing_address_city 						=  $request->input('mailing_address_city');
				$customerAddressObj->mailing_address_state_code 						=  $request->input('mailing_address_state_code');
				$customerAddressObj->mailing_address_postal_code 						=  $request->input('mailing_address_postal_code');
				$customerAddressObj->mailing_address_country 						=  $request->input('mailing_address_country');
				
				$customerAddressObj->save();

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(isset($formData['ingradiant']) && !empty($formData['ingradiant'])){
					foreach ($formData['ingradiant'] as $data){
						$modelO					=  new MealIngredient();
						$modelO->meal_plan_id		=	$obj->id;
						$modelO->name				=	$data['name'];	
						$modelO->save();
					}
				}

				if(isset($formData['direction']) && !empty($formData['direction'])){
					foreach ($formData['direction'] as $data){
						$modelO						=  new MealDirection();
						$modelO->meal_plan_id		=	$obj->id;
						$modelO->name				=	$data['name'];	
						$modelO->save();
					}
				}	

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	/**
	* Function for update status
	*
	* @param $modelId as id of customer 
	* @param $status as status of customer 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('customers',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
			if(auth()->guard('admin')->user()->user_role == "user_admin"){
				$model					=	Customer::where('admin_id', auth()->guard('admin')->user()->id)->where('id',$modelId)->first();
			}else{
				$model					=	Customer::where('id',$modelId)->first();
			}
			
		if(empty($model)){
			return Redirect::back();
		}
		if($model->group_id != Session::get('group')){
			return Redirect::route($this->model.".index");
		}

		$custAddressDetail	= CustomerAddress::where('customer_id', $modelId)->first();
		$existingCustomers 	= Customer::orderBy('business_name', 'asc')->where('id', '!=', $modelId)->pluck('business_name', 'id')->toArray();
		$status 			= config()->get('status');
		$membershipTypes 	= config()->get('customer_membership_type');
		$beverageTypes 		= config()->get('beverage_type');
		$customerTypes 		= config()->get('customer_types');
		$states				= config()->get('states');
		$internal_notes 	= InternalNotes::where('customer_id', $modelId)->get();
		$general_notes 		= GeneralNotes::where('customer_id', $modelId)->get();

		
		
		return  View::make("admin.$this->model.edit",compact('model','existingCustomers','status', 'membershipTypes', 'beverageTypes', 'customerTypes', 'states', 'custAddressDetail', 'internal_notes', 'general_notes'));
	} // end edit()


	public function import(){
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		return  View::make("admin.$this->model.import");
	}

	public function importSaveCSV(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'file'		=> 'required',
					
				),
				array(
					"file.required"		=>	trans("The file is required."),
				)
			);
			if($request->has('file')){
				if(($request->file('file')->getClientOriginalExtension() != 'csv')){
					$errors 				=	$validator->messages();
					$errors->add('file', trans("The file must be only csv"));
					return Redirect::back()->withErrors($errors)->withInput();
				}
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('file')->getRealPath();
				$path1 = $request->file('file')->store('temp'); 
				$path=storage_path('app').'/'.$path1;  
				$import = Excel::import(new CustomerImport,$path);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}

	public function importSaveExcel(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'exel_file'		=> 'required',	
				),
				array(
					"exel_file.required"		=>	trans("The file is required."),
				)
			);
			if($request->has('file')){
				if(($request->file('exel_file')->getClientOriginalExtension() != 'xlsx')){
					$errors 				=	$validator->messages();
					$errors->add('exel_file', trans("The file must be only excel"));
					return Redirect::back()->withErrors($errors)->withInput();
				}
			}

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('exel_file')->getRealPath();
				$path1 = $request->file('exel_file')->store('temp'); 
				$path=storage_path('app').'/'.$path1; 
				$import = Excel::import(new CustomerImport,$path);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Customer::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'business_name'		=> 'required',
					'corporate_name'	=> 'required',
					'membership_date'	=> 'required',
					'physical_address_line_1'	=> 'required',
					'physical_address_city'	=> 'required',
					'physical_address_postal_code'	=> 'required',
					'website'	=> ['regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
					'monthly_food_amount'	=> 'numeric|min:0',
				),
				array(
					"business_name.required"		=>	trans("The business name field is required."),
					"corporate_name.required"		=>	trans("The corporate name field is required."),
					"membership_date.required"		=>	trans("The membership date field is required."),
					"physical_address_line_1.required"		=>	trans("The address line 1 field is required."),
					"physical_address_city.required"		=>	trans("The city field is required."),
					"physical_address_postal_code.required"		=>	trans("The zip code field is required."),
					"monthly_food_amount.numeric"		=>	trans("Monthly Food purchases amount numeric only"),
					"monthly_food_amount.numeric"		=>	trans("Monthly Food purchases amount numeric only"),
					"monthly_food_amount.min"		    =>	trans("Monthly Food purchases amount minimum 0"),
					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  $model;
				$obj->business_name 						=  $request->input('business_name');
				$obj->corporate_name 						=  $request->input('corporate_name');
				$obj->membership_date 							=  $request->input('membership_date');
				$obj->parent_customer_id 						=  $request->input('parent_customer_id');
				$obj->check_description 						=  $request->input('check_description');
				$obj->is_active 						=  $request->input('is_active');
				$obj->restaurant_type 						=  $request->input('restaurant_type');
				$obj->membership_type 						=  $request->input('membership_type');
				$obj->beverage_program 						=  $request->input('beverage_program');
				$obj->website 						=  $request->input('website');
				$obj->type 						=  $request->input('type');
				$obj->federal_tax_identification_number 						=  $request->input('federal_tax_identification_number');
				$obj->lead_source 						=  $request->input('lead_source');
				$obj->dormant_status 						=  (! empty($request->input('dormant_status'))) ? $request->input('dormant_status') : 0;
				$obj->show_dormat_acc 						=  (! empty($request->input('show_dormat_acc'))) ? $request->input('show_dormat_acc') : 0;
				$obj->paid_status 						=  (! empty($request->input('paid_status'))) ? $request->input('paid_status') : 0;
				$obj->exclude_vendor_emails_status 						=  (! empty($request->input('exclude_vendor_emails_status'))) ? $request->input('exclude_vendor_emails_status') : 0;
				$obj->distributor_information 						=  $request->input('distributor_information');
				$obj->brand_information 						=  $request->input('brand_information');
				$obj->admin_id									=  Auth::guard('admin')->user()->id;
				$obj->group_id									=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->subcription_start_date									=  !empty($request->input('subscription_start_date')) ? $request->input('subscription_start_date'):'';
				$obj->subcription_end_date									=  !empty($request->input('subscription_end_date')) ? $request->input('subscription_end_date'):'';
				$obj->monthly_food_amount									=  !empty($request->input('monthly_food_amount')) ? $request->input('monthly_food_amount'):0;
			
				$obj->save();
				
				$userId					=	$obj->id;

				$customerAddressModel					=	CustomerAddress::where('customer_id', $modelId)->first();
				if(!empty($customerAddressModel)){
					$customerAddressObj									=  $customerAddressModel;
				}else{
					$customerAddressObj									=  new CustomerAddress();
				}

				$customerAddressObj									=  $customerAddressModel;
				$customerAddressObj->customer_id 						=  $userId;
				$customerAddressObj->physical_address_line_1 						=  $request->input('physical_address_line_1');
				$customerAddressObj->physical_address_line_2 						=  $request->input('physical_address_line_2');
				$customerAddressObj->physical_address_city 						=  $request->input('physical_address_city');
				$customerAddressObj->physical_address_state_code 						=  $request->input('physical_address_state_code');
				$customerAddressObj->physical_address_postal_code 						=  $request->input('physical_address_postal_code');
				$customerAddressObj->physical_address_country 						=  $request->input('physical_address_country');
				$customerAddressObj->mailing_address_line_1 						=  $request->input('mailing_address_line_1');
				$customerAddressObj->mailing_address_line_2 						=  $request->input('mailing_address_line_2');
				$customerAddressObj->mailing_address_city 						=  $request->input('mailing_address_city');
				$customerAddressObj->mailing_address_state_code 						=  $request->input('mailing_address_state_code');
				$customerAddressObj->mailing_address_postal_code 						=  $request->input('mailing_address_postal_code');
				$customerAddressObj->mailing_address_country 						=  $request->input('mailing_address_country');
				
				$customerAddressObj->save();

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(!empty($userId)){
					if(isset($formData['internal_notes']) && !empty($formData['internal_notes'])){
						$deletenotes = InternalNotes::where('customer_id',$userId)->delete();
						foreach ($formData['internal_notes'] as $data){
							$modelO						=  new InternalNotes();
							$modelO->customer_id		=	$userId;
							$modelO->internal_note		=	$data['name'];	
							$modelO->save();
						}
					}

					if(isset($formData['general_note']) && !empty($formData['general_note'])){
						GeneralNotes::where('customer_id',$userId)->delete();
						foreach ($formData['general_note'] as $data){
							$modelO						=  new GeneralNotes();
							$modelO->customer_id		=	$userId;
							$modelO->general_note		=	$data['name'];	
							$modelO->save();
						}
					}

					Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::route($this->model.".index");

				}else{
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				
			}
		}
	}// end update()
	 
	/**
	* Function for update Currency  status
	*
	* @param $modelId as id of Currency 
	* @param $modelStatus as status of Currency 
	*
	* @return redirect page. 
	*/	
	public function delete($userId = 0){
		$userDetails	=	Customer::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$email 			=	'delete_'.$userId.'_'.$userDetails->email;		
			$phone_number 		=	'delete_'.$userId.'_'.$userDetails->phone_number;		
			$social_id 		=	'delete_'.$userId.'_'.$userDetails->social_id;		
			$customer =  Customer::where('admin_id', auth()->guard('admin')->user()->id)->where('id',$userId)->update(array('is_deleted'=>1, 'deleted_at' => date('Y-m-d H:i:s')));
			if($customer == true){
				Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
			}else{
				Session::flash('error',trans($this->sectionNameSingular." something went to wrong")); 
			}
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
	   if(auth()->guard('admin')->user()->user_role == "super_admin") {
		 	$model	=	Customer::where('id',"$modelId")->first();
		}else{
			$model	=	Customer::where('id',"$modelId")->where('group_id',Session::get('group'))->first();
		}
		$custAddressDetails = 	CustomerAddress::where('customer_id', $modelId)
											->first(); 

		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model','custAddressDetails'));
	} // end view()

	public function addMoreInternal(){
		$id 	= $_POST['id'];
		$output = 0;
		if(!empty($id)){	
			return  View::make("admin.$this->model.addMoreInternal", compact('id'));
		}
	}
	public function addMoreGeneral(){
		$id 	= $_POST['id'];
		$output = 0;
		if(!empty($id)){	
			return  View::make("admin.$this->model.addMoreGeneral", compact('id'));
		}
	}
	// import customer vendor page
	public function customerVendorAdminImport(){
		if(Session::get('group') == ''){
			return Redirect::to('/adminpnlx/dashboard');
		}
		
		return  View::make("admin.$this->model.customer_vendor_import");
	}//end function

	// import customer vendor save
	public function customerVendorAdminImportSave(Request $request)
	{
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'file'		=> 'required',
					
				),
				array(
					"file.required"		=>	trans("The file is required."),
				)
			);
			// if($request->has('file')){
			// 	if(($request->file('file')->getClientOriginalExtension() != 'csv')){
			// 		$errors 				=	$validator->messages();
			// 		$errors->add('file', trans("The file must be only csv"));
			// 		return Redirect::back()->withErrors($errors)->withInput();
			// 	}
			// }

			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$path = $request->file('file')->getRealPath();
				$path1 = $request->file('file')->store('temp'); 
				$path=storage_path('app').'/'.$path1;  
				
				$import = Excel::import(new CustomerVendorImport,$path);
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::back();
			  /* echo "<pre>";
			   print_r($data);*/
			}
		}
	}//end function
	
}// end BrandsController
