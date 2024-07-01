<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\CountryVat;
use App\Model\CountryLanguage;
use App\Model\Country;
use App\Model\Lookup;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* CountriesController Controller
*
* Add your methods in the class below
*
*/
class CountriesController extends BaseController {

	public $model		=	'Countries';
	public $sectionName	=	'Countries';
	public $sectionNameSingular	=	'Country';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	 
	/**
	* Function for display all Users 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function index(Request $request){  
		$DB					=	Country::query();
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
				$DB->whereBetween('countries.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('countries.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('countries.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "country_name"){
						$DB->where("countries.country_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "iso_code"){
						$DB->where("countries.iso_code",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("countries.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("countries.is_deleted",0);
		$DB->leftjoin('languages','countries.language_name','languages.id');
        $DB->select("countries.*",'languages.title as language_name');
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'countries.created_at';
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
		$languages = DB::table('languages')->where('is_active',1)->pluck("title","id")->toArray();
        $vat_type =  Lookup::where('lookup_type',"vat_type")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		return  View::make("admin.$this->model.add",compact('vat_type','languages'));
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
					'country_name'				=> 'required',
					'iso_code'					=> 'required',
					'currency_name'				=> 'required',
					'currency_code'				=> 'required',
					'language_name'				=> 'required',
					'languages'				=> 'required',
					'dial_code'					=> 'required',
				),
				array(
                    "country_name.required"		=>	trans("The country name field is required."),
					"iso_code.required"			=>	trans("The iso code field is required."),
					"currency_name.required"	=>	trans("The currency name field is required."),
					"currency_code.required"	=>	trans("The currency code field is required."),
					"language_name.required"	=>	trans("The language name field is required."),
					"languages.required"	=>	trans("The listed language field is required."),
					"language_code.required"	=>	trans("The language code field is required."),
					"dial_code.required"		=>	trans("The dial code field is r	equired."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new Country;
				$obj->country_name 						=  $request->input('country_name');
				$obj->iso_code 					    	=  $request->input('iso_code');
				$obj->currency_name 					=  $request->input('currency_name');
                $obj->currency_code 					=  $request->input('currency_code');
                $obj->language_name 					=  $request->input('language_name');
                $obj->dial_code 					=  $request->input('dial_code');
				$obj->save();
				$userId					=	$obj->id;

				if(!empty($formData['item_data'])){
					foreach($formData['item_data'] as $key => $value) {
						if(!empty($value)){
							$VatOption 							= 	new CountryVat;	
							$VatOption->country_id				=	$userId;
							$VatOption->vat			    		=	$value["vat"];
							$VatOption->vat_type				= 	$value["vat_type"];	
							
							$VatOption->save();	
						}
					}
				}

				foreach ($formData['languages'] as $key ) {
					$county_languages_obj					=  new CountryLanguage();
					$county_languages_obj->country_id		=	$userId;
					$county_languages_obj->language_id		=	$key ;	
					$county_languages_obj->save();
				}  


				
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}

				Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				return Redirect::route($this->model.".index");
			}
		}
	}//end save()
	
	public function vataddMoreDetailRow(Request $request){
        $request->replace($this->arrayStripTags($request->all()));
        $thisData						=	$request->all();
       
        $count = $thisData['id'];
		$vat_type =  Lookup::where('lookup_type',"vat_type")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		
        return View::make('admin.Countries.add_more_vat_detail_row', compact('count','vat_type'));
    }
	
	public function deleteItem(Request $request){
		$modelId  = $request->get('id'); 
		$delete_item = CountryVat::where('id',$modelId)->delete();
		return response()->json($delete_item);
	 }


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
		$this->_update_all_status('countries',$modelId,$status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::back();
    }// end changeStatus()
	
	
	
	/**
	* Function for display page for edit customer
	*
	* @param $modelId id  of customer
	*
	* @return view page. 
	*/
	public function edit($modelId = 0,Request $request){
		$model					=	Country::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		$VatData = DB::table('country_vats')->where('country_id',$modelId)->get()->toArray();					
		//$VatData = json_decode($Data, true);
		$languages = DB::table('languages')->where('is_active',1)->pluck("title","id")->toArray();
		$countryLanguagues  = CountryLanguage::where('country_id',$modelId)->pluck("language_id")->toArray();
		$vat_type =  Lookup::where('lookup_type',"vat_type")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		return  View::make("admin.$this->model.edit",compact('model','vat_type','VatData','countryLanguagues','languages'));
	} // end edit()
	
	
	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($modelId,Request $request){
		$model					=	Country::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'country_name'				=> 'required',
					'iso_code'					=> 'required',
					'currency_name'				=> 'required',
					'currency_code'				=> 'required',
					'language_name'				=> 'required',
					'languages'				=> 'required',
					'dial_code'					=> 'required',
				),
				array(
                    "country_name.required"		=>	trans("The country name field is required."),
					"iso_code.required"			=>	trans("The iso code field is required."),
					"currency_name.required"	=>	trans("The currency name field is required."),
					"currency_code.required"	=>	trans("The currency code field is required."),
					"language_name.required"	=>	trans("The language name field is required."),
					"language_code.required"	=>	trans("The language code field is required."),
					"dial_code.required"		=>	trans("The dial code field is required."),
					"languages.required"	=>	trans("The listed language field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  $model;
				$obj->country_name 						=  $request->input('country_name');
				$obj->iso_code 					    	=  $request->input('iso_code');
				$obj->currency_name 					=  $request->input('currency_name');
                $obj->currency_code 					=  $request->input('currency_code');
                $obj->language_name 					=  $request->input('language_name');
                $obj->dial_code 					=  $request->input('dial_code');
				$obj->save();
				$userId					=	$obj->id;

				
				//CountryVat::where("country_id",$modelId)->delete();
				
				$ids=	array();
				if(!empty($formData['item_data'])){
					foreach($formData['item_data'] as $value) {
						if(!empty($value)){
							if(!empty($value["id"])){
								$CountryVat	=	CountryVat::where("id",$value["id"])->first();
								
								$VatOption 							= 	$CountryVat;	
								$VatOption->country_id				=	$userId;
								$VatOption->vat			    		=	$value["vat"];
								$VatOption->vat_type				= 	$value["vat_type"];	
								
								$VatOption->save();	
								$ids[]	=	$VatOption->id;
							}else {
								$VatOption 							= 	new CountryVat;	
								$VatOption->country_id				=	$userId;
								$VatOption->vat			    		=	$value["vat"];
								$VatOption->vat_type				= 	$value["vat_type"];	
								
								$VatOption->save();	
								$ids[]	=	$VatOption->id;
							}
							
							
						}
					}	
					CountryVat::whereNotIn("id",$ids)->where("country_id",$modelId)->delete();
				}

				CountryLanguage::where("country_id",$modelId)->delete();
				foreach ($formData['languages'] as $key ) {
					$county_languages_obj					=  new CountryLanguage();
					$county_languages_obj->country_id		=	$userId;
					$county_languages_obj->language_id		=	$key ;	
					$county_languages_obj->save();
				}  

				if(!$userId){
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
		$userDetails	=	Country::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($userId){
			Country::where('id',$userId)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()

	public function view($modelId = 0){
		$model	=	Country::where('id',"$modelId")
							->select('countries.*')->first();
		if(empty($model)) {
			return Redirect::route($this->model.".index");
		}
		$countryVat = DB::table('country_vats')->where('country_id',$modelId)
											   ->leftJoin ('lookups as vat-type', 'country_vats.vat_type', '=', 'vat-type.id')
											   ->select("country_vats.*",'vat-type.code as vatType')
											   ->get();	
		//echo "<pre>"; print_r($countryVat); die;											   				
		return  View::make("admin.$this->model.view",compact('model','countryVat'));
	} // end view()
	

}// end BrandsController
