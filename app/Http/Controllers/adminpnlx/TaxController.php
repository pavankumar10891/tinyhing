<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Tax;
use App\Model\Country;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator;

/**
* TaxController Controller
*
* Add your methods in the class below
*
*/
class TaxController extends BaseController {

	public $modelName		=	'tax';
	public $sectionName	=	'Taxes';
	public $sectionNameSingular	=	'Tax';
	
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
		$DB					=	Tax::select('taxes.*');
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
					if($fieldName == "city"){
						$DB->where("city",$fieldValue);
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

		return  View::make("admin.$this->modelName.index",compact('results','searchVariable','sortBy','order','query_string'));
	}// end index()

	/**
	* Function for add new tax
	*
	* @param null
	*
	* @return view page. 
	*/

	public function add(){  
		return  View::make("admin.$this->modelName.add");
	}// end add()
	


    /**
    * Function for save new tax
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
    				'city'							=> 'required',
    				'tax'     						=> 'required|numeric',					
    			),
    			array(
    				"city.required"				    =>	trans("The city field is required."),
    				"tax.required"				    =>	trans("The tax field is required."),	
    				"tax.numeric"				    =>	trans("The tax should be numeric."),	

    			)
    		);
    		if ($validator->fails()){
    			return Redirect::back()->withErrors($validator)->withInput();
    		}else{ 
    			$obj 									=  new Tax;
    			$obj->city 						        =  $request->input('city');
    			$obj->tax						    	=  $request->input('tax');
    			$obj->save();
    			$tax_id									=	$obj->id;
    			if(!$tax_id){
    				Session::flash('error', trans("Something went wrong.please try again")); 
    				return Redirect::back()->withInput();
    			}
    			Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
    			return Redirect::route($this->modelName.".index");
    		}
    	}
	}//end save()


	/**
	* Function for edit  tax 
	*
	* @param $couponId as id of tax 
	*
	* @return redirect page. 
	*/

	public function edit($taxId = 0,Request $request){
		$model		=	Tax::where('id',$taxId)->first();
		if(empty($model)) {
			return Redirect::back();
		}
		return View::make("admin.$this->modelName.edit",compact('model'));
	} // end edit()


	/**
	* Function for update tax 
	*
	* @param $modelId as id of tax 
	*
	* @return redirect page. 
	*/
	function update($taxId,Request $request){
		$model					=	Tax::findorFail($taxId);
		if(empty($model)) {
			return Redirect::back();
		}
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'city'							=> 'required',
					'tax'     						=> 'required|numeric',					
				),
				array(
					"city.required"				    =>	trans("The city field is required."),
					"tax.required"			    	=>	trans("The tax field is required."),	
					"tax.numeric"				    =>	trans("The tax should be numeric."),	

				)

			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$obj 						            =  Tax::find($taxId);
				$obj->city 						        =  $request->input('city');
				$obj->tax								=  $request->input('tax');
				$obj->save();
				$tax_id									=	$obj->id;
				if(!$tax_id){
					Session::flash('error', trans("Something went wrong. please try again")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
				return Redirect::route($this->modelName.".index");
			}
		}
	}// end update()



    /**
	* Function for delete tax
	*
	* @param $taxId as id of tax 
	* @param $modelStatus as status of tax 
	*
	* @return redirect page. 
	*/	

	public function delete($taxId = 0){
		$UserDetails = Tax::find($taxId);
		if(empty($UserDetails)) {
			return Redirect::route($this->model.".index");
		}
		if($taxId){
			Tax::where('id',$taxId)->delete();
			Session::flash('flash_notice',trans($this->sectionNameSingular." has been removed successfully")); 
		}
		return Redirect::back();
	}// end delete()



}// End Tax Controller
