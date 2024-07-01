<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Job;
use App\Model\Customer;
use App\Model\CustomerDetail;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* JobsController Controller
*
* Add your methods in the class below
*
*/
class JobsController extends BaseController {

	public $model		=	'Jobs';
	public $sectionName	=	'Jobs';
	public $sectionNameSingular	=	'Job';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Jobs 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Job::query();
        $searchVariable		=	array();
        //$subcategory        =   array(); 
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
				$DB->whereBetween('jobs.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('jobs.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('jobs.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "customer_name"){
						$DB->where(function ($query) use($fieldValue){
							$query->Orwhere("customers.name","LIKE","%".$fieldValue."%");
							$query->Orwhere("customers.email","LIKE","%".$fieldValue."%");
							$query->Orwhere("customers.phone_number","LIKE","%".$fieldValue."%");
							});
                    } 
                    if($fieldName == "handyman_name"){
						$DB->where(function ($query) use($fieldValue){
							$query->Orwhere("service_providers.name","LIKE","%".$fieldValue."%");
							$query->Orwhere("service_providers.email","LIKE","%".$fieldValue."%");
							$query->Orwhere("service_providers.phone_number","LIKE","%".$fieldValue."%");
							});
                    } 
                    if($fieldName == "job_name"){
						$DB->where(function ($query) use($fieldValue){
							$query->Orwhere("jobs.first_name","LIKE","%".$fieldValue."%");
							$query->Orwhere("service_providers.email","LIKE","%".$fieldValue."%");
							$query->Orwhere("service_providers.phone_number","LIKE","%".$fieldValue."%");
							});
                    } 
                    if($fieldName == "category_name"){
						$DB->where("categories.category_name",'like','%'.$fieldValue.'%');
                    }
                    // if($fieldName == "category_name"){
					// 	$DB->where("sub_categories.category_name",'like','%'.$fieldValue.'%');
					// }
                    if($fieldName == "city"){
						$DB->where("jobs.city",$fieldValue);
                    }
                    if($fieldName == "zip_code"){
						$DB->where("jobs.zip_code",$fieldValue);
                    }
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
        $DB->leftjoin('service_providers','jobs.service_provider_id','service_providers.id');
        $DB->leftjoin('customers','jobs.customer_id','customers.id');
        $DB->leftjoin('categories','jobs.category_id','categories.id');
        $DB->leftjoin('sub_categories','jobs.sub_category_id','sub_categories.id');
        $DB->select("jobs.*",'service_providers.name as handyman_name','customers.name as customer_name','categories.category_name','sub_categories.category_name');
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $categories =   DB::table("categories")->where('is_active',1)->where('is_deleted',0)->pluck("category_name","id")->toArray();
        //$subcategory	=	DB::table("sub_categories")->where("is_active",1)->where("is_deleted",0)->pluck("category_name","id")->toArray();
		return  View::make("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string','categories'));
	}// end index()

	
	/**
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
        $countries	=	DB::table("countries")->where('is_active',1)->where('is_deleted',0)->pluck("country_name","id")->toArray();
        $categories =   DB::table("categories")->where('is_active',1)->where('is_deleted',0)->pluck("category_name","id")->toArray();
        $subcategories = array();
        $questions     = array();
        $serviceprovider  = DB::table('service_providers')->where('is_active',1)->where('is_deleted',0)->pluck("name","id")->toArray();
		return  View::make("admin.$this->model.add",compact('countries','subcategories','categories','serviceprovider','questions'));
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
        $email       =    $formData['email'];
        //echo "<pre>"; print_r($email); die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'first_name'				=> 'required',
                    'last_name'					=> 'required',
                    'category_id'            	=> 'required',
					'email' 					=> 'required|email|unique:jobs',
					'phone_number' 				=> 'required|numeric|unique:jobs',
					'house_number'				=> 'required',
					'street_name'				=> 'required',
					'zip_code'					=> 'required',
					'city'						=> 'required',
                    'country'					=> 'required',
                    'sub_category_id'           => 'required',
                    'type_of_time'              => 'required',
                    'service_provider_id'              => 'required',
				),
				array(
					"first_name.required"			    =>	trans("The first name field is required."),
                    "last_name.required"			    =>	trans("The last name field is required."),
                    "category_id.required"				=>	trans("The category field is required."),
                    "sub_category_id.required"		    =>	trans("The sub category field is required."),
                    "type_of_time.required"		        =>	trans("The preferred time field is required."),
                    "service_provider_id.required"		=>	trans("The handyman field is required."),
					"email.required"			        =>	trans("The email field is required."),
					"email.email"				        =>	trans("The email must be a valid email address."),
					"email.unique"				        =>	trans("The email has already been taken."),
					"house_number.required"		        =>	trans("The house number field is required."),
					"street_name.required"		        =>	trans("The street name field is required."),
					"zip_code.required"			        =>	trans("The zip code field is required."),
					"city.required"				        =>	trans("The city field is required."),
					"country.required"			        =>	trans("The country field is required."),
					"phone_number.required"		        =>	trans("The phone number field is required."),
					"phone_number.unique"		        =>	trans("The phone number is already taken."),
					"phone_number.numeric"		        =>	trans("The phone number must be numeric."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
            //     if(Customer::where('email',$email)){
            //         $customerData  =  Customer::where('email',$email)->first();
            //         $customerId   =   $customerData['id'];
                   
            //     $obj 								    =  new Job;
            //     $obj->job_number                        =  "";
            //     $obj->service_provider_id				=	$request->input('service_provider_id');
            //     $obj->customer_id				        =	1;
			// 	$obj->first_name 						=  $request->input('first_name');
			// 	$obj->last_name 						=  $request->input('last_name');
			// 	$obj->email 							=  $request->input('email');
            //     $obj->phone_number 						=  $request->input('phone_number');
            //     $obj->house_number 						=  $request->input('house_number');
			// 	$obj->street_name 						=  $request->input('street_name');
			// 	$obj->zip_code 							=  $request->input('zip_code');
			// 	$obj->city 								=  $request->input('city');
            //     $obj->country 							=  $request->input('country');
            //     $obj->preferred_date 		        	=  (!empty($request->input('preferred_date'))) ? date("Y-m-d",strtotime($request->input('preferred_date'))) : "";
            //     $obj->request_date 		            	=  "";
            //     $obj->type_of_time 		  		    	=  $request->input('type_of_time');
            //     $obj->category_id 		  		    	=  $request->input('category_id');
            //     $obj->sub_category_id 		  		    =  $request->input('sub_category_id');
            //     $obj->describe_job 		  		        = "";
            //     $obj->currency 		  		            = "";
                
			// 	$obj->save();
            //     $jobId					=	$obj->id;
            //     if(!empty($jobId)){
			// 		$job_number				=	'CUSTOMER'.'-0000'.$jobId;
			// 		Job::where('id',$jobId)->update(array('job_number'=>$job_number));
            //     }
            //    // echo "<pre>"; print_r($obj); die; 
            //     }else{
            //         echo "<pre>"; print_r("123"); die;
            //     $CustomerData 									=  new Customer;
			// 	$CustomerData->first_name 						=  $request->input('first_name');
			// 	$CustomerData->last_name 						=  $request->input('last_name');
			// 	$CustomerData->name 							=  $CustomerData->first_name.' '.$CustomerData->last_name;
			// 	$CustomerData->email 							=  $request->input('email');
			// 	$CustomerData->phone_number 					=  $request->input('phone_number');
			// 	$CustomerData->password	 						=  "";
			// 	$CustomerData->save();
			// 	$userId					                        =	$CustomerData->id;

			// 	if(!empty($userId)){
			// 		$customer_id				=	'CUSTOMER'.'-0000'.$userId;
			// 		Customer::where('id',$userId)->update(array('customer_id'=>$customer_id));
			// 	}

			// 	$customerdetails 									=  new CustomerDetail;
			// 	$customerdetails->customer_id 						=  $userId;
			// 	$customerdetails->house_number 						=  $request->input('house_number');
			// 	$customerdetails->street_name 						=  $request->input('street_name');
			// 	$customerdetails->near_by 							=  (!empty($request->input('near_by'))) ? $request->input('near_by') : "";
			// 	$customerdetails->zip_code 							=  $request->input('zip_code');
			// 	$customerdetails->city 								=  $request->input('city');
			// 	$customerdetails->country 							=  $request->input('country');
            //     $customerdetails->save();
                

            //     $obj 									=  new Job;
            //     $obj->job_number                        =  "";
            //     $obj->service_provider_id				=	$request->input('service_provider_id');
            //     $obj->customer_id				        =	$userId;
			// 	$obj->first_name 						=  $request->input('first_name');
			// 	$obj->last_name 						=  $request->input('last_name');
			// 	$obj->email 							=  $request->input('email');
            //     $obj->phone_number 						=  $request->input('phone_number');
            //     $obj->house_number 						=  $request->input('house_number');
			// 	$obj->street_name 						=  $request->input('street_name');
			// 	$obj->zip_code 							=  $request->input('zip_code');
			// 	$obj->city 								=  $request->input('city');
            //     $obj->country 							=  $request->input('country');
            //     $obj->preferred_date 		        	=  (!empty($request->input('preferred_date'))) ? date("Y-m-d",strtotime($request->input('preferred_date'))) : "";
            //     $obj->request_date 		            	=   "";
            //     $obj->type_of_time 		  		    	=  $request->input('type_of_time');
            //     $obj->category_id 		  		    	=  $request->input('category_id');
            //     $obj->sub_category_id 		  		    =  $request->input('sub_category_id');
            //     $obj->describe_job 		  		        = "";
            //     $obj->currency 		  		            = "";

			// 	$obj->save();
            //     $jobId					=	$obj->id;
            //     if(!empty($jobId)){
			// 		$job_number				=	'JOB'.'-0000'.$jobId;
			// 		Job::where('id',$jobId)->update(array('job_number'=>$job_number));
			// 	}
				

            //     }


                $obj 									=  new Job;
                $obj->job_number                        =  "";
                $obj->service_provider_id				=  $request->input('service_provider_id');
                $obj->customer_id				        =  4;
				$obj->first_name 						=  $request->input('first_name');
				$obj->last_name 						=  $request->input('last_name');
				$obj->email 							=  $request->input('email');
                $obj->phone_number 						=  $request->input('phone_number');
                $obj->house_number 						=  $request->input('house_number');
				$obj->street_name 						=  $request->input('street_name');
				$obj->zip_code 							=  $request->input('zip_code');
				$obj->city 								=  $request->input('city');
                $obj->country 							=  $request->input('country');
                $obj->preferred_date 		        	=  (!empty($request->input('preferred_date'))) ? date("Y-m-d",strtotime($request->input('preferred_date'))) : "";
                $obj->request_date 		            	=  !empty($thisData['request_date']) ? ($thisData['request_date']): NULL;
                $obj->type_of_time 		  		    	=  $request->input('type_of_time');
                $obj->category_id 		  		    	=  $request->input('category_id');
                $obj->sub_category_id 		  		    =  $request->input('sub_category_id');
                $obj->describe_job 		  		        =  "";
                $obj->currency 		  		            =  "";

				$obj->save();
                $jobId					=	$obj->id;
                if(!empty($jobId)){
					$job_number				=	'JOB'.'-0000'.$jobId;
					Job::where('id',$jobId)->update(array('job_number'=>$job_number));
				}
				
               
			
				if(!$jobId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($jobId = 0,Request $request){
		$model					=	Job::findorFail($jobId);
		if(empty($model)) {
			return Redirect::back();
        } 
        $countries	=	DB::table("countries")->where('is_active',1)->where('is_deleted',0)->pluck("country_name","id")->toArray();
        $categories =   DB::table("categories")->where('is_active',1)->where('is_deleted',0)->pluck("category_name","id")->toArray();
        $subcategories = array();
        $questions     = array();
        $serviceprovider  = DB::table('service_providers')->where('is_active',1)->where('is_deleted',0)->pluck("name","id")->toArray();
        $model->request_date 			=  (!empty($model->request_date)) ? date("m/d/Y",strtotime($model->request_date)) : "";
		return  View::make("admin.$this->model.edit",compact('model','countries','categories','subcategories','questions','serviceprovider'));
	} // end edit()
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Job::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'first_name'				=> 'required',
                    'last_name'					=> 'required',
                    'category_id'            	=> 'required',
					'email' 					=> "required|email|unique:jobs,email,$modelId",
					'phone_number' 				=> "required|numeric|unique:jobs,phone_number,$modelId",
					'house_number'				=> 'required',
					'street_name'				=> 'required',
					'zip_code'					=> 'required',
					'city'						=> 'required',
                    'country'					=> 'required',
                    'sub_category_id'           => 'required',
                    'type_of_time'              => 'required',
                    'service_provider_id'              => 'required',
				),
				array(
					"first_name.required"			    =>	trans("The first name field is required."),
                    "last_name.required"			    =>	trans("The last name field is required."),
                    "category_id.required"				=>	trans("The category field is required."),
                    "sub_category_id.required"		    =>	trans("The sub category field is required."),
                    "type_of_time.required"		        =>	trans("The preferred time field is required."),
                    "service_provider_id.required"		=>	trans("The handyman field is required."),
					"email.required"			        =>	trans("The email field is required."),
					"email.email"				        =>	trans("The email must be a valid email address."),
					"email.unique"				        =>	trans("The email has already been taken."),
					"house_number.required"		        =>	trans("The house number field is required."),
					"street_name.required"		        =>	trans("The street name field is required."),
					"zip_code.required"			        =>	trans("The zip code field is required."),
					"city.required"				        =>	trans("The city field is required."),
					"country.required"			        =>	trans("The country field is required."),
					"phone_number.required"		        =>	trans("The phone number field is required."),
					"phone_number.unique"		        =>	trans("The phone number is already taken."),
					"phone_number.numeric"		        =>	trans("The phone number must be numeric."),
				)
                
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				
                $obj 									=  $model;
                $obj->service_provider_id				=  $request->input('service_provider_id');
                $obj->customer_id				        =  4;
				$obj->first_name 						=  $request->input('first_name');
				$obj->last_name 						=  $request->input('last_name');
				$obj->email 							=  $request->input('email');
                $obj->phone_number 						=  $request->input('phone_number');
                $obj->house_number 						=  $request->input('house_number');
				$obj->street_name 						=  $request->input('street_name');
				$obj->zip_code 							=  $request->input('zip_code');
				$obj->city 								=  $request->input('city');
                $obj->country 							=  $request->input('country');
                $obj->preferred_date 		        	=  (!empty($request->input('preferred_date'))) ? date("Y-m-d",strtotime($request->input('preferred_date'))) : "";
                $obj->request_date 		            	=  !empty($thisData['request_date']) ? ($thisData['request_date']): NULL;
                $obj->type_of_time 		  		    	=  $request->input('type_of_time');
                $obj->category_id 		  		    	=  $request->input('category_id');
                $obj->sub_category_id 		  		    =  $request->input('sub_category_id');
                $obj->describe_job 		  		        =  "";
                $obj->currency 		  		            =  "";

				$obj->save();
				$JobID					=	$obj->id;

				if(!$JobID){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->model.".index");
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
		$userDetails	=	Job::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$email 			=	'delete_'.$userId.'_'.$userDetails->email;		
			$phone_number 		=	'delete_'.$userId.'_'.$userDetails->phone_number;
			Job::where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($serviceProviderId = 0){
        $model	=	ServiceProvider::where('service_providers.id',"$serviceProviderId")
                            ->leftJoin ('lookups as identity_type', 'service_providers.type_of_document', '=', 'identity_type.id')
                            ->select('service_providers.*','identity_type.code as identity_name')->first();
                            
        $companyDetails =  ServiceProviderCompanyDetail::where('service_provider_id',$serviceProviderId)
                                                        ->leftjoin('countries','service_provider_companies.country','countries.id')
                                                        ->select("service_provider_companies.*",'countries.country_name')->first();
        $bankData		=	ServiceProviderBankDetail::where('service_provider_id',$serviceProviderId)->first();							
        $serviceData	=	ServiceProviderService::where('service_provider_id',$serviceProviderId) 
                                                    ->leftjoin('categories','service_provider_services.category_id','categories.id')
                                                    ->select("service_provider_services.*",'categories.category_name as category_name')->first();							
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		return  View::make("admin.$this->model.view",compact('model','companyDetails','bankData','serviceData'));
	} // end view()
	
	public function getSubCategory(Request $request){
		$categoryid	=	$request->input("categoryid");
		$selctedid	=	$request->input("selctedid");
		$subcategories = DB::table('sub_categories')->where("category_id",$categoryid)->where("is_active",1)->where("is_deleted",0)->get()->toArray();
		return  View::make("admin.$this->model.add_more_subcategories",compact('subcategories','selctedid'));
    }
    
    public function getQuestion(Request $request){
		$subcategoryid	=	$request->input("subcategoryid");
		$selctedid	=	$request->input("selectedid");
		$questions = DB::table('sub_category_questions')->where("sub_category_id",$subcategoryid)->get()->toArray();
		return  View::make("admin.$this->model.add_more_questions",compact('questions','selctedid'));
	}

}// end BrandsController
