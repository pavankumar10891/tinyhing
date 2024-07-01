<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Price;
use App\Model\Country;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* PriceController Controller
*
* Add your methods in the class below
*
*/
class PriceController extends BaseController {

	public $modelName		=	'price';
	public $sectionName	=	'Prices';
	public $sectionNameSingular	=	'Price';
	
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
		$DB					=	Price::leftJoin('countries','prices.country_id','countries.id')->select('prices.*','countries.country_name');
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
           
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "price"){
						$DB->where("price",$fieldValue);
					}
					if($fieldName == "country_id"){
						$DB->where("country_id",$fieldValue);
					}
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		if(!empty($request->input('per_page'))){
			$searchVariable["per_page"]	=	$records_per_page;
		}
		$sortBy             =   ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order              =   ($request->input('order')) ? $request->input('order')   : 'DESC';
		$results            =   $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string	=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string		=	http_build_query($complete_string);
		$results->appends($request->all())->render();
        $countries=Country::where('is_active',1)->where('is_deleted',0)->pluck('country_name','id')->toArray();

		return  View::make("admin.$this->modelName.index",compact('results','searchVariable','sortBy','order','query_string','countries'));
	}// end index()

	/**
	* Function for add new coupon
	*
	* @param null
	*
	* @return view page. 
	*/

	public function add(){  
        $countries=Country::where('is_active',1)->where('is_deleted',0)->pluck('country_name','id')->toArray();
		return  View::make("admin.$this->modelName.add",compact('countries'));
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
					'country'						=> 'required',
					// 'state'						=> 'required',
					'city'							=> 'required',
					'price'						=> 'required|numeric|gt:0',
					'tax'     						    => 'required|numeric',					
				),
				array(
					"country.required"				=>	trans("The country field is required."),
					"state.required"	        	=>	trans("The state field is required."),
					"city.required"				    =>	trans("The city field is required."),
					"price.required"				=>	trans("The price field is required."),
					"price.numeric"					=>	trans("The price should be numeric."),
					"price.gt"				    	=>	trans("The price should be greater than 0."),
					"tax.required"				    =>	trans("The tax field is required."),	
					"tax.numeric"				    =>	trans("The tax should be numeric."),	

				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 									=  new Price;
				$obj->country_id 						=  $request->input('country');
                $obj->state      						=  $request->input('state');
				$obj->city 						        =  $request->input('city');
				$obj->price						        =  $request->input('price');
				$obj->tax						    	=  $request->input('tax');
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
		$model		=	Price::where('id',$couponId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
        $countries=Country::where('is_active',1)->where('is_deleted',0)->pluck('country_name','id')->toArray();
	 	return View::make("admin.$this->modelName.edit",compact('model','countries'));
	} // end edit()


	/**
	* Function for update customer 
	*
	* @param $modelId as id of customer 
	*
	* @return redirect page. 
	*/
	function update($priceId,Request $request){
		$model					=	Price::findorFail($priceId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'country'						=> 'required',
					// 'state'						=> 'required',
					'city'							=> 'required',
					'price'						=> 'required|numeric|gt:0',
					'tax'     						    => 'required|numeric',					
				),
				array(
					"country.required"				=>	trans("The country field is required."),
					"state.required"	        	=>	trans("The state field is required."),
					"city.required"				    =>	trans("The city field is required."),
					"price.required"				=>	trans("The price field is required."),
					"price.numeric"					=>	trans("The price should be numeric."),
					"price.gt"			    		=>	trans("The price should be greater than 0."),
					"tax.required"			    	=>	trans("The tax field is required."),	
					"tax.numeric"				    =>	trans("The tax should be numeric."),	

				)

			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 						            =  Price::find($priceId);
				$obj->country_id 						=  $request->input('country');
                $obj->state      						=  $request->input('state');
				$obj->city 						        =  $request->input('city');
				$obj->price						        =  $request->input('price');
				$obj->tax							=  $request->input('tax');
				$obj->save();
				$price_id						=	$obj->id;
				if(!$price_id){
					Session::flash('error', trans("Something went wrong. please try again")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->modelName.".index");
			}
		}
	}// end update()


   
    /**
	* Function for delete coupon
	*
	* @param $couponId as id of coupon 
	* @param $modelStatus as status of coupon 
	*
	* @return redirect page. 
	*/	

	 public function delete($couponId = 0){
		$UserDetails = Price::find($couponId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($couponId){
			Price::where('id',$couponId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()


  
}// End Coupon Code COntroller
