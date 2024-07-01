<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Earning;
use App\Model\Payout;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* PayoutController Controller
*
* Add your methods in the class below
*
*/
class PayoutController extends BaseController {

	public $modelName		=	'Payoutrequest';
	public $sectionName	=	'Payment Request';
	public $sectionNameSingular	=	'Payouts';
	
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
		$DB					=	Payout::query();
		$DB->leftJoin('users as n', 'n.id', 'payouts.nanny_id')->select('n.name as nanny', 'payouts.id', 'payouts.payout_date', 'payouts.status', 'payouts.created_at');
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
				$DB->whereBetween('created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "coupon_code"){
						$DB->where("coupon_code",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "coupon_type"){
						$DB->where("coupon_type",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		if(!empty($request->input('per_page'))){
			$searchVariable["per_page"]	=	$records_per_page;
		}
		$results            =   $DB->get();
		$sortBy             =   ($request->input('sortBy')) ? $request->input('sortBy') : 'payouts.created_at';
	    $order              =   ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results            =   $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string	=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string		=	http_build_query($complete_string);
		$results->appends($request->all())->render();

		echo "<pre>";
		print_r($results);die;

		return  View::make("admin.$this->modelName.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()

	/**
	* Function for add new coupon
	*
	* @param null
	*
	* @return view page. 
	*/

}// End Coupon Code COntroller
