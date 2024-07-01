<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\ServiceProvider;
use App\Model\ServiceProviderBankDetail;
use App\Model\ServiceProviderCompanyDetail;
use App\Model\ServiceProviderService;
use App\Model\Lookup;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* HandymansController Controller
*
* Add your methods in the class below
*
*/
class HandymansController extends BaseController {

	public $model		=	'Handymans';
	public $sectionName	=	'Handymans';
	public $sectionNameSingular	=	'Handyman';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Handymans 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	ServiceProvider::query();
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
				$DB->whereBetween('service_providers.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('service_providers.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('service_providers.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("service_providers.name",'like','%'.$fieldValue.'%');
                    }
                    if($fieldName == "handyman_id"){
						$DB->where("service_providers.handyman_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "phone_number"){
						$DB->where("service_providers.phone_number",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "company_name"){
						$DB->where("service_provider_companies.company_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "email"){
						$DB->where("service_providers.email",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("service_providers.is_active",$fieldValue);
                    }
                    if($fieldName == "is_verified"){
						$DB->where("service_providers.is_verified",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
        $DB->where("is_deleted",0);
        $DB->leftjoin('service_provider_companies','service_providers.id','service_provider_companies.service_provider_id');
        $DB->select("service_providers.*",'service_provider_companies.company_name as company_name');
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
	* Function for add new customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function add(){  
        $countries	=	DB::table("countries")->where('is_active',1)->where('is_deleted',0)->pluck("country_name","id")->toArray();
        $LookUp	=	new Lookup();
        $identity_types	=	$LookUp->getActiveIdentityTypeList();
        $categories = array();
		return  View::make("admin.$this->model.add",compact('countries','categories','identity_types'));
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
					'first_name'				=> 'required',
                    'last_name'					=> 'required',
                    'handyman_id'				=> 'required',
                    'gender'					=> 'required',
                    'dob'   					=> 'required',
                    'company_name'				=> 'required',
                    'chamber_of_commerce_no'	=> 'required',
                    'tax_number'            	=> 'required',
                    'insured'               	=> 'required',
                    'category_id'            	=> 'required',
                    'radius_range'            	=> 'required',
                    'type_of_document'        	=> 'required',
                    'document_front'         	=> 'required|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
                    'document_back'             => 'required|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'email' 					=> 'required|email|unique:service_providers',
					'phone_number' 				=> 'required|numeric|unique:service_providers',
					'house_number'				=> 'required',
					'street_name'				=> 'required',
					'near_by'					=> 'required',
					'zip_code'					=> 'required',
					'city'						=> 'required',
					'country'					=> 'required',
					'password'					=> 'required|min:8',
					'confirm_password'  		=> 'required|min:8|same:password',
				),
				array(
					"first_name.required"			    =>	trans("The first name field is required."),
                    "last_name.required"			    =>	trans("The last name field is required."),
                    "handyman_id.required"		        =>	trans("The handyman ID field is required."),
                    "gender.required"			        =>	trans("The gender field is required."),
                    "dob.required"				        =>	trans("The date of birth field is required."),
                    "company_name.required"		        =>	trans("The company name field is required."),
                    "chamber_of_commerce_no.required"	=>	trans("The chamber of commerce number field is required."),
                    "tax_number.required"				=>	trans("The tax number field is required."),
                    "insured.required"			    	=>	trans("The insured field is required."),
                    "category_id.required"				=>	trans("The service interest field is required."),
                    "radius_range.required"				=>	trans("The radius range field is required."),
                    "type_of_document.required"			=>	trans("The type of document field is required."),
                    "document_front.required"			=>	trans("The document front field is required."),
                    "document_back.required"			=>	trans("The document back field is required."),
                    "document_front.mimes"				=>	trans("The document front must be a file of type: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel'."),
                    "document_back.mimes"				=>	trans("The document back must be a file of type: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel'."),
					"email.required"			        =>	trans("The email field is required."),
					"email.email"				        =>	trans("The email must be a valid email address."),
					"email.unique"				        =>	trans("The email has already been taken."),
					"house_number.required"		        =>	trans("The house number field is required."),
					"street_name.required"		        =>	trans("The street name field is required."),
					"near_by.required"			        =>	trans("The near by field is required."),
					"zip_code.required"			        =>	trans("The zip code field is required."),
					"city.required"				        =>	trans("The city field is r	equired."),
					"country.required"			        =>	trans("The country field is required."),
					"phone_number.required"		        =>	trans("The phone number field is required."),
					"phone_number.unique"		        =>	trans("The phone number is already taken."),
					"phone_number.numeric"		        =>	trans("The phone number must be numeric."),
					"password.required"			        =>	trans("The password field is required."),
					"password.min"				        =>	trans("The password must be atleast 8 characters."),
					"confirm_password.required"	        =>	trans("The confirm password field is required."),
					"confirm_password.same"		        =>	trans("The confirm password not matched with password."),
					"confirm_password.min"		        =>	trans("The confirm password must be atleast 8 characters."),
				)
			);
			$password 					= 	$request->input('password');
			if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
				$correctPassword		=	Hash::make($password);
			}else{
				$errors 				=	$validator->messages();
				$errors->add('password', trans("Password must have be a combination of numeric, alphabet and special characters."));
				return Redirect::back()->withErrors($errors)->withInput();
			}
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new ServiceProvider;
				$obj->handyman_id						=	$request->input('handyman_id');
				$obj->first_name 						=  $request->input('first_name');
				$obj->last_name 						=  $request->input('last_name');
				$obj->name 								=  $obj->first_name.' '.$obj->last_name;
				$obj->email 							=  $request->input('email');
                $obj->phone_number 						=  $request->input('phone_number');
                $obj->gender 			    			=  $request->input('gender');
                $obj->dob 		        				=  (!empty($request->input('dob'))) ? date("Y-m-d",strtotime($request->input('dob'))) : "";
                $obj->type_of_document 		  			=  $request->input('type_of_document');
                $obj->password	 						=  Hash::make($request->input('password'));
                
                if($request->hasFile('document_front')){
					$extension 	=	 $request->file('document_front')->getClientOriginalExtension();
					$fileName	=	time().'-document_front.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	SERVICE_PROVIDER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('document_front')->move($folderPath, $fileName)){
						$obj->document_front	=	$folderName.$fileName;
					}
                }
                
                if($request->hasFile('document_back')){
					$extension 	=	 $request->file('document_back')->getClientOriginalExtension();
					$fileName	=	time().'-document_back.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	SERVICE_PROVIDER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('document_back')->move($folderPath, $fileName)){
						$obj->document_back	=	$folderName.$fileName;
					}
                }
                
				$obj->save();
				$serviceproviderId					=	$obj->id;

				$companydetails 									=  new ServiceProviderCompanyDetail;
                $companydetails->service_provider_id 				=  $serviceproviderId;
                $companydetails->company_name 						=  $request->input('company_name');
                $companydetails->chamber_of_commerce_no 			=  $request->input('chamber_of_commerce_no');
                $companydetails->tax_number 						=  $request->input('tax_number');
                $companydetails->insured 						    =  $request->input('insured');
				$companydetails->house_number 						=  $request->input('house_number');
				$companydetails->street_name 						=  $request->input('street_name');
				$companydetails->near_by 							=  $request->input('near_by');
				$companydetails->zip_code 							=  $request->input('zip_code');
				$companydetails->city 								=  $request->input('city');
                $companydetails->country 							=  $request->input('country');
                $companydetails->radius_range 						=  $request->input('radius_range');
                $companydetails->save();
                
                $BankDetail                             =   new ServiceProviderBankDetail;
                $BankDetail->bank_name					=  $request->input('bank_name');
				$BankDetail->ifsc_code 					=  $request->input('ifsc_code');
				$BankDetail->iban_number 				=  $request->input('iban_number');
                $BankDetail->account_number 			=  $request->input('account_number');
                $BankDetail->service_provider_id 		=  $serviceproviderId;
                $BankDetail->save();

                $serviceDetail                              =   new ServiceProviderService;
                $serviceDetail->service_provider_id 		=  $serviceproviderId;
                $serviceDetail->category_id                 =  $request->input('category_id');
                $serviceDetail->save();

				if(!$serviceproviderId){
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
	* @param $modelId as id of customer 
	* @param $status as status of customer 
	*
	* @return redirect page. 
	*/	
	public function changeStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been deactivated successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been activated successfully");
		}
		$this->_update_all_status('service_providers',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
    }// end changeStatus()
    

    /**
	* Function for update status
	*
	* @param $modelId as id of customer 
	* @param $status as status of customer 
	*
	* @return redirect page. 
	*/	
	public function changeVerificationStatus($modelId = 0, $status = 0){ 
		if($status == 0){
			$statusMessage	=	trans($this->sectionNameSingular." has been pending successfully");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." has been verified successfully");
		}
		ServiceProvider::where('id',$modelId)->update(array('is_verified'=>$status));
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
	}// end changeVerificationStatus()
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($serviceProviderId = 0,Request $request){
		$model					=	ServiceProvider::findorFail($serviceProviderId);
		if(empty($model)) {
			return Redirect::back();
		}
        $companyDetails =  ServiceProviderCompanyDetail::where('service_provider_id',$serviceProviderId)->leftjoin('countries','service_provider_companies.country','countries.id')->select("service_provider_companies.*",'countries.country_name')->first();
        $bankData		=	ServiceProviderBankDetail::where('service_provider_id',$serviceProviderId)->first();							
        $serviceData	=	ServiceProviderService::where('service_provider_id',$serviceProviderId)->value("category_id");						
		$countries	=	DB::table("countries")->where('is_active',1)->where('is_deleted',0)->pluck("country_name","id")->toArray();
        $Lookup	=	new Lookup();
        $identity_types	=	$Lookup->getActiveIdentityTypeList();
        $categories = array();
        $model->dob 			=  (!empty($model->dob)) ? date("m/d/Y",strtotime($model->dob)) : "";
		return  View::make("admin.$this->model.edit",compact('model','countries','companyDetails','categories','identity_types','bankData','serviceData'));
	} // end edit()
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	ServiceProvider::findorFail($modelId);
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
                    'handyman_id'				=> "required|unique:service_providers,handyman_id,$modelId",
                    'gender'					=> 'required',
                    'dob'   					=> 'required',
                    'company_name'				=> 'required',
                    'chamber_of_commerce_no'	=> 'required',
                    'tax_number'            	=> 'required',
                    'insured'               	=> 'required',
                    'category_id'            	=> 'required',
                    'radius_range'            	=> 'required',
                    'type_of_document'        	=> 'required',
                    'document_front'         	=> 'nullable|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
                    'document_back'             => 'nullable|mimes:'.IMAGE_EXTENSION_DOCUMENTS,
					'email' 					=> "required|email|unique:service_providers,email,$modelId",
					'phone_number' 				=> "required|numeric|unique:service_providers,phone_number,$modelId",
					'house_number'				=> 'required',
					'street_name'				=> 'required',
					'near_by'					=> 'required',
					'zip_code'					=> 'required',
					'city'						=> 'required',
					'country'					=> 'required',
				),
				array(
					"first_name.required"			    =>	trans("The first name field is required."),
                    "last_name.required"			    =>	trans("The last name field is required."),
                    "handyman_id.required"		        =>	trans("The handyman ID field is required."),
                    "gender.required"			        =>	trans("The gender field is required."),
                    "dob.required"				        =>	trans("The date of birth field is required."),
                    "company_name.required"		        =>	trans("The company name field is required."),
                    "chamber_of_commerce_no.required"	=>	trans("The chamber of commerce number field is required."),
                    "tax_number.required"				=>	trans("The tax number field is required."),
                    "insured.required"			    	=>	trans("The insured field is required."),
                    "category_id.required"				=>	trans("The service interest field is required."),
                    "radius_range.required"				=>	trans("The radius range field is required."),
                    "type_of_document.required"			=>	trans("The type of document field is required."),
                    "document_front.mimes"				=>	trans("The document front must be a file of type: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel'."),
                    "document_back.mimes"				=>	trans("The document back must be a file of type: 'jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel'."),
					"email.required"			        =>	trans("The email field is required."),
					"email.email"				        =>	trans("The email must be a valid email address."),
					"email.unique"				        =>	trans("The email has already been taken."),
					"house_number.required"		        =>	trans("The house number field is required."),
					"street_name.required"		        =>	trans("The street name field is required."),
					"near_by.required"			        =>	trans("The near by field is required."),
					"zip_code.required"			        =>	trans("The zip code field is required."),
					"city.required"				        =>	trans("The city field is r	equired."),
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
				$obj->handyman_id						=  $request->input('handyman_id');
				$obj->first_name 						=  $request->input('first_name');
				$obj->last_name 						=  $request->input('last_name');
				$obj->name 								=  $obj->first_name.' '.$obj->last_name;
				$obj->email 							=  $request->input('email');
                $obj->phone_number 						=  $request->input('phone_number');
                $obj->gender 			    			=  $request->input('gender');
                $obj->dob 		        				=  (!empty($request->input('dob'))) ? date("Y-m-d",strtotime($request->input('dob'))) : "";
                $obj->type_of_document 		  			=  $request->input('type_of_document');
                $obj->password	 						=  Hash::make($request->input('password'));
                
                if($request->hasFile('document_front')){
					$extension 	=	 $request->file('document_front')->getClientOriginalExtension();
					$fileName	=	time().'-document_front.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	SERVICE_PROVIDER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('document_front')->move($folderPath, $fileName)){
						$obj->document_front	=	$folderName.$fileName;
					}
                }
                
                if($request->hasFile('document_back')){
					$extension 	=	 $request->file('document_back')->getClientOriginalExtension();
					$fileName	=	time().'-document_back.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	SERVICE_PROVIDER_IMAGE_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					if($request->file('document_back')->move($folderPath, $fileName)){
						$obj->document_back	=	$folderName.$fileName;
					}
                }
                
