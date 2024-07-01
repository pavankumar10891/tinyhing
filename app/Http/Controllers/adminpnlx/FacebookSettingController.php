<?php
/**
* Settings Controller
*
* Add your methods in the class below
*
* This file will render views from views/settings
*/
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use App\Model\FacebookSetting;

class FacebookSettingController extends BaseController {
	
	public $model		=	'Facebook';
	public $sectionName	=	'Facebook Setting';
	public $sectionNameSingular	=	'Facebook Setting';
	public function __construct(Request $request){
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
	}
	

/**
* function edit facebook settings view page
*
*@param $Id as Id
*
* @return void
*/
	public function edit(Request $request){
		$model			 = 	FacebookSetting::find(1);
		return  View::make('admin.facebook_settings.edit',compact('model'));
	}//end editSetting()
/**
* function for update setting
*
* @param $Id as Id
*
* @return void
*/	
	public function update(Request $request){
		$thisData				=	$request->all(); 
			$validator  			= 	Validator::make(
				$request->all(),
				array(
					'page_id' 		=> 'required'
				)
			);
			if ($validator->fails()){	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$model			 = 	FacebookSetting::find(1);
				if(empty($model)){
					$obj	 					=  new FacebookSetting;
				}else{
					$obj	 					=  	FacebookSetting::find(1);
				}
				$obj->page_id    			= $request->input('page_id');
				$obj->user_token   			= $request->input('user_token');
				$obj->user_id   			= $request->input('user_id');
				$url = "https://graph.facebook.com/".$request->input('user_id')."/accounts?access_token=".$request->input('user_token');
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$response = curl_exec($ch);
				curl_close($ch);
				$response = json_decode($response,true);
				$obj->page_token   			= !empty( $response['data']) ? $response['data'][0]['access_token']  : '';
				$obj->save();
				Session::flash('flash_notice', 'Facebook Setting updated successfully.'); 
				return Redirect::intended('adminpnlx/facebook-setting/edit');
			}
	}//end updateSetting()

	public function updateToken(Request $request){
		 
		// print_r($request->all());
		 
		/*$thisData				=	$request->all(); 
		print_r($thisData);*/
		//$this->request->replace($this->arrayStripTags($request->all()));
		/*$validator  			= 	Validator::make(
			$request->all(),
			array(
				'title' 		=> 'required',
				'key' 			=> 'required',
				'value' 		=> 'required',
				'input_type' 	=> 'required'
			)
		);
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/settings/edit-setting/'.$Id)
				->withErrors($validator)->withInput();
		}else{
			
		}*/	
		if($request->token != ''){
			$obj	 					=  FacebookSetting::find(1);
			$obj->app_id   				= $request->userid;
			$obj->token   				= $request->token;
			$obj->save();
		}
		    
		//$this->settingFileWrite();
		return response()->json(['success' => true]);
	}//end updateSetting()

	
}//end FacebookSettingController class
