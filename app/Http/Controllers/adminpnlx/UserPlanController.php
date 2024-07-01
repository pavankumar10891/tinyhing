<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\UserPlans;
use App\Model\ScheduleInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* Vender Controller
*
* Add your methods in the class below
*
*/
class UserPlanController extends BaseController {

	public $model		=	'UserPlans';
	public $sectionName	=	'Subscription';
	public $sectionNameSingular	= 'Subscriptions';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Nannies 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	UserPlans::query();
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
				$DB->whereBetween('user_plans.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('user_plans.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('user_plans.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "plans"){
						$DB->where("user_plans.plan_id", $fieldValue);
					}
					elseif($fieldName == "is_active"){
						$DB->where("user_plans.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
	
		$DB->leftjoin('users', 'user_plans.user_id', '=', 'users.id');
		$DB->leftjoin('packages', 'user_plans.plan_id', '=', 'packages.id');
		$DB->where("user_plans.status",1);
		$DB->select("user_plans.*"  , "users.name as client_name" , "packages.name as plan_name" ,"packages.price" );
	    $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';	
	    $records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
	    $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
	//	dd($results);
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()
	
	/**
	 * Function for add new Vender
	 *
	 * @param null
	 *
	 * @return view page. 
	 */


	public function add(Request $request){ 
		return  View::make("admin.$this->model.add");
	}// end add()
	


	/**
	* Function for update status
	*
	* @param $modelId as id of Vender 
	* @param $status as status of Vender 
	*
	* @return redirect page. 
	*/	

	public function changeStatus($id){ 
		$status = User::where('id',$id)->value('is_active');
		if($status == '1'){
			User::where('id',$id)->update(['is_active' =>'0']);
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();
		}else{
			User::where('id',$id)->update(['is_active' => '1']);
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
			Session::flash('flash_notice', $statusMessage); 
			return Redirect::back();	
		}
	}// end changeStatus()
	
	/**
	* Function for display page for edit Vender
	*
	* @param $modelId id  of Vender
	*
	* @return view page. 
	*/

	
	

	
	
	 
	/**
	* Function for update Vender  status
	*
	* @param $modelId as id of Vender 
	* @param $modelStatus as status of Vender 
	*
	* @return redirect page. 
	*/	

	 public function delete($UserId = 0){
		$UserDetails = User::find($UserId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($UserId){
			$email = 'delete_'.$UserId.'_'.$UserDetails->email;
			$phone_number = 'delete_'.$UserId.'_'.$UserDetails->phone_number;
			$deleteDate = date("Y-m-d H:i:s");
			User::where('id',$UserId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number, 'deleted_at' => $deleteDate));
			userCertificates::where('user_id',$UserId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	UserPlans::leftjoin('users', 'user_plans.user_id', '=', 'users.id')
							  ->leftjoin('packages', 'user_plans.plan_id', '=', 'packages.id')                
							  ->where('user_plans.id',$modelId)->where('user_plans.status',1)->select('user_plans.*',"users.name as client_name" , "packages.name as plan_name" ,"packages.price")->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
	 
        return view::make("admin.$this->model.view",compact('model'));
	} // end view()


}// end VenderController