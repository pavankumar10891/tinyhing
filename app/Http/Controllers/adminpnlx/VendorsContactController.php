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
use App\Model\VendorContact;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Exports\CustomersContactExport;
use App\Model\Group;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* VendorsContactController Controller
*
* Add your methods in the class below
*
*/
class VendorsContactController extends BaseController {

	public $model		=	'VendorContact';
	public $sectionName	=	"Vendor's Contact Admin";
	public $sectionNameSingular	=	"Vendor's Contact Admin";
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Vendor Contacts 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index($vendorId='', Request $request){  

		$DB					=	VendorContact::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
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
				$DB->whereBetween('vendor_contact_admin.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('vendor_contact_admin.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('vendor_contact_admin.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}elseif(!empty($searchData['membership_date'])){
				$membershipDate = $searchData['membership_date'];
				$DB->where('vendor_contact_admin.membership_date','=' ,[$membershipDate." 00:00:00"]);	
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "group"){
						$searchExportItem['group'] = $fieldValue;
						$DB->where("vendor_contact_admin.group_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "name"){
						$DB->where("first_name",'LIKE', '%'.$fieldValue .'%')->orWhereRaw("concat(first_name, ' ', last_name) like '%" . $fieldValue . "%' ");
					}
					if($fieldName == "email"){
						$DB->where("vendor_contact_admin.email",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "title"){
						$DB->where("vendor_contact_admin.title",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "ownership_year"){
						$DB->where("vendor_contact_admin.ownership_year",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("vendor_contact_admin.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		//group
		$group = Session::get('group');
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("vendor_contact_admin.is_deleted", 0)->where('vendor_id',$vendorId);
		}else{
		  $DB->where("vendor_contact_admin.is_deleted", 0)->where('vendor_contact_admin.group_id', $group)->where('vendor_id',$vendorId);
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'vendor_contact_admin.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
		$exportedCustomerRecords = $DB->orderBy($sortBy,$order)->get();

		if(Session::has('Vendors_export_all_data')) {
			Session::forget('custVendorsport_all_data');
		}

		Session::put('customers_export_all_data', $exportedCustomerRecords);
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		
		return  View::make("admin.$this->model.index",compact('groups','results','searchVariable','sortBy','order','query_string', 'vendorId'));
	}// end index()


	public function exportAllDataToExcel($vendorId) {
		return Excel::download(new CustomersContactExport($vendorId), 'customer-information-'.time().'.xlsx');
	}
	
	/**
	* Function for add new Vendor Contact
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add($vendorId){
		if(!$vendorId){
			Session::flash('error', trans("Something went wrong.")); 
			return Redirect::back()->withInput();
		}
		$status = config()->get('status');
		$reporting_freq = config()->get('reporting_freq');

		$contacts = VendorContact::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->pluck('name', 'id')->toArray();
		
		return  View::make("admin.$this->model.add",compact('status','vendorId', 'reporting_freq', 'contacts'));
	}// end add()
	
/**
* Function for save Vendor Contact
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
					'email'				=> 'email|unique:vendor_contact_admin',
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
				$obj 						=  new VendorContact;
				$obj->vendor_id 			=  $request->input('vendor_id');
				$obj->reporting_freq 		=  $request->input('reporting_freq');
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
				$obj->admin_id				=  Auth::guard('admin')->user()->id;
				$obj->group_id				=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->save();
				
				$userId						=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index", $obj->vendor_id);
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
	public function changeStatus($modelId = 0, $status = 0,$vendorId){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		VendorContact::where(['id' => $modelId, 'vendor_id' => $vendorId])->update(['is_active' => $status]);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit Vendor Contact
	*
	* @param $modelId id, $vendorId of Vendor Contact
	*
	* @return view page. 
	*/
	public function edit($modelId = 0, $vendorId){
		$model					=	VendorContact::findorFail($modelId);

		if(empty($model)) {
			return Redirect::back();
		}
		$status = config()->get('status');
		$states	=	config()->get('states');
		$internal_notes =	InternalNotes::where('vendor_contact_id', $modelId)->get();
		$general_notes 	=	GeneralNotes::where('vendor_contact_id', $modelId)->get();
		$reporting_freq = config()->get('reporting_freq');

		$contacts = VendorContact::select(
            DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->pluck('name', 'id')->toArray();
		
		return  View::make("admin.$this->model.edit",compact('model','status', 'internal_notes', 'general_notes', 'vendorId', 'reporting_freq', 'contacts'));
	} // end edit()
	
	
	/**
	* Function for update Vendor Contact 
	*
	* @param $modelId as id of Vendor Contact 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){

		$model 			=	VendorContact::where('id',$modelId)->where('vendor_id', $request->vendor_id)->first();
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
					'email'				=> 'unique:vendor_contact_admin,email,'.$modelId,
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
				$obj->vendor_id 			=  $request->input('vendor_id');
				$obj->contact_person 		=  $request->input('contact_person');
				$obj->reporting_freq 		=  $request->input('reporting_freq');
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
				$obj->save();
				
				$userId					=	$obj->id;

				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				if(!empty($userId)){	
					if(isset($formData['internal_notes']) && !empty($formData['internal_notes'])){
						$deletenotes = InternalNotes::where('vendor_contact_id',$modelId)->delete();
						
							foreach ($formData['internal_notes'] as $data){
								$modelO							=  new InternalNotes();
								$modelO->vendor_contact_id		=	$modelId;
								$modelO->internal_note			=	$data['name'];	
								$modelO->save();
							}
					}

					if(isset($formData['general_note']) && !empty($formData['general_note'])){
						GeneralNotes::where('vendor_contact_id',$modelId)->delete();
						foreach ($formData['general_note'] as $data){
							$modelO								=  new GeneralNotes();
							$modelO->vendor_contact_id			=	$modelId;
							$modelO->general_note				=	$data['name'];	
							$modelO->save();
						}
					}

					Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::route($this->model.".index", $obj->vendor_id);

				}else{
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				
			}
		}
	}// end update()
	 
	/**
	* Function for delete by id and vendor id
	*
	* @return redirect page. 
	*/	
	public function delete($id = 0, $vendorId=0){
		$userDetails	=	VendorContact::find($id); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($id){		
			VendorContact::where('id',$id)->where('vendor_id', $vendorId)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()


	public function view($modelId = 0, $vendorId){
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$model	=	VendorContact::where('id',"$modelId")->first();
		}else{
			$model	=	VendorContact::where('id',"$modelId")->where('group_id',Session::get('group'))->first();	
		}
		$custAddressDetails = 	CustomerAddress::where('customer_id', $modelId)
											->first(); 


		if(empty($model)) {
			return Redirect::route($this->model.".index", $vendorId);
		}
		$reporting_freq = config()->get('reporting_freq');
		return  View::make("admin.$this->model.view",compact('model','custAddressDetails', 'vendorId', 'reporting_freq'));
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
	
}// end VendorsContactController
