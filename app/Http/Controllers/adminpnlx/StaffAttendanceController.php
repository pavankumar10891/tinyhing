<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Payout;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayoutExport;
use App\Model\StaffAttendance;

/**
* StaffAttendanceController Controller
*
* Add your methods in the class below
*
*/
class StaffAttendanceController extends BaseController {

	public $model		=	'StaffAttendance';
	public $sectionName	=	'Staff Attendance';
	public $sectionNameSingular	= 'StaffAttendance';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	/**
	* Function for display all Customers 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  

		if(Auth::guard('admin')->user()->user_role !='super_admin'){
			Session::flash('error', 'You Are not authorized access this page');
			Redirect::back();
		}

		$DB					=	StaffAttendance::query();
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
				$DB->whereBetween('staff_attedance.checkin_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('staff_attedance.checkin_date','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('staff_attedance.checkin_date','<=' ,[$dateE." 00:00:00"]); 						
			}
			
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("users.name",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->leftjoin('admins','admins.id','staff_attedance.user_id');

		$DB->select("staff_attedance.*","admins.name");
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);

		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();


		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()
	

	

	public function edit($modelId = 0,Request $request){
		
		if(Auth::guard('admin')->user()->user_role !='super_admin'){
			Session::flash('error', 'You Are not authorized access this page');
			Redirect::back();
		}
		$model					=	StaffAttendance::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		return  View::make("admin.$this->model.edit",compact('model'));
	} // end edit()

	function update($modelId,Request $request){
		$model					=	StaffAttendance::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		//$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'checkin_date' 					=> 'required',
					'checkin_time' 					=> 'required',
					//'checkout_time' 				=> 'required',
				),
				array(
					
					"checkin_date.required"			=>	trans("The checkin date field is required."),
					"checkin_time.required"			=>	trans("The checkin time field is required."),
					"checkout_time.required"		=>	trans("The checkout time field is required."),
					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 							=  $model;	
				$obj->checkin_date 				=  $request->input('checkin_date');
				$obj->checkin_time 				=  $request->input('checkin_time');
				$obj->checkout_time 			=  $request->input('checkout_time');
				$obj->save();
				$staffid					=	$obj->id;
				

				if(!$staffid){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()

	public function changeStatus($status){ 
		if($status == '1'){
			StaffAttendance::insert(['user_id' => Auth::guard('admin')->user()->id, 'checkin_date' => date('Y-m-d'), 'checkin_time' => date('h:i:s'), 'status' =>'1']);
			$statusMessage	=	trans($this->sectionNameSingular." has been checkin successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();
		}else{
			$check = StaffAttendance::where('checkout_time', null)->where('user_id', Auth::guard('admin')->user()->id)->orderBy('id', 'desc')->first();
			StaffAttendance::where('id',$check->id)->update(['status' => '0', 'checkout_time' => date('h:i:s')]);
			$statusMessage	=	trans($this->sectionNameSingular." has been checkout successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
		}
	}// end changeStatus()

}// end StaffAttendanceController