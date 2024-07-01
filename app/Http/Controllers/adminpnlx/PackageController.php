<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Package;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use Illuminate\Http\Request;
use App\Model\DropDown;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;
use Stripe;
use Stripe\Plan;

/**
* PackageController Controller
*
* Add your methods in the class below
*
* This file will render views from views/admin/Package
*/
class PackageController extends BaseController {

	public  $model		=	'Package';
	public  $sectionName	=	'Plan Management';
	public  $sectionNameSingular	=	'Plan';
	
	public function __construct(Request $request) {
		parent::__construct();

		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->stripe = Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		$this->request = $request;
	}
	 
	/**
	* Function for display all Packages 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Package::query();
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
				$DB->whereBetween('packages.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('packages.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('packages.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("packages.name",'like','%'.$fieldValue.'%');
					}
					
					if($fieldName == "is_active"){
						$DB->where("packages.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("is_deleted",0);
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results = $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()

	
	/**
	* Function for add new package
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
		return  View::make("admin.$this->model.add");
	}// end add()
	
/**
* Function for save new package
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
					'name'						=> 'required',
					'price' 					=> 'required|numeric|min:1',
					'month' 					=> 'required|numeric|min:1',
					'order_type' 				=> 'required',	
				),
				array(
					"name.required"				=>	trans("The name field is required."),
					"price.required"			=>	trans("The price field is required."),
					"price.numeric"				=>	trans("The price field is only numeric."),
					"price.min"					=>	trans("The price field is minimum 1."),
					"month.required"			=>	trans("The no. of month field is required."),
					"month.numeric"				=>	trans("The no. of month field is only numeric."),
					"month.min"					=>	trans("The no. of month field is minimum 1."),
					"order_type.required"		=>	trans("The order field required"),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new Package;			
				$obj->name 								=  $request->input('name');
				$obj->price 							=  $request->input('price');
				$obj->no_of_month 						=  $request->input('month');
				$obj->slug								=  	$this->getSlug($obj->name,'name',"Package");
				$obj->order_type 	    				=  $request->input('order_type');
				$obj->description 	    				=  $request->input('description');
				$obj->is_active							=  1;
				$obj->save();

				//$stripe = $this->stripe = Stripe\Stripe::setApiKey();
				$stripe = new \Stripe\StripeClient(
				  env('STRIPE_SECRET')
				);

				$pl =  \Stripe\Plan::create(array(
				  "amount" => $obj->price*100,
				  "interval" => "month",
				  "product" => array(
				    "name" => $obj->name
				  ),
				  "currency" => "usd",
				  "id" => $this->getSlug($obj->name,'name',"Package")
				));
				$planId					=	$obj->id;
				if(!$planId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	/**
	* Function for update status
	*
	* @param $modelId as id of package 
	* @param $status as status of package 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('packages',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeStatus()
	
	/**
	* Function for display page for edit package
	*
	* @param $modelId id  of package
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		
		
		$model					=	Package::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		return  View::make("admin.$this->model.edit",compact('model'));
	} // end edit()
	
	
	/**
	* Function for update package 
	*
	* @param $modelId as id of package 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Package::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		//$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'						=> 'required',
					'price' 					=> 'required|numeric|min:1',
					'month' 					=> 'required|numeric|min:1',
					"order_type"				=>	'required',

				),
				array(
					"name.required"				=>	trans("The name field is required."),
					"price.required"			=>	trans("The price field is required."),
					"price.numeric"				=>	trans("The price field is only numeric."),
					"price.min"					=>	trans("The price field is minimum 1."),
					"month.required"			=>	trans("The no. of month field is required."),
					"month.numeric"				=>	trans("The no. of month field is only numeric."),
					"month.min"					=>	trans("The no. of month field is minimum 1."),
					"order_type.required"		=>	trans("The order field required"),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 							=  $model;	
				$obj->name 						=  $request->input('name');
				$obj->price 					=  $request->input('price');
				$obj->no_of_month 	    		=  $request->input('month');
				$obj->order_type 	    		=  $request->input('order_type');
				$obj->description 	    				=  $request->input('description');
				$obj->save();
				$planId					=	$obj->id;

				/*$stripe = new \Stripe\StripeClient(
				  env('STRIPE_SECRET')
				);*/

				/*$stripe->plans->update(
				  'pro',
				  ['amount' => 150, 'amount_decimal' => 150]
				);*/

				if(!$planId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}// end update()
	 
	/**
	* Function for delete package
	*
	* @param $modelId as id of package 
	* @param $modelStatus as status of package 
	*
	* @return redirect page. 
	*/	
	public function delete($modelId = 0){
		$userDetails	=	Package::find($modelId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($modelId){	
			Package::where('id',$modelId)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Package::where('id',"$modelId")->select('packages.*')->first(); 
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model'));
	} // end view()
	

}// end BrandsController
