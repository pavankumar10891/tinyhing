<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\EmailTemplate;
use App\Model\EmailTemplateDescription;
use App\Model\EmailAction;
use Illuminate\Http\Request;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

/**
* Emailtemplate Controller
*
* Add your methods in the class below
*
* This file will render views from views/emailtemplates
*/
 
	class EmailtemplateController extends BaseController {
	
	public $model				=	'EmailTemplate';
	public $sectionName			=	'Email Templates';
	public $sectionNameSingular	=	'Email Template';
	
	public function __construct(Request $request) {
		parent::__construct();
		$this->request = $request;
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
	}
/**
* Function for display list of all email templates
*
* @param null
*
* @return view page. 
*/
	public function listTemplate(Request $request){
		$DB				=	EmailTemplate::query();
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
		if ($request->all()){
			$searchData	=	$request->all();
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
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
						if($fieldName == "name" || $fieldName == "subject"){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
						}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'updated_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($request->all())->render();
		
		return  View::make('admin.emailtemplates.index', compact('results','searchVariable','sortBy','order','query_string'));
	}// end listTemplate()
/**
* Function for display page for add email template
*
* @param null
*
* @return view page. 
*/
	public function addTemplate(Request $request){
		$Action_options	=	EmailAction::pluck('action','action')->toArray();
		$languages					=	DB::select("CALL GetAcitveLanguages_english(1)");
        if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		//echo "<pre>"; print_r($Action_options); die;
		return  View::make('admin.emailtemplates.add',compact('Action_options','languages' ,'language_code'));
	}// end addTemplate()
/**
* Function for display save email template
*
* @param null
*
* @return redirect page. 
*/
	public function saveTemplate(Request $request){
		//$this->request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'action' 		=> 'required',
				//'constants' 	=> 'required', 
				'body' 			=> 'required'
			),
			array(
				"name.required"			=>	trans("The name field is required."),
				"subject.required"		=>	trans("The subject field is required."),				
				"action.required"		=>	trans("The action field is required."),
				"constants.required"	=>	trans("The constants field is required."),				
				"body.required"			=>	trans("The email body field is required."),				
			)
		);
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/email-manager/add-template')
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$obj 								= new EmailTemplate;
			
			$obj->name		 					= $dafaultLanguageArray['name'];
			$obj->subject		  	    		= $dafaultLanguageArray['subject'];
			$obj->body    	   				    = $dafaultLanguageArray['body'];
			$obj->action       					= $dafaultLanguageArray['action'];
			
			$objSave				            = $obj->save();
			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route("EmailTemplate.index");
			}
			$last_id			=	$obj->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$EmailTemplateDescription_obj					=  new EmailTemplateDescription();
				$EmailTemplateDescription_obj->language_id		=	$language_id;
				$EmailTemplateDescription_obj->parent_id		=	$last_id;
				$EmailTemplateDescription_obj->name				=	$value['name'];	
				$EmailTemplateDescription_obj->subject			=	$value['subject'];	
				$EmailTemplateDescription_obj->body				=	$value['body'];	
				$EmailTemplateDescription_obj->save();
			}
			DB::commit();
			Session::flash('flash_notice', trans("Email template added successfully")); 
			return Redirect::intended('adminpnlx/email-manager');
		}
		
	}//  end saveTemplate()
/**
* Function for display page for edit email template page
*
* @param $Id as id of email template
*
* @return view page. 
*/
	public function editTemplate($Id,Request $request){
		$Action_options	=	EmailAction::pluck('action','action')->toArray();
		$emailTemplate	=	EmailTemplate::find($Id);
		if(empty($emailTemplate)) {
			return Redirect::to('adminpnlx/email-manager');
		}
		$EmailTemplateDescription	=	EmailTemplateDescription::where('parent_id', '=',  $Id)->get();
        $multiLanguage		 	=	array();
        if(!empty($EmailTemplateDescription)){
			foreach($EmailTemplateDescription as $description) {
				$multiLanguage[$description->language_id]['name']			=	$description->name;			
				$multiLanguage[$description->language_id]['subject']		=	$description->subject;			
				$multiLanguage[$description->language_id]['body']			=	$description->body;			
			}
		}
        $languages			=	DB::select("CALL GetAcitveLanguages_english(1)");
        if(!empty($languages)){
			foreach($languages as &$language){
				$language->image	=	LANGUAGE_IMAGE_URL.$language->image;
			}
		}
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];

		return  View::make('admin.emailtemplates.edit',compact('languages','language_code','multiLanguage','Action_options','emailTemplate','languages' ,'language_code'));
	} // end editTemplate()
/**
* Function for update email template
*
* @param $Id as id of email template
*
* @return redirect page. 
*/
	public function updateTemplate($Id,Request $request){
		$model					=	EmailTemplate::findorFail($Id);
		if(empty($model)) {
			return Redirect::back();
		}
		//$this->request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'body' 			=> 'required'
			),
			array(
				"name.required"			=>	trans("The name field is required."),
				"subject.required"		=>	trans("The subject field is required."),			
				"body.required"			=>	trans("The email body field is required."),				
			)
		);
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/email-manager/edit-template/'.$Id)
				->withErrors($validator)->withInput();
		}else{
			$obj 								= $model;
			
			$obj->name		 					= $dafaultLanguageArray['name'];
			$obj->subject		  	    		= $dafaultLanguageArray['subject'];
			$obj->body    	   				    = $dafaultLanguageArray['body'];
			
			$objSave				            = $obj->save();

			if(!$objSave) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::route("EmailTemplate.index");
			}
			$last_id			=	$obj->id;

			EmailTemplateDescription::where('parent_id', '=', $last_id)->delete();
			foreach ($thisData['data'] as $language_id => $value) {
				$EmailTemplateDescription_obj					=  new EmailTemplateDescription();
				$EmailTemplateDescription_obj->language_id		=	$language_id;
				$EmailTemplateDescription_obj->parent_id		=	$last_id;
				$EmailTemplateDescription_obj->name				=	$value['name'];	
				$EmailTemplateDescription_obj->subject			=	$value['subject'];	
				$EmailTemplateDescription_obj->body				=	$value['body'];	
				$EmailTemplateDescription_obj->save();
			}

			Session::flash('flash_notice', trans("Email template updated successfully")); 
			return Redirect::intended('adminpnlx/email-manager');
		}
	} // end updateTemplate()
/**
* Function for get all  defined constant  for email template
*
* @param null
*
* @return all  constant defined for template. 
*/
	public function getConstant(Request $request){
		if($request->all()){
			$constantName 	= 	$request->input('constant');
			$options		= 	EmailAction::where('action', '=', $constantName)->pluck('options','action'); 
			$a 				= 	explode(',',$options[$constantName]);
			echo json_encode($a);
		}
		exit;
	}
}

