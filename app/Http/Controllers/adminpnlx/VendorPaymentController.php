<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Vendor;
use App\Model\VendorPayments;
use Illuminate\Http\Request;
use App\Exports\VendorsExport;
use App\Model\InternalNotes;
use App\Model\VendorExportPayment;
use App\Model\GeneralNotes;
use App\Imports\VendorImport;
use App\Exports\ImportDataExport;
use App\Imports\ImportTemplateExcel;
use App\Model\Group;
use App\Model\ImportVendorChecks;
use Auth,Storage,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,Redirect,Response,Session,URL,View,Validator,Excel;
use PDF;
use CustomHelper;
use App\Model\ImportManufacturerRebateDetails;
use App\Model\ImportManufacturerRebate;

/**
* VendorPaymentController Controller
*
* Add your methods in the class below
*
*/
class VendorPaymentController extends BaseController {

	public $model		=	'VendorPayments';
	public $sectionName	=	'Vendor Payments';
	public $sectionNameSingular	=	'Vendor Payment';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}



	public function index(Request $request){  
		 
		$DB					=	VendorPayments::query();
		$searchVariable		=	array(); 
		$inputGet			=	$request->all();
		$DB->leftJoin('vendors', 'vendors.id', '=', 'vendors_payment.vendor_id')->select('vendors_payment.*', 'vendors.business_name');


		//export item by seach namevendors
		$searchExportItem = array();

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
			if((!empty($searchData['t_start_date'])) && (!empty($searchData['t_enddate_to']))){
				$dateS = $searchData['t_start_date'];
				$dateE = $searchData['t_enddate_to'];
				$searchExportItem['t_start_date']  = $dateS;
				$searchExportItem['t_enddate_to'] 	= $dateE;
				$DB->whereBetween('vendors_payment.transaction_date', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['t_start_date'])){
				$dateS = $searchData['t_start_date'];
				$searchExportItem['t_start_date'] = $dateS;
				$DB->where('vendors_payment.transaction_date','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['t_enddate_to'])){
				$dateE = $searchData['t_enddate_to'];
				$searchExportItem['t_enddate_to'] = $dateE;
				$DB->where('vendors_payment.transaction_date','<=' ,[$dateE." 00:00:00"]); 						
			}elseif((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$searchExportItem['date_from']  = $dateS;
				$searchExportItem['date_to'] 	= $dateE;
				$DB->whereBetween('vendors_payment.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$searchExportItem['date_from'] = $dateS;
				$DB->where('vendors_payment.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$searchExportItem['date_to'] = $dateE;
				$DB->where('vendors_payment.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "vendor"){
						$searchExportItem['vendor'] = $fieldValue;
						$DB->where("vendors_payment.vendor_id",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "customer_name"){
						$searchExportItem['customer_name'] = $fieldValue;
						$DB->where("vendors_payment.cus_name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "check_number"){
						$searchExportItem['check_number'] = $fieldValue;
						$DB->where("vendors_payment.check_number",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			$DB->where("vendors_payment.is_deleted",0);
		}else{
		   $DB->where("vendors_payment.is_deleted",0)->where('vendors_payment.group_id', Session::get('group'));
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		// To export all to PDF as well as Excel
	
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$groups =  Group::where('deleted_at', NULL)->pluck('name', 'id')->toArray();
		$vendors =   Vendor::where('group_id', Session::get('group'))->where('is_active', 1)->where('deleted_at', Null)->pluck('business_name', 'id')->toArray();
	
		return  View::make("admin.$this->model.index",compact('groups','vendors','results','searchVariable','sortBy','order','query_string'));
	}// end index()
	
    public function add()
	{
		$vendors  = Vendor::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();
		return  View::make("admin.$this->model.add",compact('vendors'));
	}

	function save(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'				=> 'required',
					'payment_deposit_date'	=> 'required',
					'check_number'			=> 'required',
					'check_amount'			=> 'required',
				),
				array(
					"vendor.required"					=>	trans("The vendor field is required."),
					"payment_deposit_date.required"		=>	trans("The payment deposit field is required."),
					"check_number.required"				=>	trans("The check number field is required."),
					"check_amount.required"				=>	trans("The check amount field is required."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				// $obj 							=  new VendorPayments;
				// $obj->vendor_id 				=  $request->input('vendor');
				// $obj->payment_deposit_date 		=  $request->input('payment_deposit_date');
				// $obj->check_number 				=  $request->input('check_number');
				// $obj->check_amount 				=  $request->input('check_amount');
				// $obj->admin_id					=  Auth::guard('admin')->user()->id;
				// $obj->group_id					=  !empty(Session::get('group')) ? Session::get('group'):0;
				// $obj->save();
				// $userId							= $obj->id;
		
				// if(!$userId){
				// 	Session::flash('error', trans("Something went wrong.")); 
				// 	return Redirect::back()->withInput();
				// }
				

				// Session::flash('success',trans($this->sectionNameSingular." has been added successfully"));
				// return Redirect::route($this->model.".index");
				
			}
		}
	}

	public function edit($modelId = 0,Request $request){
		$model					=	VendorPayments::where('admin_id', auth()->guard('admin')->user()->id)->where('id',$modelId)->first();

		

		if(empty($model)){
			return Redirect::back();
		}
		if($model->group_id != Session::get('group')){
			return Redirect::route($this->model.".index");
		}

		$vendors  = Vendor::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();
		return  View::make("admin.$this->model.edit",compact('model','vendors'));
	} // end edit()


	function update($modelId,Request $request){
		$model					=	VendorPayments::findorFail($modelId);
		if(empty($model)) {
			return Redirect::back();
		}
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'				=> 'required',
					'payment_deposit_date'	=> 'required',
					'check_number'			=> 'required',
					'check_amount'			=> 'required',
				),
				array(
					"vendor.required"					=>	trans("The vendor field is required."),
					"payment_deposit_date.required"		=>	trans("The payment deposit field is required."),
					"check_number.required"				=>	trans("The check number field is required."),
					"check_amount.required"				=>	trans("The check amount field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 							=  $model;
				$obj->vendor_id 				=  $request->input('vendor');
				$obj->payment_deposit_date 		=  $request->input('payment_deposit_date');
				$obj->check_number 				=  $request->input('check_number');
				$obj->check_amount 				=  $request->input('check_amount');
				$obj->admin_id					=  Auth::guard('admin')->user()->id;
				$obj->group_id					=  !empty(Session::get('group')) ? Session::get('group'):0;
				$obj->save();
				$userId					=	$obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				Session::flash('success',trans($this->sectionNameSingular." has been updated successfully"));
					return Redirect::route($this->model.".index");
			}
		}
	}// end update()

	public function view($modelId = 0){
		if(auth()->guard('admin')->user()->user_role == "super_admin") {
			  $model	=	VendorPayments::where('id',"$modelId")->first();
		 }else{
			 $model	=	VendorPayments::where('id',"$modelId")->where('group_id',Session::get('group'))->first();
		 }
		 $vendor = Vendor::where('id',$model->vendor_id)->first();
		 return  View::make("admin.$this->model.view",compact('model','vendor'));
	 } // end view()


	public function vendotpaymentregister()
	{
		
		$vendors  = Vendor::orderBy('id', 'asc')->where('is_active', 1)
		->where('group_id', Session::get('group'))
		->where('is_active', 1)
		->where("deleted_at", NULL)
		->pluck("business_name","id")
		->toArray();
		$exportpfs= VendorExportPayment::where('type', 1)->get();
		return  View::make("admin.$this->model.register",compact('vendors', 'exportpfs'));
	}

	function exportRegister(Request $request){
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'svendor'						=> 'required',
					'evendor'						=> 'required',
					'payment_deposit_start_date'	=> 'required',
					'payment_deposit_end_date'		=> 'required',
				),
				array(
					"svendor.required"						=>	trans("The start vendor field is required."),
					"evendor.required"						=>	trans("The end vendor field is required."),
					"payment_deposit_start_date.required"	=>	trans("The payment deposit start date field is required."),
					"payment_deposit_end_date.required"		=>	trans("The payment deposit end date field is required."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				
				$vendorpayments	=  VendorPayments::leftJoin('vendors', 'vendors.id', 'vendors_payment.vendor_id')
				->whereBetween('vendor_id', [$request->input('svendor'), $request->input('evendor')])
				->whereBetween('payment_deposit_date', [$request->input('payment_deposit_start_date'), $request->input('payment_deposit_end_date')])
				->select('vendors_payment.*', 'vendors.business_name as vendor_name')
				->get();

				$vendorDates = VendorPayments::select('payment_deposit_date')
				->whereBetween('payment_deposit_date', [$request->input('payment_deposit_start_date'), $request
				->input('payment_deposit_end_date')])
				->groupBy('payment_deposit_date')->get();

				//echo "<pre>";
				$grandArray = array();
				$grandtotal = 0;
				$total = 0;
				$vendorNameArray = array();
				$first = '';
				$last = '';
				$firstId=0;
				$lastId =0;
				foreach($vendorDates as $datkey=>$dateValue){
					$vendorArray = array();	
					 $grandArray[$datkey]['deposite_date'] = $dateValue['payment_deposit_date'];
					$total = 0;
					$x= 0;
					$lenth = count($vendorpayments);
					foreach($vendorpayments as $key1=>$value1){
						$singleArray = array();
						if($value1->payment_deposit_date == $dateValue['payment_deposit_date']){

							if ($key1 === $x) {
								$first = $value1->vendor_name;
								$firstId = $value1->vendor_id;
							}
						
							if ($x === $lenth-1) {
								$last = $value1->vendor_name;
								$lastId = $value1->vendor_id;
							}
							$vendorNameArray = array('first' => $first, 'last' => $last, 'firstId' => $firstId, 'lastId' => $lastId);
							$singleArray['id'] = $value1->id;
							$singleArray['payment_deposit_date'] 	= $value1->payment_deposit_date;
							$singleArray['vendor_id'] 				= $value1->vendor_id;
							$singleArray['vendor_name'] 			= $value1->vendor_name;
							$singleArray['vendor_check'] 			= $value1->check_number;
							$singleArray['vendor_amount'] 			= $value1->check_amount;
							$total 									= $total + $singleArray['vendor_amount'];
							$grandtotal 							= $grandtotal + $value1->check_amount; 
							$vendorArray[] = $singleArray;
						  	
						}
						$x++;
					}
					$grandArray[$datkey]['total'] 				    = $total;
					$grandArray[$datkey]['vendors'] 				= $vendorArray;
					
			     }
				 
				 $startDate = date('mdY',strtotime($request->input('payment_deposit_start_date')));
				 $endDate =  date('mdY',strtotime($request->input('payment_deposit_end_date')));
				
				
				//$pdf->setOptions(['isPhpEnabled' => true,'isRemoteEnabled' => true]);

				

				$checkExisData = VendorExportPayment::where('deposit_start_date', $request->input('payment_deposit_start_date'))
				->where('deposit_start_date', $request->input('payment_deposit_start_date'))
				->where('vendor_start_id', $vendorNameArray['firstId'])
				->where('vendor_end_id', $vendorNameArray['lastId'])
				->where('type', 1)
				->first();
				 
				
				if(empty($checkExisData)){
					$filename = $vendorNameArray['first'].'_'.$vendorNameArray['last'].'_'.$startDate.'_'.$endDate.'_'.time().'.pdf';
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				    $folderPath = VENDOR_PAYMENT_REGISTER_ROOT_PATH.$folderName;
					$pdf = PDF::loadView("admin.$this->model.export",array('grandArray' => $grandArray, 'grandtotal' => $grandtotal));
				     
					if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$pdf->save($folderPath . '/' . $filename);
					// $pdf->output()->move($folderPath, $filename);
					
					$paymentExport 						=  new VendorExportPayment;
					$paymentExport->export_file 		= $folderName.$filename;
					$paymentExport->name 				= $filename;
					$paymentExport->deposit_start_date 	= $request->input('payment_deposit_start_date');
					$paymentExport->deposit_end_date	= $request->input('payment_deposit_end_date');
					$paymentExport->vendor_start_id		= $vendorNameArray['firstId'];
					$paymentExport->vendor_end_id		= $vendorNameArray['lastId'];
					$paymentExport->type				= 1;
					$paymentExport->save();
					//$pdf->output();
					//file_put_contents($pathsss,$pdf); 
					//Storage::disks('local')->put('uploads/vendor_payment_register/'.$filename, $pdf->output());
					
				}

				//return  View::make("admin.$this->model.export",compact('grandArray', 'grandtotal'));
				return Redirect::route('VendorPayments.register');
				
			}
		}
	}

	public function checkregisterdeletePdf()
	{
		

		$datas =  VendorExportPayment::where('type', 1)->get();
		
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			
			VendorExportPayment::where('type', 1)->where('id', $data->id)->delete();
		}

		return Redirect::route('VendorPayments.register');

	}


	public function unappliedPayment()
	{
		
		$vendors  = Vendor::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();
		$sectionNameSingular = 'Vendor Unapplied Payments';
		$exportpfs= VendorExportPayment::where('type', 2)->get();
		return  View::make("admin.$this->model.unappliedpayment",compact('vendors', 'sectionNameSingular', 'exportpfs'));
	}

	function exportUnappliedPayment(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'svendor'						=> 'required',
					'evendor'						=> 'required',
					'payment_deposit_start_date'	=> 'required',
					'payment_deposit_end_date'		=> 'required',
				),
				array(
					"svendor.required"						=>	trans("The start vendor field is required."),
					"evendor.required"						=>	trans("The end vendor field is required."),
					"payment_deposit_start_date.required"	=>	trans("The payment deposit start date field is required."),
					"payment_deposit_end_date.required"		=>	trans("The payment deposit end date field is required."),
				)
			);
			
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 

				$vendorpayments	=  VendorPayments::leftJoin('vendors', 'vendors.id', 'vendors_payment.vendor_id')
				->whereBetween('vendor_id', [$request->input('svendor'), $request->input('evendor')])
				->whereBetween('payment_deposit_date', [$request->input('payment_deposit_start_date'), $request->input('payment_deposit_end_date')])
				->where('vendors_payment.applied_to', 2)
				->select('vendors_payment.*', 'vendors.business_name as vendor_name')
				->get();

				//  echo "<pre>";
				//  print_r($vendorpayments);die;

				$vendorDates = VendorPayments::select('payment_deposit_date')
				->whereBetween('payment_deposit_date', [$request->input('payment_deposit_start_date'), $request
				->input('payment_deposit_end_date')])
				->where('vendors_payment.applied_to', 2)
				->groupBy('payment_deposit_date')->get();

				//echo "<pre>";
				$grandArray = array();
				$grandtotal = 0;
				$total = 0;
				$vendorNameArray = array();
				$first = '';
				$last = '';
				$firstId=$request->input('svendor');
				$lastId =$request->input('evendor');
				$startVendor = $this->getvendorById($request->input('svendor'));
				$endVendor = $this->getvendorById($request->input('evendor'));
				$vendorNameArray = array('first' =>$startVendor, 'last' => $endVendor, 'firstId' => $firstId, 'lastId' => $lastId);

				foreach($vendorDates as $datkey=>$dateValue){
					$sssArray = array();
					$vendorArray = array();	
					 $grandArray[$datkey]['deposite_date'] = $dateValue['payment_deposit_date'];
					$total = 0;
					$x= 0;
					$lenth = count($vendorpayments);
					
					foreach($vendorpayments as $key1=>$value1){
						

						if($value1->payment_deposit_date == $dateValue['payment_deposit_date']){
							if ($key1 === $x) {
								$first = $value1->vendor_name;
								$firstId = $value1->vendor_id;
							}
						
							if ($x === $lenth-1) {
								$last = $value1->vendor_name;
								$lastId = $value1->vendor_id;
							}
							$firstId=$request->input('svendor');
							$lastId =$request->input('evendor');
							$vendorNameArray = array('first' => $startVendor, 'last' => $endVendor, 'firstId' => $firstId, 'lastId' => $lastId);
							$singleArray['id'] = $value1->id;
							$singleArray['payment_deposit_date'] 	= $value1->payment_deposit_date;
							$singleArray['vendor_id'] 				= $value1->vendor_id;
							$singleArray['vendor_name'] 			= $value1->vendor_name;
							$singleArray['vendor_check'] 			= $value1->check_number;
							$singleArray['vendor_amount'] 			= $value1->check_amount;
							$total 									= $total + $singleArray['vendor_amount'];
							$grandtotal 							= $grandtotal + $value1->check_amount; 
							$vendorArray[] = $singleArray;
						}

						$x++;
					}
					$grandArray[$datkey]['total'] 				    = $total;
					$grandArray[$datkey]['vendors'] 				= $vendorArray;
					
			     }
				 $startDate = date('mdY',strtotime($request->input('payment_deposit_start_date')));
				 $endDate =  date('mdY',strtotime($request->input('payment_deposit_end_date')));
				//$grandArray[] = $vendorArray;
				//echo "<pre>";
			    //print_r($grandArray);die;
				//return  View::make("admin.$this->model.unappliedexport",compact('grandArray', 'grandtotal'));

				//\DB::table('vendor_payment_export')->
				
				//$pdf->setOptions(['isPhpEnabled' => true,'isRemoteEnabled' => true]);
				
				$checkExisData = VendorExportPayment::where('deposit_start_date', $request->input('payment_deposit_start_date'))
				->where('deposit_start_date', $request->input('payment_deposit_start_date'))
				->where('vendor_start_id', $vendorNameArray['firstId'])
				->where('vendor_end_id', $vendorNameArray['lastId'])
				->where('type', 2)
				->first();

				
				if(empty($checkExisData)){
					$filename = $vendorNameArray['first'].'_'.$vendorNameArray['last'].'_'.$startDate.'_'.$endDate.'.pdf';
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				    $folderPath = VENDOR_PAYMENT_UNAPPLIED_ROOT_PATH.$folderName;
					$pdf = PDF::loadView("admin.$this->model.unappliedexport",array('grandArray' => $grandArray, 'grandtotal' => $grandtotal));
				     
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$pdf->save($folderPath . '/' . $filename);

					$paymentExport 						=  new VendorExportPayment;
					$paymentExport->export_file 		= $folderName.$filename;
					$paymentExport->name 				= $filename;
					$paymentExport->deposit_start_date 	= $request->input('payment_deposit_start_date');
					$paymentExport->deposit_end_date	= $request->input('payment_deposit_end_date');
					$paymentExport->vendor_start_id		= $vendorNameArray['firstId'];
					$paymentExport->vendor_end_id		= $vendorNameArray['lastId'];
					$paymentExport->type				= 2;
					$paymentExport->save();
					//$pdf->save('generatepdf/'.$filename);
					Storage::put('public/pdf/'.$filename, $pdf->output());
					
				}
				return Redirect::route('VendorPayments.unapplied.payment');
	    		//return $pdf->setPaper('a4', 'portrait')->download($filename);
				 //echo "<pre>";
				 //print_r($pdf);die;


			}
		}
	}


	public function unapplieddeletePdf()
	{
		

		$datas =  VendorExportPayment::where('type', 2)->get();
		
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			
			VendorExportPayment::where('type', 2)->where('id', $data->id)->delete();
		}

		return Redirect::route('VendorPayments.unapplied.payment');

	}

	public function vendorPaymentReconciliation()
	{
		
		$vendors  = Vendor::orderBy('id', 'asc')->where('is_active', 1)->where('group_id', Session::get('group'))->where('is_active', 1)->where("deleted_at", NULL)->pluck("business_name","id")->toArray();
		$sectionNameSingular = 'Vendor Payment Reconciliation';
		$exportpfs= VendorExportPayment::where('type', 3)->get();
		
		return  View::make("admin.$this->model.reconciliation",compact('vendors', 'sectionNameSingular', 'exportpfs'));
	}

	public function exportvendorPaymentReconciliation(Request $request)
	{
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'svendor'						=> 'required',
					'evendor'						=> 'required',
					'payment_deposit_start_date'	=> 'required',
					'payment_deposit_end_date'		=> 'required',
				),
				array(
					"svendor.required"						=>	trans("The start vendor field is required."),
					"evendor.required"						=>	trans("The end vendor field is required."),
					"payment_deposit_start_date.required"	=>	trans("The payment deposit start date field is required."),
					"payment_deposit_end_date.required"		=>	trans("The payment deposit end date field is required."),
				)
			);
			
			if ($validator->fails()){

				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$startDate = $request->input('payment_deposit_start_date');
				$endDate = $request->input('payment_deposit_end_date');
				$svendor = $request->input('svendor'); 
				$evendor = $request->input('evendor'); 

				$vendorDates = VendorPayments::select('payment_deposit_date', 'id', 'vendor_id')
				->whereBetween('payment_deposit_date', [$request->input('payment_deposit_start_date'), $request
				->input('payment_deposit_end_date')])
				->whereBetween('vendor_id', [$svendor, $evendor])
				->where('vendors_payment.applied_to', 1)
				->groupBy('vendor_id')->get()->toArray();

				$vendorsarray = array();
				$i=0;
				$grandtotal = 0;
				$dp = array(); 
				$temp = 0;
				$compact = [];
				foreach($vendorDates as $kes=>$values){
					$singlearray = array();
					$singlearray['id'] 		= $values['vendor_id'];
					$name = Vendor::where('id', $values['vendor_id'])->first();
					$singlearray['name'] 	= $name->business_name;
					$singlearray['date'] 	= $values['payment_deposit_date'];
					$singlearray['paymentdata'] = array();
					$subtotal = 0;
					$vendorpayments	=  VendorPayments::where('vendor_id', $values['vendor_id'])->where('applied_to', 1)->get();
					foreach($vendorpayments as $key11 => $value22){
						$singlePayment = array();
						$singlePayment['deposit_date'] 		=  date('d/m/Y',strtotime($value22->payment_deposit_date));
						$singlePayment['check_number'] 		=  $value22->check_number;
						$singlePayment['check_amount'] 		=  $value22->check_amount;
						$singlePayment['rebate'] 			=  $value22->check_amount;
						$singlePayment['received_at']  		=  date('d/m/Y', strtotime($value22->created_at));
						$singlePayment['balance_due']  		=  '0.00';
						$singlearray['paymentdata'][] 		=  $singlePayment;
						$subtotal = $subtotal + $singlePayment['check_amount'];
					}
					$singlearray['subtotal'] 	= $subtotal;
					$vendorsarray[] 		= $singlearray;

					$grandtotal =  $grandtotal + $subtotal;
				}
			
				$sectionNameSingular = 'VENDOR PAYMENTS RECONCILIATION';
				$startVendor = $this->getvendorById($request->input('svendor'));
				$endVendor = $this->getvendorById($request->input('evendor'));
				$vendorNameArray = array('first' => $startVendor, 'last' => $endVendor);
				$startDate  = str_replace("-","",$startDate);
				$endDate  = str_replace("-","",$endDate);
				$filename = $vendorNameArray['first'].'_'.$vendorNameArray['last'].'_'.$startDate.'_'.$endDate.'.pdf';
				$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
				$folderPath = VENDOR_PAYMENT_RECONCILATION_PATH.$folderName;
				$pdf = PDF::loadView("admin.$this->model.reconciliation_export",array('vendorsarray' => $vendorsarray, 'grandtotal' => $grandtotal, 'sectionNameSingular'));
				$pdf->download('reconcilation.pdf');
				if(!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777,true);
				}
				$pdf->save($folderPath . '/' . $filename);

				$paymentExport 						=  new VendorExportPayment;
				$paymentExport->export_file 		= $folderName.$filename;
				$paymentExport->name 				= $filename;
				$paymentExport->deposit_start_date 	= $request->input('payment_deposit_start_date');
				$paymentExport->deposit_end_date	= $request->input('payment_deposit_end_date');
				$paymentExport->vendor_start_id		= 0;
				$paymentExport->vendor_end_id		= 0;
				$paymentExport->type				= 3;
				$paymentExport->save();
				
				return Redirect::route('VendorPayments.reconciliation');
			}
		}
		
	}

	public function deletevendorPaymentReconciliation()
	{
		$datas =  VendorExportPayment::where('type', 3)->get();
		
		foreach($datas as $data){
			$file =  storage_path('app/public/pdf/'.$data->export_file);
			if(File::exists($file)){
				unlink($file);
			}
			
			VendorExportPayment::where('type', 3)->where('id', $data->id)->delete();
		}
		return Redirect::route('VendorPayments.reconciliation');

	}

	public function getRebates(Request $request)
	{	
		$rebates =  ImportManufacturerRebate::select('id', 'transaction_date', 'paid_amount')->where('vendor_id', $request->vendor)->get();
		$rebateArray = array();
		if(!empty($rebates)){
			foreach($rebates as $key=>$value){
				$signlerebate = array();
				$signlerebate['id'] = $value->id;
				$signlerebate['transaction_date'] = $value->transaction_date;
				$signlerebate['sum'] =  ImportManufacturerRebateDetails::where('manufacturer_rebate_id', $value->id)->sum('amount');
				$signlerebate['paid_amount'] = $value->paid_amount;
				$signlerebate['remaining_amount'] = $signlerebate['sum'] - $signlerebate['paid_amount'];
				$rebateArray[] = $signlerebate;
			}
		}
		$html = '';
		if(count($rebateArray) != 0){
			foreach($rebateArray as $key =>$value){
				if($value['remaining_amount'] != 0){
					$html .= "<tr>".
					"<td><input type='checkbox' name='applied[]' data-id=".$value['id']." class='sub_chk'></td>".
					"<td>".date(config::get("Reading.date_format"),strtotime($value['transaction_date']))."</td>".
					"<td>".CustomHelper::priceFormat($value['sum'])."</td>".
					"<td>".CustomHelper::priceFormat($value['paid_amount'])."</td>".
					"<td>".CustomHelper::priceFormat($value['remaining_amount'])."</td>".
					"</tr>";
				}	
			}
		}
		$checkData = VendorPayments::where('vendor_id', $request->vendor)->where('applied_to', 2)->orderBy('created_at', 'desc')->where('group_id', Session::get('group'))->first();
		$date = '';
		$amonut = '';
		$vendor = '';
		$totalCheckAmount = '0.00';
		$amount = '0.00';
		$checknumber = '';
		$date = '';
		 if(!empty($checkData)){
			$date 	= date('Y-m-d', strtotime($checkData->payment_deposit_date));
			$amonut = $checkData->appied_balance_amount;
			$vendor = $checkData->vendor_id;
			$checknumber = $checkData->check_number;
		 }
		$totalCheckAmount =  VendorPayments::where('vendor_id', $request->vendor)->where('applied_to', 2)->sum('appied_balance_amount');
		if($totalCheckAmount != 0){
			$totalCheckAmount = 'Vendor has '. $totalCheckAmount .' in unapplied checks. click apply next to process';
		}
		return response()->json(['success' => true, 'html' => $html, 'totalCheckAmount' => $totalCheckAmount, 'date' => $date, 'amount' => $amonut, 'vendor' => $vendor, 'checknumber' => $checknumber]);
	}


	public function  vendorPaymentApplied(Request $request)
	{	
	    $vendorAmount = CustomHelper::checkVendorBalance($request->vendor);
		Validator::extend('invalid_amount', function ($attribute, $value, $parameters, $validator) {
			$pndingAmount = $parameters[0];
			if($pndingAmount <= $value){
				return false;
			}else{
				return true; 
			}
		});
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'vendor'				=> 'required',
					'payment_date'			=> 'required',
					'checknumber'			=> 'required',
					'checkamount'			=> 'required',
					// 'checkamount'			=> 'required|invalid_amount:'.$vendorAmount,
				),
				array(
					"vendor.required"					=>	trans("The vendor field is required."),
					"payment_date.required"				=>	trans("The payment deposit field is required."),
					"checknumber.required"				=>	trans("The check number field is required."),
					"checkamount.required"				=>	trans("The check amount field is required."),
					"checkamount.invalid_amount"	    =>	trans("Invalid amount request."),
					)
				);
				if ($validator->fails()){
					$response	=	array(
					'success' 	=> false,
					'errors' 	=> $validator->errors()
				);
				return Response::json($response);
			}else{ 
				$idsArray = array();
				$Amount = 0;
				$pendindAmont = 0;
				$checkAmount = $request->checkamount;
				if(!empty($request->ids)){
					$rebatesIds =  explode(',', $request->ids);
				}else{
					$rebatesIds =  array();
				}
				
			    if(count($rebatesIds) > 0){
					foreach($rebatesIds as $rebets){
						if($checkAmount > 0){
							$Rebatedetails = ImportManufacturerRebateDetails::where('manufacturer_rebate_id', $rebets)->sum('amount');
							$paidAmount = ImportManufacturerRebate::where('id',$rebets)->value('paid_amount');
							$remainingAmount = $Rebatedetails - $paidAmount;
							if($checkAmount >= $remainingAmount){
								ImportManufacturerRebate::where('id', $rebets)->increment('paid_amount', $remainingAmount);
								$checkAmount = $checkAmount - $remainingAmount;
							}else{
								ImportManufacturerRebate::where('id', $rebets)->increment('paid_amount',$checkAmount);
								$checkAmount = 0;
							}
						}
					}
					
					$checkData =  VendorPayments::where('check_number', $request->checknumber)->where('vendor_id', $request->vendor)->where('group_id', Session::get('group'))->where('applied_to', 2)->first();
					if(!empty($checkData)){
						if($checkAmount > 0){
							$checkData->applied_to 	=  2;
						}else{
							$checkData->applied_to 	=  1;
						}
						$checkData->appied_paid_amount 	= $checkData->appied_paid_amount + $checkAmount;
						$checkData->appied_balance_amount 	= $checkData->check_amount - $checkData->appied_paid_amount;
						$checkData->save();
						return $response	=	array('success' => 	true);
					}else{
						$obj 							=  new VendorPayments;
						$obj->admin_id					=  Auth::guard('admin')->user()->id;
						$obj->vendor_id 				=  $request->vendor;
						$obj->payment_deposit_date 		=  $request->payment_date;
						$obj->check_number 				=  $request->checknumber;
						$obj->check_amount 				=  $request->checkamount;
						if($checkAmount > 0){
							$obj->applied_to 	=  2;
						}else{
							$obj->applied_to 	=  1;
						}
						$checkData->appied_paid_amount 	=  $checkAmount;
						$checkData->appied_balance_amount 	= $checkData->check_amount - $checkData->appied_paid_amount;
						$obj->admin_id					=  Auth::guard('admin')->user()->id;
						$obj->group_id					=  !empty(Session::get('group')) ? Session::get('group'):0;
						$obj->save();
						$response	=	array('success' => 	true); 
						return Response::json($response);
					}
				}else{
					$checkData =  VendorPayments::where('check_number', $request->checknumber)->where('admin_id', auth()->guard('admin')->user()->id)->where('group_id', Session::get('group'))->first();
					if(empty($checkData)){
						$obj 							=  new VendorPayments;
						$obj->admin_id					=  Auth::guard('admin')->user()->id;
						$obj->vendor_id 				=  $request->vendor;
						$obj->payment_deposit_date 		=  $request->payment_date;
						$obj->check_number 				=  $request->checknumber;
						$obj->check_amount 				=  $request->checkamount;
						$obj->applied_to 				=  2;
						$obj->appied_balance_amount 	=  $request->checkamount;
						$obj->admin_id					=  Auth::guard('admin')->user()->id;
						$obj->group_id					=  !empty(Session::get('group')) ? Session::get('group'):0;
						$obj->save();
						$response	=	array('success' => 	true); 
						return Response::json($response);
					}else{
						$response	=	array('success' => 	true); 
						return Response::json($response);
					}
				}
			}
		}

	}

	public function getCheckData(Request $request)
	{
		$request->vendor;
		if($request->check != ''){
		 $checkData = VendorPayments::where('vendor_id', $request->vendor)->orderBy('created_at', 'desc')->first();

		 if(!empty($checkData)){
			$date 	= date('Y-m-d', strtotime($checkData->payment_deposit_date));
			$amonut = $checkData->check_amount;
			$vendor = $checkData->vendor_id;
			return response()->json(['success' => true, 'date' => $date, 'amount' => $amonut, 'vendor' => $vendor]);
		 }else{
			 return false;
		 }

		}
		
	}

	public function getVendor(Request $request) 
	{ 
	 	return CustomHelper::getVendor($request->id);
	}
	
	
}// end VendorPaymentController

