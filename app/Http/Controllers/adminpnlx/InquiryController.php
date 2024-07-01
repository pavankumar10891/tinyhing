<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\CustomerContact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* Vender Controller
*
* Add your methods in the class below
*
*/
class InquiryController extends BaseController {

	public $model		=	'Inquiry';
	public $sectionName	=	'Inquiries';
	public $sectionNameSingular	= 'Inquiry';
	
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
		$DB					=	CustomerContact::query();
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
				$DB->whereBetween('customer_contacts.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('customer_contacts.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('customer_contacts.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("customer_contacts.name",'like','%'.$fieldValue.'%');
					}elseif($fieldName == "email"){

						$DB->where("customer_contacts.email",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
	
		$DB->select("customer_contacts.*" );
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
	
	/**
	 * Function for add new Vender
	 *
	 * @param null
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
		$UserDetails = CustomerContact::find($UserId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($UserId){
			$email = 'delete_'.$UserId.'_'.$UserDetails->email;
					$deleteDate = date("Y-m-d H:i:s");
			CustomerContact::where('id',$UserId)->update(array('is_deleted'=>1,'email'=>$email, 'deleted_at' => $deleteDate));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	CustomerContact::where('id',$modelId)->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
	 
        return view::make("admin.$this->model.view",compact('model'));
	} // end view()


}// end VenderController