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
use App\Model\CustomerContact;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Exports\CustomersContactExport;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;
use App\Model\Group;

/**
* CustomersContactController Controller
*
* Add your methods in the class below
*
*/
class CustomersContactController extends BaseController {

	public $model		=	'UsersContact';
	public $sectionName	=	"Customer's Contact Admin";
	public $sectionNameSingular	=	"Customer's Contact Admin";
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all customer Contacts
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index($customerId, Request $request){
		
		$checCustomer = Customer::where('id', $customerId)->first();
		if($checCustomer->group_id != Session::get('group')){
			return Redirect::route("Users.index");
		}
		$items = $request->per_page ?? DEFAULT_PAGE_LIMIT;
		$group = Session::get('group');
		$DB					=	CustomerContact::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
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
				$DB->whereBetween('customer_contacts.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('customer_contacts.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('customer_contacts.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("customer_contacts.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "name"){
						$searchExportItem['name'] = $fieldValue;
						$DB->where("first_name",'LIKE', '%'.$fieldValue .'%')->orWhereRaw("concat(first_name, ' ', last_name) like '%" . $fieldValue . "%' ");
					}
					if($fieldName == "email"){
						$searchExportItem['email'] = $fieldValue;
						$DB->where("customer_contacts.email",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "title"){
						$searchExportItem['title'] = $fieldValue;
						$DB->where("customer_contacts.title",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "ownership_year"){
						$searchExportItem['ownership_year'] = $fieldValue;
						$DB->where("customer_contacts.ownership_year",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$searchExportItem['is_active'] = $fieldValue;
						$DB->where("customer_contacts.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("customer_contacts.is_deleted", 0);
		}else{
			$DB->where("customer_contacts.is_deleted", 0)->where('customer_id',$customerId)->where('group_id', $group);
		}
		

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'customer_contacts.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedCustomerRecords = $DB->orderBy($sortBy,$order)->get();

		if(Session::has('customers_export_all_data')) {
			Session::forget('customers_export_all_data');
		}

		Session::put('customers_export_all_data', $exportedCustomerRecords);
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($items);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();

		/*echo "<pre>";
		print_r($results);die;
		*/
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

		$checCustomer = Customer::where('id', $customerId)->first();
		
		if($checCustomer->group_id != Session::get('group')){
			return Redirect::route("Users.index");
		}
		$existingCustomers = Customer::pluck('business_name', 'id')->toArray();
		$status = config()->get('status');
		$membershipTypes = config()->get('customer_membership_type');
		$beverageTypes = config()->get('beverage_type');
		$customerTypes = config()->get('customer_types');
		$countries = AdminCountry::pluck('name', 'id')->toArray();
		$states	=	config()->get('states');

		$contacts = CustomerContact::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
            ->where('is_deleted', 0)
            ->where('customer_id', $customerId)
            ->where('is_active', 1)
            ->pluck('name', 'id')->toArray();

		return  View::make("admin.$this->model.add",compact('existingCustomers', 'status', 'membershipTypes', 'beverageTypes', 'customerTypes', 'countries','states', 'customerId', 'contacts'));
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

		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'last_name'			=> 'required',
					'email'				=> 'email|unique:customer_contacts',
				),
				array(
					"last_name.required"		=>	trans("The last name field is required."),
					"email.email"				=>	trans("The email must be a valid email address."),
					"email.unique"				=>	trans("The email has already been taken."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 							=  new CustomerContact;
				$obj->customer_id 				=  $request->input('customer_id');
				$obj->first_name 				=  $request->input('first_name');
				$obj->last_name 				=  $request->input('last_name');
				$obj->business_phone 			=  $request->input('business_phone');
				$obj->title 					=  $request->input('title');
				$obj->cell_phone 				=  $request->input('cell_phone');
				$obj->is_active 				=  $request->input('is_active');
				$obj->ownership_percent 		=  $request->input('ownership_percent');
				$obj->fax 						=  $request->input('fax');
				$obj->ownership_year 			=  $request->input('ownership_year');
				$obj->email 					=  $request->input('email');
				$obj->email_pdf_statement 		=  !empty($request->input('email_pdf_statement')) ? $request->input('email_pdf_statement'):0;
				$obj->contact_id 				=  $request->input('contact_id');
				$obj->allow_online_statement	=  !empty($request->input('allow_online_statement')) ? $request->input('allow_online_statement'):0;
				$obj->active_customer_email_pdf =  $request->input('active_customer_email_pdf');
				$obj->admin_id									=  Auth::guard('admin')->user()->id;
				$obj->group_id									=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->save();
				
				$userId						=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index", $obj->customer_id);
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
	public function changeStatus($modelId = 0, $status = 0,$customerId){ 
		

		// echo "<pre>";print_r($data);die;
		//$data = CustomerContact::where(['id' => $modelId, 'customer_id' => $customerId])->update(['is_active' => 1]);
		//echo "<pre>";print_r($data);die;
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$data = CustomerContact::where(['id' => $modelId])->first();
		$data->is_active = $status;
		$data->save();
		
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
	public function edit($modelId = 0, $customerId){
		
		$model					=	CustomerContact::findorFail($modelId);

		if(empty($model)) {
			return Redirect::back();
		}
		$checCustomer = '';
		if(auth()->guard('admin')->user()->user_role == "user_admin"){
			$checCustomer = Customer::where('id', $customerId)->where('admin_id', auth()->guard('admin')->user()->id)->first();
		}
		else{
			$checCustomer = Customer::where('id', $customerId)->first();
		}

		if($checCustomer->group_id != Session::get('group')){
			return Redirect::route("Users.index");
		}
		$status = config()->get('status');
		$states	=	config()->get('states');
		$internal_notes =	InternalNotes::where('contact_customer_id', $modelId)->get();
		$general_notes 	=	GeneralNotes::where('contact_customer_id', $modelId)->get();
		
		$contacts = CustomerContact::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
            ->where('is_deleted', 0)
            ->where('customer_id', $customerId)
            ->where('is_active', 1)
            ->pluck('name', 'id')->toArray();

		
		return  View::make("admin.$this->model.edit",compact('model','status', 'internal_notes', 'general_notes', 'customerId', 'contacts'));
	} // end edit()
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model = '';
		if(auth()->guard('admin')->user()->user_role == "user_admin"){
		$model					=	CustomerContact::where('id',$modelId)->where('customer_id', $request->customer_id)->first();
		}else{
			$model					=	CustomerContact::where('id',$modelId)->first();

		}
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'last_name'			=> 'required',
					'email'				=> 'unique:customer_contacts,email,'.$modelId,
				),
				array(
					"last_name.required"		=>	trans("The last name field is required."),
					"email.email"				=>	trans("The email must be a valid email address."),
					"email.unique"				=>	trans("The email has already been taken."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 						=  $model;
				$obj->customer_id 			=  $request->input('customer_id');
				$obj->first_name 			=  $request->input('first_name');
				$obj->last_name 			=  $request->input('last_name');
				$obj->business_phone 		=  $request->input('business_phone');
				$obj->title 				=  $request->input('title');
				$obj->cell_phone 			=  $request->input('cell_phone');
				$obj->is_active 			=  $request->input('is_active');
				$obj->ownership_percent 	=  $request->input('ownership_percent');
				$obj->fax 					=  $request->input('fax');
				$obj->ownership_year 		=  $request->input('ownership_year');
				$obj->email 				=  $request->input('email');
				$obj->email_pdf_statement 		=  !empty($request->input('email_pdf_statement')) ? $request->input('email_pdf_statement'):0;
				$obj->contact_id 				=  $request->input('contact_id');
				$obj->allow_online_statement	=  !empty($request->input('allow_online_statement')) ? $request->input('allow_online_statement'):0;
				$obj->active_customer_email_pdf =  $request->input('active_customer_email_pdf');
				$obj->save();
				
				$userId					=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(!empty($userId)){	
					if(isset($formData['internal_notes']) && !empty($formData['internal_notes'])){
						$deletenotes = InternalNotes::where('contact_customer_id',$modelId)->delete();
						foreach ($formData['internal_notes'] as $data){
							$modelO							=  new InternalNotes();
							$modelO->contact_customer_id	=	$userId;
							$modelO->internal_note			=	$data['name'];	
							$modelO->save();
						}
					}
					if(isset($formData['general_note']) && !empty($formData['general_note'])){
						GeneralNotes::where('contact_customer_id',$modelId)->delete();
						foreach ($formData['general_note'] as $data){
							$modelO								=  new GeneralNotes();
							$modelO->contact_customer_id		=	$userId;
							$modelO->general_note				=	$data['name'];	
							$modelO->save();
						}
					}

					Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::route($this->model.".index", $obj->customer_id);

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
	public function delete($id = 0, $customerId=0){
		$userDetails	=	CustomerContact::find($id); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($id){
			if(auth()->guard('admin')->user()->user_role == "super_admin"){
				CustomerContact::where('id',$id)->where('customer_id', $customerId)->update(array('is_deleted'=>1));
			}else{
				CustomerContact::where('id',$id)->where('customer_id', $customerId)->where('admin_id', auth()->guard('admin')->user()->id)->update(array('is_deleted'=>1));

			}		
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0, $customerId){
		$group = Session::get('group');
		$checCustomer = '';
		if(auth()->guard('admin')->user()->user_role == "user_admin"){
			$checCustomer = Customer::where('id', $customerId)->where('admin_id', auth()->guard('admin')->user()->id)->first();
		
		}else{
			$checCustomer = Customer::where('id', $customerId)->first();

		}	
		if($checCustomer->group_id != Session::get('group')){
			return Redirect::route("Users.index");
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin"){
			$model	=	CustomerContact::where('id',"$modelId")->select('customer_contacts.*')->first();		
		}else{
			$model	=	CustomerContact::where('id',"$modelId")->select('customer_contacts.*')->where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', $group)->first();	
		}
		if(empty($model)) {
			return Redirect::route($this->model.".index", $customerId);
		}

		$internalnotes = InternalNotes::select('internal_note')->where('contact_customer_id', $model->id)->get();
		$generalnotes  = GeneralNotes::select('general_note')->where('contact_customer_id', $model->id)->get();
		$parentContact = CustomerContact::select('first_name', 'last_name')->where('id', $model->contact_id)->first();

		/*echo "<pre>";
		print_r($parentContact);die;*/
		
		return  View::make("admin.$this->model.view",compact('model', 'customerId', 'parentContact', 'internalnotes', 'generalnotes'));
	} // end view()


	//get internal notes by id
	public function addMoreInternal(){
		$id 	= $_POST['id'];
		$output = 0;
		if(!empty($id)){	
			return  View::make("admin.$this->model.addMoreInternal", compact('id'));
		}
	}

	//get general notes by id
	public function addMoreGeneral(){
		$id 	= $_POST['id'];
		$output = 0;
		if(!empty($id)){	
			return  View::make("admin.$this->model.addMoreGeneral", compact('id'));
		}
	}
	
}// end CustomersContactController
