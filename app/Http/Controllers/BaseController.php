<?php
namespace App\Http\Controllers;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\User;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator,Str,App,DateTime,PDF;

/**
* Base Controller
*
* Add your methods in the class below
*
* This is the base controller called everytime on every request
*/
class BaseController extends Controller {
	
	protected $user;
	
	public function __construct() {
		/*$this->middleware(function ($request, $next){
			return $next($request);
		});*/
	}// end function __construct()
	
	/**
	* Setup the layout used by the controller.
	*
	* @return layout
	*/
	protected function setupLayout(){
		if(Request::segment(1) != 'admin'){
			
		}
		if ( ! is_null($this->layout)){
			$this->layout = View::make($this->layout);
		}
	}//end setupLayout()
	
	/** 
	* Function to make slug according model from any certain field
	*
	* @param title     as value of field
	* @param modelName as section model name
	* @param limit 	as limit of characters
	* 
	* @return string
	*/	
	public function getSlug($title, $fieldName,$modelName,$limit = 30){
		$slug 		= 	 substr(Str::slug($title),0 ,$limit);
		$Model		=	 "\App\Models\\$modelName";
		$slugCount 	=    count($Model::where($fieldName, 'regexp', "/^{$slug}(-[0-9]*)?$/i")->get());
		return ($slugCount > 0) ? $slug."-".$slugCount : $slug;
	}//end getSlug()

	/** 
	* Function to make slug without model name from any certain field
	*
	* @param title     as value of field
	* @param tableName as table name
	* @param limit 	as limit of characters
	* 
	* @return string
	*/	
	public function getSlugWithoutModel($title, $fieldName='' ,$tableName,$limit = 30){ 	
		$slug 		=	substr(Str::slug($title),0 ,$limit);
		$slug 		=	Str::slug($title);
		$DB 		= 	DB::table($tableName);
		$slugCount 	= 	count( $DB->whereRaw("$fieldName REGEXP '^{$slug}(-[0-9]*)?$'")->get() );
		return ($slugCount > 0) ? $slug."-".$slugCount: $slug;
	}//end getSlugWithoutModel()

	/** 
	* Function to search result in database
	*
	* @param data  as form data array
	*
	* @return query string
	*/		
	public function search($query,$searchArray){
		$search	=	false;
		if(!empty($searchArray)){
			unset($searchArray["_token"]);
			unset($searchArray["page"]);
			unset($searchArray["per_page"]);
			foreach($searchArray as $fieldName => $fieldValue){
				if(!empty($fieldValue) || $fieldValue != ""){
					$search	=	true;
				}
			}
			if($search){
				foreach($searchArray as $fieldName => $fieldValue){
					if(!empty($fieldValue) || $fieldValue != ""){
						$query->where("$fieldName",'like','%'.$fieldValue.'%');
					}
				}
			}
		}
		return $query;
	}//end search()
		
/** 
* Function to send email form website
*
* @param string $to            as to address
* @param string $fullName      as full name of receiver
* @param string $subject       as subject
* @param string $messageBody   as message body
*
* @return void
*/
public function sendMail($to,$fullName,$subject,$messageBody, $from = '',$files = false,$path='',$attachmentName='') {
	$from	=	Config::get("Site.from_email");
	$data				=	array();
	$data['to']			=	$to;
	$data['from']		=	(!empty($from) ? $from : Config::get("Site.email"));
	$data['fullName']	=	$fullName;
	$data['subject']	=	$subject;
	$data['filepath']	=	$path;
	$data['attachmentName']	=	$attachmentName; 

	if($files===false){
		Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
			$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);
		});
	}else{
		if($attachmentName!=''){
			Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'],array('as'=>$data['attachmentName']));
			});
		}else{
			Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
			});
		}
	}
	DB::table('email_logs')->insert(
		array(
			'email_to'	 => $data['to'],
			'email_from' => $from,
			'subject'	 => $data['subject'],
			'message'	 =>	$messageBody,
			'created_at' => DB::raw('NOW()')
		)
	); 
}

/** 
 * Function to SendSms
 *
 * @param string $to  as to mobile
 *
 * @param string $messageBody   as message body
 *
 * @return void
 */	
