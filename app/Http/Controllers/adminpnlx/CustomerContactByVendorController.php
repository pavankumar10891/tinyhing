<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Model\Customer;
use App\Imports\VendorImport;
use App\Model\VendorRebate;
use App\Exports\ExportCustomerContactByVendor;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;

/**
* CustomerContactByVendorController Controller
*
* Add your methods in the class below
*
*/
class CustomerContactByVendorController extends BaseController {

	public $model				=	'CustomerContactByVendor';
	public $sectionName			=	'Customer Contact By Vendor';
	public $sectionNameSingular	=	'Customer Contact By Vendor';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}

	public function ExportForm(){
		$vendorDetails 		=  	DB::table("vendors")
								->where('is_active', 1)
								->where('deleted_at', Null)
								->pluck('corporate_name', 'id')
								->toArray();
		return  View::make("admin.$this->model.exportform", compact('vendorDetails'));
	}//end function

	public function export(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor_id'		=> 'required',
					'start_date'	=> 'required',
					'end_date'		=> 'required',
				),
				array(
					"vendor_id.required"	=>	trans("The vendor is required."),
					"start_date.required"	=>	trans("The start date is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$startDate 	= 	date('Y-m-d', strtotime("01-".$request->get('start_date')));
				$startDate 	= 	date("Y-m-t", strtotime($startDate));
				$endDate 	= 	date('Y-m-d', strtotime("01-".$request->get('end_date')));
				$endDate 	= 	date("Y-m-t", strtotime($endDate));
				$data 		= 	array('start_date' => $startDate ,'end_date' => $endDate, 'vendor_id' => $request->vendor_id); 
				return Excel::download(new ExportCustomerContactByVendor($data), 'customer-contact-by-vendor-'.time().'.xlsx');
			}
		}
	}//end function 
	  
}// end NoneComplanceController

