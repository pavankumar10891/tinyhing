<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\BaseController;
use App\Model\Setting;
use App\Model\AdminLanguageSetting;
use App\Model\LanguageSetting;
use App\Model\Language;
use Illuminate\Http\Request;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

/**
 * LanguageSettings Controller
 *
 * Add your methods in the class below
 *
 * This file will render views from views/languages/
 */
 
class LanguageSettingsController extends BaseController {

/**
* Function for display list of all languages
*
* @param null
*
* @return view page. 
*/
	public $model	=	'LanguageSetting';
	public $sectionName			=	'LanguageSettings';
	public $sectionNameSingular	=	'LanguageSettings';
	
	public function __construct(Request $request) {

		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);

		$this->request = $request;
	}
	
	public function listLanguageSetting(Request $request){	
		$DB								=	AdminLanguageSetting::query();
		$searchVariable					=	array(); 
		$inputGet						=	$request->all();
		if(!empty($inputGet)) {	
			$searchData					=	$request->all();
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
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable		=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		
		$sortBy 						=	($request->input('sortBy')) ? $request->input('sortBy') : 'updated_at';
	    $order  						=	($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$activeLanguages = DB::table('languages')->where('is_active',1)->pluck('lang_code','lang_code');
		$result 						=	$DB
												// ->where("id",">",1343)
												->whereIn('locale',$activeLanguages)
												->orderBy($sortBy,$order)
												->paginate(Config::get("Reading.records_per_page"));
		$complete_string				=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string					=	http_build_query($complete_string);
		$result->appends($inputGet)->render();
		return  View::make('admin.languages.index',compact('result','searchVariable','sortBy','order','query_string'));
	} // listLanguageSetting()

/**
 * Function for display page for  add new text or message
 *
 * @param null
 *
 * @return view page. 
 */
	public function addLanguageSetting(Request $request){ 
		$languages			=	Language::where('is_active', '=', '1')->get();
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		return  View::make('admin.languages.add',compact('languages' ,'language_code'));
	
	} // end addLanguageSetting()

/**
 * Function for save new text or message 
 *
 * @param null
 *
 * @return redirect page. 
 */
	public function saveLanguageSetting(Request $request){	
		$thisData	=	$request->all();
		$validator  = Validator::make(
			$thisData,
			array(
				'default' 			=> 'required|unique:language_settings,msgid'
			),
			array(
				'default.required' 	=> 'Default language required'
			)
		);
		
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/language-settings/add-setting')
				->withErrors($validator)->withInput();
		}else{
			
			$msgid					=	$request->input('default');
			foreach($thisData['language'] as $key => $val){
				$obj	 			= 	new AdminLanguageSetting;
				$obj->msgid    		=  trim($msgid);
				$obj->locale   		=  trim($key);
				$obj->msgstr   		=  empty($val)?"":$val;
				$obj->save();
				/* $langarr 			=	$thisData['language'];
				$filename			= 'f' . gmdate('YmdHis');
				foreach ($langarr as $k => $v){			
				
						$path = ROOT.DS.'resources'.DS.'lang'.DS.$k;
						if (!file_exists($path)) mkdir($path,0777);
						$file = $path.DS.$filename;
						if (!file_exists($path)) touch($file);
						$file = new File($path.DS.$filename);
					    $list			=	LanguageSetting::where('locale',$k)->get();
						$currLangArray	=	'<?php return array(';
						foreach($list as $listDetails){
							if($listDetails['locale'] == $k){
								$currLangArray	.=  '"'.$listDetails['msgid'].'"=>"'.$listDetails['msgstr'].'",'."\n";
							}
						}
						$currLangArray	.=	');';
						
						$file 			= 	 ROOT.DS.'resources'.DS.'lang'.DS.$k.DS.'messages.php';
						$bytes_written  = 	 File::put($file, $currLangArray);
						
						if (file_exists(ROOT.DS.'resources'.DS.'lang'.DS.'messages.php'))
						rename (ROOT.DS.'resources'.DS.'lang'.DS.$k.DS.'messages.php',ROOT.DS.'resources'.DS.'lang'.DS.$k.DS.'messages.php.old'.gmdate('YmdHis'));
					} */
			}
			$this->settingFileWrite();
			return Redirect::to('adminpnlx/language-settings')
				->with('success',trans("New word added successfully") );
		}
	}// end saveLanguageSetting()
 
/**
 * Function for display page for edit text or message
 *
 * @param $Id as id of created text or message
 *
 * @return view page. 
*/
	public function editLanguageSetting($Id,Request $request){ 
		$id = $request->input('id');
		$result		=	AdminLanguageSetting::find($Id);
		//pr($result);die;
		return  View::make('admin.languages.edit',compact('Id','result'));
	} // end editLanguageSetting()

/**
 * Function for save changed message or text 
 *
 * @param $Id as id of created text or message
 *
 * @return redirect page. 
*/
	function updateLanguageSetting(Request $request){
			$id				=	$request->input('id');	
			$msgstr			=   $request->input('msgstr');
			$obj	 	 	=	AdminLanguageSetting::find($id);
			$obj->msgstr   	= 	!empty($msgstr) ? addslashes($msgstr):'';
			$local 			=   $obj->locale;
			$obj->save();
			$this->settingFileWrite();
			/** Writes on the file **/

				/* $filename	= 	'f' . gmdate('YmdHis');
				$path 		= 	ROOT.DS.'resources'.DS.'lang'.DS.$local;
				if (!file_exists($path)) mkdir($path,0777);
				$file 		= 	$path.DS.$filename;
				if (!file_exists($path)) touch($file);
				$file 		= 	new File($path.DS.$filename);
				
				
				$list		=	LanguageSetting::where('locale',$local)->get();
				
				//$languages	=	language::where('is_active', '=', '1')->get(array('folder_code','lang_code'));
				$currLangArray	=	'<?php return array(';
				foreach($list as $listDetails){
					if($listDetails['locale'] == $local){
						$currLangArray	.=  '"'.$listDetails['msgid'].'"=>"'.$listDetails['msgstr'].'",'."\n";
					}
				}
				$currLangArray	.=	');';
				
				$file 			= 	 ROOT.DS.'resources'.DS.'lang'.DS.$local.DS.'messages.php';
				$bytes_written  = 	 File::put($file, $currLangArray); */
				Session::flash('flash_notice',trans("Language word updated successfully")); 
			die;
	} // end updateLanguageSetting()
/**
 * Function for write file on create and update text  or message 
 *
 * @param null
 *
 * @return void. 
 */
	public function settingFileWrite(){ 
		//echo "HELLO";die;
		/* $DB			=	AdminLanguageSetting::query();
		$list		=	$DB->get()->toArray(); */
		//pr($list);die;
		$languages	=	Language::where('is_active', '=', '1')->get(array('folder_code','lang_code'));
		
		foreach($languages as $key => $val){
			$currLangArray	=	'<?php return array(';
			$list			=	AdminLanguageSetting::where('locale',$val->lang_code)->select("msgid","msgstr")->get()->toArray();
			//pr($list);die;
			if(!empty($list)){
				foreach($list as $listDetails){
					//if($listDetails['locale'] == $val->lang_code){
						$currLangArray	.=  '"'.$listDetails['msgid'].'"=>"'.$listDetails['msgstr'].'",'."\n";

					//}
				}
			}
			$currLangArray	.=	');';
			
			$file 			= 	 ROOT.DS.'resources'.DS.'lang'.DS.$val->lang_code.DS.'messages.php';
			File::put($file, $currLangArray);
			/* $bytes_written  = 	 
			if ($bytes_written === false)
			{
				die("Error writing to file");
			} */
		}
	}// end settingFileWrite()
	
/*  Import language data from en file  */
	function import($lang_code = 'en') {
		// set the filename to read from
		$filename='message.php';
		$filename = ROOT.DS.'resources'.DS.'lang'.DS.$lang_code.DS.'messages.php';
		// open the file
		
		$filehandle = fopen($filename, "r");
		
		while (($row = fgets($filehandle)) !== FALSE) {
		  if (preg_match('/"([^"]+)"/', $row, $msgstring)) {
				// parse string in hochkomma:
				$msgid = $msgstring[1];
				$re = '~(["\'])[^"\']+\1[^"\']*(["\'])([^"\']+)\1~';
				//pr($msgid);
				 if (!empty($msgid)) {  
					//$row = fgets($filehandle);
					if ( preg_match($re, $row, $mString) )
					{
						$msgstr =  $mString[3];						
					} // check if exists
					
					$trec =DB::table('language_settings')->where('msgid','like', '%' . $msgid . '%')->where('locale',$lang_code)->first();
					
				    if (empty($trec)) {  						
						$modelSettings 				=   new LanguageSetting;
						$modelSettings->msgid			=	$msgid;
						$modelSettings->msgstr			=	$msgstr;
						$modelSettings->locale			=	$lang_code;
						$modelSettings->save();  
					} else { 
					} 
				} 
			} 
		}
		fclose($filehandle);
		die;
   }  //end import()
   
   
   
	public function importNewLanguage($lang_code){
		$english_string_lists		=	DB::table("language_settings")->where("locale","en")->get()->toArray();
		if(!empty($english_string_lists)){
			foreach($english_string_lists as $english){
				$language_settings	=	DB::table("language_settings")->where("msgid",$english->msgid)->where("locale",$lang_code)->first();
				if(empty($language_settings)){
					$msgstr						=	$this->translate($english->msgstr,"en",$lang_code);
					$modelSettings 				=   new LanguageSetting;
					$modelSettings->msgid		=	$english->msgid;
					$modelSettings->msgstr		=	$msgstr;
					$modelSettings->locale		=	$lang_code;
					$modelSettings->save();
				}else{
					$getstring		=	explode(":",$language_settings->msgstr);
					if(trim($getstring[0]) == "d is over the quota"){
						$msgstr						=	$this->translate($english->msgstr,"en",$lang_code);
						$modelSettings 				=   LanguageSetting::find($language_settings->id);
						$modelSettings->msgid		=	$english->msgid;
						$modelSettings->msgstr		=	$msgstr;
						$modelSettings->locale		=	$lang_code;
						$modelSettings->save();
					}
				}
			}
		}
	}
	
	function translate($text, $from = '', $to = '') {
		$url = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate?oncomplete=MicrosoftTranslateComplete&appId=EF45DE6734F756B2F1DEF91B9DFCE3FD0B03748B&text='.urlencode($text).'&from='.urlencode($from).'&to='.urlencode($to).'';
		$response 	= 	file_get_contents($url);
		if(strstr($response,'must be a valid language')){
			return $text;
		}		
		$result		=	str_replace('");','',substr($response,31,strlen($response)));	
		return $result;
	}
	
}// end LanguageSettingsController