public function SendSms($to = "",$body = "") {
	//$to	=	"+91".$to;
	/* if($to != "") {
		$sid 		= 	config::get("twilio.twilio_sid");
		$from 		= 	config::get("twilio.twilio_from");
		$token 		=	config::get("twilio.twilio_token_number");
		$uri 		= 	"https://api.twilio.com/2010-04-01/Accounts/" . $sid . "/Messages";
		$auth 		= 	$sid . ':' . $token;
		$fields 	= 
		'&To=' . urlencode( $to ) . 
		'&From=' . urlencode( $from ) . 
	//	'&Body=' . urlencode( $body );
		'&Body=' . $body;
		// start cURL
		$res 		= 	curl_init();
		curl_setopt( $res, CURLOPT_URL, $uri );
		curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $res, CURLOPT_RETURNTRANSFER, true ); // don't echo
		curl_setopt( $res, CURLOPT_POST, true ); // number of fields
		curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
		curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
		
		try {
			$result 	= 	curl_exec( $res );
			print_r($result);die;
			curl_close($res);
		} catch (Exception $e) {
		
		}
	} */
	return true; 
}
	
	public function getVerificationCode(){
		//$code	=	rand(100000,999999);
		$code	=	"0000";
		return $code;
	}
	
	public  function arrayStripTags($array){
		$result =array();
		foreach ($array as $key => $value) {
			// Don't allow tags on key either, maybe useful for dynamic forms.
			$key = strip_tags($key,ALLOWED_TAGS_XSS);

			// If the value is an array, we will just recurse back into the
			// function to keep stripping the tags out of the array,
			// otherwise we will set the stripped value.
			if (is_array($value)) {
				$result[$key] = $this->arrayStripTags($value);
			} else {
				// I am using strip_tags(), you may use htmlentities(),
				// also I am doing trim() here, you may remove it, if you wish.
				$result[$key] = trim(strip_tags($value,ALLOWED_TAGS_XSS));
			}
		}

		return $result;

	}
	
	public function saveCkeditorImages() {
		if(!empty($_GET['CKEditorFuncNum'])){
			$image_url				=	"";
			$msg					=	"";
			// Will be returned empty if no problems
			$callback = ($_GET['CKEditorFuncNum']);        // Tells CKeditor which function you are executing
			$image_details 				= 	getimagesize($_FILES['upload']["tmp_name"]);
			$image_mime_type			=	(isset($image_details["mime"]) && !empty($image_details["mime"])) ? $image_details["mime"] : "";
			if($image_mime_type	==	'image/jpeg' || $image_mime_type == 'image/jpg' || $image_mime_type == 'image/gif' || $image_mime_type == 'image/png'){
				$ext					=	$this->getExtension($_FILES['upload']['name']);
				$fileName				=	"ck_editor_".time().".".$ext;
				$upload_path			=	CK_EDITOR_ROOT_PATH;
				if(move_uploaded_file($_FILES['upload']['tmp_name'],$upload_path.$fileName)){
					$image_url 			= 	CK_EDITOR_URL. $fileName;    
				}
			}else{
				$msg =  'error : Please select a valid image. valid extension are jpeg, jpg, gif, png';
			}
			$output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$callback.', "'.$image_url .'","'.$msg.'");</script>';
			echo $output;
			exit;
		}
	}
	
	function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		$ext = strtolower($ext);
		return $ext;
	}
	
	/** 
	 * Function to _update_all_status
	 *
	 * param source tableName,id,status,fieldName
	 */	
	public function _update_all_status($tableName = null,$id = 0,$status= 0,$fieldName = 'is_active'){
		DB::beginTransaction();
		$response			=	DB::statement("CALL UpdateAllTableStatus('$tableName',$id,$status,'$fieldName')");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong")); 
			return Redirect::back();
		}
		DB::commit();
		return true;
	}
	/** 
	 * Function to _update_all_child_status
	 *
	 * param source tableName,id,status,fieldName
	 */	
	public function _update_all_child_status($tableName = null,$parent_id = 0, $parent_field_name='parent_id', $status= 0,$fieldName = 'is_active',$neglect=4){
		DB::beginTransaction();
		$response			=	DB::statement("CALL UpdateChildTableStatus('$tableName',$parent_id,'$parent_field_name',$status,'$fieldName',$neglect)");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong")); 
			return Redirect::back();
		}
		DB::commit();
	}
	/** 
	 * Function to _delete_table_entry
	 *
	 * param source tableName,id,fieldName
	 */
	public function _delete_table_entry($tableName = null,$id = 0,$fieldName = null){
		DB::beginTransaction();
		$response			=	DB::statement("CALL DeleteAllTableDataById('$tableName',$id,'$fieldName')");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong")); 
			return Redirect::back();
		}
		DB::commit();
	}// end _delete_table_entry()
	
	public function change_error_msg_layout($errors = array()){
		$response				=	array();
		$response["status"]		=	"error";
		if(!empty($errors)){
			$error_msg				=	"";
			foreach($errors as $errormsg){
				$error_msg1			=	(!empty($errormsg[0])) ? $errormsg[0] : "";
				$error_msg			.=	$error_msg1.", ";
			}
			$response["msg"]	=	trim($error_msg,", ");			
		}else {
			$response["msg"]	=	"";			
		}
		$response["data"]			=	(object)array();
		$response["errors"]			=	$errors;
		return $response;
	}

	public function change_error_msg_layout_with_array($errors = array()){
		$response				=	array();
		$response["status"]		=	"error";
		if(!empty($errors)){
			$error_msg				=	"";
			foreach($errors as $errormsg){
				$error_msg1			=	(!empty($errormsg[0])) ? $errormsg[0] : "";
				$error_msg			.=	$error_msg1.", ";
			}
			$response["msg"]	=	trim($error_msg,", ");			
		}else {
			$response["msg"]	=	"";			
		}
		$response["data"]			=	array();
		$response["errors"]			=	$errors;
		return $response;
	}
	
	public function get_csv($data=array(),$posttype){
		ob_start();
		ob_clean();
		$file_name = date("d-m-y").$posttype.".csv";
		@header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		@header('Content-Description: File Transfer');
		@header("Content-type: text/csv");
		@header("Content-Disposition: attachment; filename=".$file_name);
		@header("Expires: 0");
		@header("Pragma: public");
		$file = fopen('php://output', 'w');                              
			foreach ($data as $row) {
				@fputcsv($file, $row);              
			}
		fclose($file); 
		exit();
	}// end get_csv()

	public function uploadImage($fileData,$image_root_path){
		// print_r($fileData); die;
		$file_name			=	'';
		$extension 			=	$fileData->getClientOriginalExtension();
		$fileName			=	time().'-image.'.$extension;
		$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
		$folderPath			=	$image_root_path.$folderName;
		if(!File::exists($folderPath)) {
			File::makeDirectory($folderPath, $mode = 0777,true);
		}
		if($fileData->move($folderPath, $fileName)){
			$file_name	=	$folderName.$fileName;
		}
		return $file_name;
	}
	public function getStateList($id)
    {
        $states = DB::table("states")
        ->where("country_id",$id)
        ->pluck("name","id");
        return response()->json($states);
    }
    public function getCityList($id)
    {
        $cities = DB::table("cities")
        ->where("state_id",$id)
        ->pluck("name","id");
        return response()->json($cities);
	}
	public function createFolder($path){
		if(!File::exists($path)) {
			File::makeDirectory($path,$mode=0777,true);
		}
	}
	public function AnyFileUpload($file,$folder_name){
		$this->createFolder(public_path('uploads/'.$folder_name));    // calling function for creating folder.
		$extension		=	$file->getClientOriginalExtension();
		$filename 		= 	time().'_' .$folder_name.'.'.$extension;
		$upload_path 	=	public_path('uploads/'.$folder_name.DS);
		$path 			= 	$upload_path.$filename;
		$file->move($upload_path,$filename);
		return $filename;
	}
	public function FileUpload($file,$folder_name,$resize=true){
		$this->createFolder(public_path('uploads/'.$folder_name));    // calling function for creating folder.
		$resize_options = 	['50x50','60x60','128x128','250x250','70x70','350x250','500x450','500x500'];
		$extension		=	$file->getClientOriginalExtension();
		$filename 		= 	time().'_' .$folder_name.'.'.$extension;
		$upload_path 	=	public_path('uploads/'.$folder_name.DS);
		$path 			= 	$upload_path.$filename;
		\Image::make($file->getRealPath())->save($path);
		if($resize){
			// if image resize is true then upload image
			if(!empty($resize_options)){
				foreach($resize_options as $options){
					$dimensions 	= 	explode('x',$options);
					$resize_path 	= 	$upload_path.$options;
					$this->createFolder($resize_path);   // calling function for creating folder.
					$new_path 		=	$resize_path.DS.$filename;
					\Image::make($file->getRealPath())->resize($dimensions[0],$dimensions[1], function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					})->save($new_path);
				}
			}	
		}
		return $filename;
	}
	public function generateOtp(){
		$otp = 123456;
		return $otp;
	}
	public function FileUpload2($file,$folder_name,$extension){
		$this->createFolder(public_path('uploads/'.$folder_name));    // calling function for creating folder.
		$filename 		= 	time().'_' .$folder_name.'.'.$extension;
		$upload_path 	=	public_path('uploads/'.$folder_name.DS);
		$path 			= 	$upload_path.$filename;
		file_put_contents($path.'/'.$filename,$file);
		return $filename;
	}
	public function date_to_db_format($date){
		return date("Y-m-d H:i:s",strtotime(str_replace('/', '-', $date)));
	}
	function generateRandomString($length) {
		if($length == 3){
			$characters = '0123456789';
		}
		else{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function citySearchByTerm($type='city',$query='')
	{
		$return_arr		=	array();
		$url = "http://citypostsearch.intim.de/api/search?q=".$query;
		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		//"<pre>";print_r($result);
		if($result['error'] == false){
		  return $result;	
		}
		

	}

	function array_multi_unique($multiArray){

	  $uniqueArray = array();

	  foreach($multiArray as $subArray){

	    if(!in_array($subArray, $uniqueArray)){
	      $uniqueArray[] = $subArray;
	    }
	  }
	  return $uniqueArray;
	}

	public function get_ip_address(){
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	public function getGeoLocation(){
		$ch = curl_init();
		$ipaddress = $this->get_ip_address();
		curl_setopt($ch, CURLOPT_URL, 'https://api.ip2location.com/v2/?' . http_build_query([
			'ip'      => $ipaddress,
			'key'     => 'demo',	//VVH886EC4Q (live api key)
			'package' => 'WS25',
			'format'  => 'json',
			'addon'   => 'continent,country,region,city,geotargeting,country_groupings,time_zone_info',
			'lang'    => 'en',
		]));

		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$response = curl_exec($ch);
		return json_decode($response,true);
	}
}// end BaseController class