				$obj->save();
				$serviceproviderId					=	$obj->id;

				$companydetails 									=   ServiceProviderCompanyDetail::where('service_provider_id','=',$serviceproviderId)->first();
                $companydetails->company_name 						=  $request->input('company_name');
                $companydetails->chamber_of_commerce_no 			=  $request->input('chamber_of_commerce_no');
                $companydetails->tax_number 						=  $request->input('tax_number');
                $companydetails->insured 						    =  $request->input('insured');
				$companydetails->house_number 						=  $request->input('house_number');
				$companydetails->street_name 						=  $request->input('street_name');
				$companydetails->near_by 							=  $request->input('near_by');
				$companydetails->zip_code 							=  $request->input('zip_code');
				$companydetails->city 								=  $request->input('city');
                $companydetails->country 							=  $request->input('country');
                $companydetails->radius_range 						=  $request->input('radius_range');
                $companydetails->save();
                
                $BankDetail                             =    ServiceProviderBankDetail::where('service_provider_id','=',$serviceproviderId)->first();
                $BankDetail->bank_name					=  $request->input('bank_name');
				$BankDetail->ifsc_code 					=  $request->input('ifsc_code');
				$BankDetail->iban_number 				=  $request->input('iban_number');
                $BankDetail->account_number 			=  $request->input('account_number');
                $BankDetail->save();

                $serviceDetail                              =   ServiceProviderService::where("service_provider_id",$serviceproviderId)->first();
				if(empty($serviceDetail)){
					$serviceDetail	=	new ServiceProviderService;
				}
                $serviceDetail->service_provider_id 		=  $serviceproviderId;
                $serviceDetail->category_id                 =  $request->input('category_id');
                $serviceDetail->save();

				if(!$serviceproviderId){
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
		$userDetails	=	ServiceProvider::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			$email 			=	'delete_'.$userId.'_'.$userDetails->email;		
			$phone_number 		=	'delete_'.$userId.'_'.$userDetails->phone_number;
			ServiceProvider::where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'phone_number'=>$phone_number));
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
	
	public function getServicesByCountry(Request $request){
		$countryid	=	$request->input("countryid");
		$selctedid	=	$request->input("selctedid");
		$categories = DB::table('categories')->where("country_id",$countryid)->where("is_active",1)->where("is_deleted",0)->get()->toArray();
		return  View::make("admin.$this->model.add_more_services",compact('categories','selctedid'));
	}

}// end BrandsController
