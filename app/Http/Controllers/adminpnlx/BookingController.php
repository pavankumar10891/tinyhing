<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Booking;
use App\Model\BookingDetails;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* BookingController Controller
*
* Add your methods in the class below
*
*/
class BookingController extends BaseController {

	public $modelName		=	'booking';
	public $sectionName	=	'Booking';
	public $sectionNameSingular	=	'Booking';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->modelName);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	 
	/**
	* Function for display all customers 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  

		$DB					=	Booking::query();
		$DB->leftJoin('users as u', 'u.id', 'bookings.user_id')->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->select('u.name as customer', 'n.name as nanny', 'bookings.id', 'bookings.booking_date', 'bookings.status', 'bookings.created_at','bookings.start_date','bookings.end_date');
		
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		if (($request->all())) {
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
			if(isset($searchData['per_page'])){
				unset($searchData['per_page']);
			}
            if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('booking_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('booking_date','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('booking_date','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "nanny"){
						$DB->where("n.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer"){
						$DB->where("u.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "coupon_type"){
						$DB->where("coupon_type",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("status",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		if(!empty($request->input('per_page'))){
			$searchVariable["per_page"]	=	$records_per_page;
		}
		$results            =   $DB->where('bookings.is_deleted',0)->get();
		$sortBy             =   ($request->input('sortBy')) ? $request->input('sortBy') : 'bookings.created_at';
	    $order              =   ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results            =   $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string	=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string		=	http_build_query($complete_string);
		$results->appends($request->all())->render();

		//echo "<pre>";
		//print_r($results);die;

		return  View::make("admin.$this->modelName.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()

	/**
	* Function for add new coupon
	*
	* @param null
	*
	* @return view page. 
	*/

	public function add(){  
		return  View::make("admin.$this->modelName.add");
	}// end add()
	


    /**
    * Function for save new customer
    *
    * @param null
    *
    * @return redirect page. 
    */
	public function save(Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'coupon_code'						=> 'required|unique:coupon_codes,coupon_code',
					'start_date'						=> 'required',
					'end_date'							=> 'required',
					'coupon_type'						=> 'required',
					'name'     						    => 'required',
					'amount' 							=> 'required|numeric',					
				),
				array(
					"coupon_code.required"				=>	trans("The coupon code field is required."),
					"name.required"	        			=>	trans("The coupon name field is required."),
					"coupon_code.unique"				=>	trans("The coupon code is already exists."),
					"amount.numeric"					=>	trans("The amount field allowed only number value."),
					"start_date.required"				=>	trans("The start date field is required."),
					"end_date.required"					=>	trans("The end date field is required."),
					"coupon_type.required"				=>	trans("The coupon code type field is required."),
					"amount.required"					=>	trans("The amount field is required."),					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new Booking;
				$obj->coupon_code 						=  $request->input('coupon_code');
				$obj->name      						=  $request->input('name');
				$obj->coupon_type 						=  $request->input('coupon_type');
				$obj->start_date						=  $request->input('start_date');
				$obj->end_date							=  $request->input('end_date');
				$obj->amount 							=  $request->input('amount');
				$obj->save();
				$coupon_code_id					=	$obj->id;
				if(!$coupon_code_id){
					Session::flash('error', trans("Something went wrong.please try again")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->modelName.".index");
			}
		}
	}//end save()


	/**
	* Function for edit  coupon 
	*
	* @param $couponId as id of coupon 
	*
	* @return redirect page. 
	*/

    public function edit($couponId = 0,Request $request){
		$model		=	Booking::where('id',$couponId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
	 	return View::make("admin.$this->modelName.edit",compact('model'));
	} // end edit()


	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($couponId,Request $request){
		$model					=	Booking::findorFail($couponId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
                    'coupon_code'						=> 'required|unique:coupon_codes,coupon_code,'.$couponId,
					'start_date'						=> 'required',
					'end_date'							=> 'required',
					'coupon_type'						=> 'required',
					'name'	        					=> 'required',
					'amount' 							=> 'required|numeric',	
				),
				array(
                    "coupon_code.required"				=>	trans("The coupon code field is required."),
                    "name.required"	        			=>	trans("The coupon name field is required."),
					"coupon_code.unique"				=>	trans("The coupon code is already exists."),
					"start_date.required"				=>	trans("The start date field is required."),
					"end_date.required"					=>	trans("The end date field is required."),
					"coupon_type.required"				=>	trans("The coupon code type field is required."),
					"amount.required"					=>	trans("The amount field is required."),			
					"amount.numeric"					=>	trans("The amount field allowed only number value."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 						            =  Booking::find($couponId);
				$obj->coupon_code 						=  $request->input('coupon_code');
                $obj->name      						=  $request->input('name');
				$obj->coupon_type 						=  $request->input('coupon_type');
				$obj->start_date						=  $request->input('start_date');
				$obj->end_date							=  $request->input('end_date');
				$obj->amount 							=  $request->input('amount');
				$obj->save();
				$coupon_Id						=	$obj->id;
				if(!$coupon_Id){
					Session::flash('error', trans("Something went wrong. please try again")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->modelName.".index");
			}
		}
	}// end update()


    /**
	* Function for update status
	*
	* @param $couponId as id of coupon 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($id){ 
		$status = Booking::where('id',$id)->value('is_active');
		if($status == '1'){
			Booking::where('id',$id)->update(['is_active' =>'0']);
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();
		}else{
			Booking::where('id',$id)->update(['is_active' => '1']);
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
		}
	}// end changeStatus()

    /**
	* Function for delete coupon
	*
	* @param $couponId as id of coupon 
	* @param $modelStatus as status of coupon 
	*
	* @return redirect page. 
	*/	

	 public function delete($couponId = 0){
		$UserDetails = Booking::find($couponId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($couponId){
			Booking::where('id',$couponId)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()


    /**
	* Function for View Bookings
	*
	* @param $couponId as id of Bookings 
	*
	* @return redirect page. 
	*/

	public function view($id = 0){
		$results	=	BookingDetails::where('booking_id',$id)->orderBy('booking_date','ASC')->get();      
		if(empty($results)){
			return Redirect::route($this->modelName.".index");
		}
		return  View::make("admin.$this->modelName.view",compact('results'));
	} // end view()
}// End 
