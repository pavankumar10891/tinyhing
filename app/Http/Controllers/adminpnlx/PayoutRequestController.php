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
use App\Model\Earning;

/**
* PayoutRequestController Controller
*
* Add your methods in the class below
*
*/
class PayoutRequestController extends BaseController {

	public $model		=	'Payout';
	public $sectionName	=	'Payouts Request';
	public $sectionNameSingular	= 'Payout';
	
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
		$DB					=	Payout::query();
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
			
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("users.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "phone"){
						$DB->where("users.phone_number",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "email"){
						$DB->where("users.email",'like','%'.$fieldValue.'%');
					}
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->leftjoin('earnings','payouts.nanny_id','earnings.nanny_id');
		$DB->leftjoin('users','users.id','earnings.nanny_id');

		$DB->select("payouts.*","users.name","users.email")->groupBy('payouts.id');
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
	

	public function payoutDownload(Request $request){ 
		$DB					=	Payout::query();
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
			
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("users.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "phone"){
						$DB->where("users.phone_number",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "email"){
						$DB->where("users.email",'like','%'.$fieldValue.'%');
					}
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->leftjoin('earnings','payouts.nanny_id','earnings.nanny_id');
		$DB->leftjoin('users','users.id','earnings.user_id');

		$DB->select("payouts.*","users.name","users.email")->groupBy('payouts.id');
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results = $DB->orderBy($sortBy, $order)->get();

		return Excel::download(new PayoutExport($results), 'payout-'.time().'.xls');
		exit;
	}

	public function edit($modelId = 0,Request $request){
		
		
		$model					=	Payout::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		return  View::make("admin.$this->model.edit",compact('model'));
	} // end edit()

	function update($modelId,Request $request){
		$model					=	Payout::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		//$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'amount' 					=> 'required|numeric|min:1',
				),
				array(
					
					"amount.required"			=>	trans("The amount field is required."),
					"amount.numeric"				=>	trans("The amount field is only numeric."),
					"amount.min"					=>	trans("The amount field is minimum 1."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 							=  $model;	
				$obj->amount 					=  $request->input('amount');
				$obj->save();
				
				if($obj->save()){
					$payoutId					=	$obj->id;
					$earobj =  Earning::where('payout_id', $payoutId)->update(['amount' => $request->input('amount')]);
				}

				if(!$payoutId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()

	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has Unmarked successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has Marked successfully");
		}
		Payout::where('id', $modelId)->update(['status' => $status]);
		//$this->_update_all_status('payouts',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
}// end PayoutRequestController