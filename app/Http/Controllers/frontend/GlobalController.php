<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB,Str,Session,Auth,View,Config,Validator;
use App\Models\GeoLocationCode;
use App\Models\User;
use GuzzleHttp;
use Hash,Mail,File;
use Yoti\DocScan\DocScanClient;
use Yoti\Sandbox\DocScan\Request\Check\Report\SandboxBreakdownBuilder;
use Yoti\Sandbox\DocScan\Request\Check\Report\SandboxRecommendationBuilder;
use Yoti\Sandbox\DocScan\Request\Check\SandboxDocumentAuthenticityCheckBuilder;
use Yoti\Sandbox\DocScan\Request\Check\SandboxDocumentFaceMatchCheckBuilder;
use Yoti\Sandbox\DocScan\Request\Check\SandboxDocumentTextDataCheckBuilder;
use Yoti\Sandbox\DocScan\Request\Check\SandboxZoomLivenessCheckBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxCheckReportsBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxResponseConfigBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxTaskResultsBuilder;
use Yoti\Sandbox\DocScan\Request\Task\SandboxDocumentTextDataExtractionTaskBuilder;
use Yoti\Sandbox\DocScan\SandboxClient;
use Yoti\DocScan\Session\Create\Check\RequestedDocumentAuthenticityCheckBuilder;
use Yoti\DocScan\Session\Create\Filters\RequiredIdDocumentBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedFaceMatchCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedLivenessCheckBuilder;
use Yoti\DocScan\Session\Create\SdkConfigBuilder;
use Yoti\DocScan\Session\Create\SessionSpecificationBuilder;
use Yoti\DocScan\Session\Create\Task\RequestedTextExtractionTaskBuilder;
use Yoti\DocScan\Session\Create\NotificationConfigBuilder;
use Yoti\Http\RequestBuilder;
use Yoti\Http\Payload;
// use GuzzleHttp\Psr7\stream_for;
class GlobalController extends BaseController
{
    public function getLocationByLatlong(Request $request)
    {
        $my_lat     = $request->latitude;
        $my_lang    =  $request->longitude;
        $searchtext  =$request->searchtext;

        Session::put('user_lat_long', array($my_lat,$my_lang));
        $service_radius = 50;
        $postalCode = ''; 
        if(!empty($my_lang) && !empty($my_lang))
        {
           $searchData =  DB::table("geo_location_codes")->select('postal_code', 'location_name')->where(DB::raw("((3959 * acos( cos(radians(" . $my_lat . ") ) * cos(radians( IFNULL(location_lat,0))) * cos( radians(IFNULL( location_lon,0)  ) - radians(" . $my_lang . ") ) + sin(radians(" . $my_lat . ") ) * sin( radians( IFNULL(location_lat,0) ) ) )))"),">=" ,$service_radius)->groupBy('postal_code','location_name')->first();
          
        }
        
        if(!empty($searchData)){
            $location_name = Str::slug($searchData->location_name);
            $postalCode     = $searchData->postal_code.'-'.$location_name;
        }else{
             $postalCode    = '';
        }
        return  $postalCode;

    }

    public function removeAllCookie(Request $request)
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
            return true;
        }
        return false;
    }

    public function logout()
    {
        
        Auth::guard('web')->logout();
        return redirect('/');
    }

    public function myAccount()
    {
        $locations = GeoLocationCode::pluck('location_name', 'location_id')->toArray();
        $locations = array_filter(array_unique($locations));
        $postal_code = GeoLocationCode::pluck('postal_code', 'postal_code')->toArray();
        $postal_code = array_unique($postal_code);
        return View::make('frontend.myaccount.account', compact('locations', 'postal_code'));
    }

    public function personalDetail(Request $request)
    {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'company'           => 'required',
                    'salutation'        => 'required',
                    'last_name'         => 'required',
                    'first_name'        => 'required',
                    'street'            => 'required',
                    'house_no'          => 'required',
                    'postal_code'       => 'required',
                    'location'          => 'required',
                    'about_me'          => 'required',
                    'location'          => 'required',
                    //'email'            => 'required|email',
                    //'mobile_number'     => 'required',
                    //'mobile_number'     => 'required',
                ),
                array(
                    'company.required'          => 'This field is required',
                    'salutation.required'       => 'This field is required',
                    'last_name.required'        => 'This field is required',
                    'first_name.required'       => 'This field is required',
                    'street.required'           => 'This field is required',
                    'house_no.required'         => 'This field is required',
                    'postal_code.required'      => 'This field is required',
                    'about_me.required'         => 'This field is required',
                    'location.required'         => 'This field is required',
                    //'email.required'    => 'Please enter the email',
                    //'email.email'       => 'Please enter the valid email',
                    )
            );

                if ($validator->fails()){
                    $errors = [];
                    $msgArr = (array) $validator->messages();

                    $msgArr = array_shift($msgArr);

                    $count = 0;

                    foreach($msgArr as $key=>$val) {
                        $errors[$key."_error"] = array_shift($val);
                        $count++;
                    }
                    return response()->json(['success' => false, 'errors' => $errors]);
                }else{
                   
                    $user = User::find(Auth::user()->id);
                    $user->company      = $request->input('company');
                    $user->salutation   = $request->input('salutation');
                    $user->last_name    = $request->input('last_name');
                    $user->first_name   = $request->input('first_name');
                    $user->street       = $request->input('street');
                    $user->house_no     = $request->input('house_no');
                    $user->post_code    = $request->input('postal_code');
                    $user->location_id  = $request->input('location');
                    $user->about_me     = $request->about_me;
                    $user->save();
                    Session::flash('success', 'Persanal details change successfully');
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'Persanal details change successfully']);
                   
                }

        }
    }

    public function changePassword(Request $request)
    {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'old_password'              => 'required|min:6',
                    'password'                  => 'required|min:6',
                    'confirm_password'          => 'required|min:6|same:password',
                    'old_password'              => ['required', function ($attribute, $value, $fail) {
                        if (!\Hash::check($value, Auth::user()->password)) {
                            return $fail(__('The current password is incorrect.'));
                        }
                    }],
                ),
                array(
                    'old_password.required'             => 'This field is required',
                    'password.required'                 => 'This field is required',
                    'confirm_password.required'         => 'This field is required',
                    )
            );

                if ($validator->fails()){
                    $errors = [];
                    $msgArr = (array) $validator->messages();

                    $msgArr = array_shift($msgArr);

                    $count = 0;

                    foreach($msgArr as $key=>$val) {
                        $errors[$key."_error"] = array_shift($val);
                        $count++;
                    }
                    return response()->json(['success' => false, 'errors' => $errors]);
                }else{

                    $user = User::find(Auth::user()->id);
                    $user->password      = Hash::make($request->input('password'));
                    $user->save();
                     Session::flash('success', 'Password change successfully');
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'password change successfully']);
                   
                }

        }
    }

    public function userNotification(Request $request)
    {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'notification'                    => 'required|min:1', 
                    'notification.*'                  => 'required|min:1'
                ),
            );

                if ($validator->fails()){
                    $errors = [];
                    $msgArr = (array) $validator->messages();

                    $msgArr = array_shift($msgArr);

                    $count = 0;

                    foreach($msgArr as $key=>$val) {
                        $errors[$key."_error"] = array_shift($val);
                        $count++;
                    }
                    return response()->json(['success' => false, 'errors' => $errors]);
                }else{
                    $user = User::find(Auth::user()->id);
                    $user->notification      = '';
                    $user->notification      = implode(',', $request->input('notification'));
                    $user->save();
                    Session::flash('success', 'Notification update successfully');
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'Notification update successfully']);
                   
                }

        }
    }

     public function changeEmail(Request $request)
    {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'new_email'            => 'required|email',
                ),
                array(
                    'new_email.required'    => 'Please enter the email',
                    'new_email.email'       => 'Please enter the valid email',
                    )
            );

                if ($validator->fails()){
                    $errors = [];
                    $msgArr = (array) $validator->messages();

                    $msgArr = array_shift($msgArr);

                    $count = 0;

                    foreach($msgArr as $key=>$val) {
                        $errors[$key."_error"] = array_shift($val);
                        $count++;
                    }
                    return response()->json(['success' => false, 'errors' => $errors]);
                }else{
                    $url = md5(time().$request->input('new_email'));
                    $email = $request->new_email;
                    $user = User::find(Auth::user()->id);
                    $user->validate_string = $url;
                    $user->save();
                    
                    $rd = '<a href="'.url('/account-verify/'.$email.'/'.$url).'">Click here</a>';
                    $message = 'We have sent email, please varify your email address '.$email.'/'.$rd.''; 
                    Mail::send('emails.template', ['content' => $message], function($message) use($email) {
                        $message->from('no-reply@intim.de', 'intim.de');
                        $message->to($email);
                        $message->subject('Email Varification');
                        // $message->attachData($pdf->output(),'customer.pdf');
                    });
                     Session::flash('success', 'We have sent email, please varify your email address');
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'We have sent email, please varify your email address']);
                   
                }

        }
    }

    public function profileImage(Request $request)
    {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();

        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ),
            );

                if ($validator->fails()){
                    $errors = [];
                    $msgArr = (array) $validator->messages();

                    $msgArr = array_shift($msgArr);

                    $count = 0;

                    foreach($msgArr as $key=>$val) {
                        $errors[$key."_error"] = array_shift($val);
                        $count++;
                    }
                    return response()->json(['success' => false, 'errors' => $errors]);
                }else{

                    $user = User::find(Auth::user()->id);
                    if($request->hasFile('image')){ 
                        $extension      =   $request->file('image')->getClientOriginalExtension();
                        $fileName       =   time().'-image-id.'.$extension;
                        $folderName     =   strtoupper(date('M'). date('Y'))."/";
                        $folderPath     =   USER_IMAGE_ROOT_PATH.$folderName;
                        if(!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, $mode = 0777,true);
                        }
                        if($request->file('image')->move($folderPath, $fileName)){
                            $user->face_image_path =    $folderName.$fileName;
                        }
                    }

                    $user->save();
                    Session::flash('success', 'Image update successfully');
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'Image update successfully']);
                }
        }
    }
    public function linkDetail($slug){
        echo $slug; die;
    }
    public function verifyIdentity(){
		
		$origin         = "https://api.yoti.com/sandbox";  
        $api_url        = "https://api.yoti.com/sandbox/idverify/v1";
        $YOTI_CLIENT_SDK_ID = Config::get('values.YOTI_SDK_ID');
        $YOTI_PEM = ROOT.DS.Config::get('values.YOTI_KEY_FILE_PATH'); //echo $YOTI_CLIENT_SDK_ID; die;
        $docScanClient  = new DocScanClient($YOTI_CLIENT_SDK_ID, $YOTI_PEM,['api.url' => $api_url]);
        $body = [
            "type"=> "OVER",
            "age_estimation"=> [
            "threshold"=> "19",
            "allowed"=> "true",
            "level"=> "NONE"
            ],
            "digital_id"=> [
            "threshold"=> "30",
            "allowed"=> "false",
            "level"=> "NONE"
            ],
            "doc_scan"=> [
            "threshold"=> "18",
            "allowed"=> "true",
            "level"=> "NONE"
            ],
            "ttl"=> "300000000",
            "reference_id"=> Auth::user()->id,
            "callback_url"=> route("user.account"),
            "notification_url"=> route("user.verifyAge")
        ];
        /* NO USE OF THIS
       $request = (new RequestBuilder())
            ->withBaseUrl('https://age.yoti.com/api/v1/')
            ->withPemFilePath($YOTI_PEM)
            ->withEndpoint('/age-antispoofing')
            ->withPayload(Payload::fromJsonData($body)) // For version < 3, use ->withPayload(new Payload($img))
            ->withMethod('POST')
            ->withHeader('X-Yoti-Auth-Id', $YOTI_CLIENT_SDK_ID)
            ->build();

        // Execute request
        $response = $request->execute();
        // echo "<pre>"; print_r($response); die;
        // Get response body
        $body = $response->getBody();
        echo "<pre>"; print_r($body); die; /




/*

            $client = new \GuzzleHttp\Client();
            
            $response = $client->request('POST', 'https://api.yoti.com/sandbox/sessions/v1', [
            'headers' => ['Content-Type' => 'application/json', 'Authorization' => "Bearer ". $YOTI_CLIENT_SDK_ID], 
            'body' => json_encode($body)
            ]);
            print_r($response); die;
*/      $origin         = "https://api.yoti.com/sandbox";  
        $api_url        = "https://api.yoti.com/sandbox/idverify/v1";
        $sandboxClient  = new SandboxClient($YOTI_CLIENT_SDK_ID, $YOTI_PEM);

        // to determine the validity of the ID document
        $documentAuthenticityCheck = (new RequestedDocumentAuthenticityCheckBuilder())->build();
        // required document
        $requiredDocument = (new RequiredIdDocumentBuilder())->build();
        // $faceMatchCheck =   (new SandboxDocumentFaceMatchCheckBuilder())->build();
        // print_r($requiredDocument); die;
        // $anotherRequiredDocument = (new RequiredIdDocumentBuilder())->build();
        $lang = Config::get('app.fallback_locale');
        if(Session::has('locale')){
            $lang        =       Session::get('locale');
        }
        // echo "<pre>"; print_r(Auth::user());
        // echo $lang; die;
        $sdkConfig = (new SdkConfigBuilder())
        ->withAllowsCameraAndUpload()
        ->withPrimaryColour('#2d9fff')
        ->withSecondaryColour('#FFFFFF')
        ->withFontColour('#FFFFFF')
        ->withLocale($lang.'-GB')
        ->withPresetIssuingCountry('GBR')
        ->withSuccessUrl(route("user.verifyAge"))
        ->withErrorUrl(route("user.account"))
        ->build();
        $notificationConfig = (new NotificationConfigBuilder())
                            ->withEndpoint(route("user.verifyData"))
                            ->withAuthToken('username:password')
                            ->forResourceUpdate()
                            ->forTaskCompletion()
                            ->forCheckCompletion()
                            ->forSessionCompletion()
                            ->build();

        $sessionSpec = (new SessionSpecificationBuilder())
            ->withRequestedCheck($documentAuthenticityCheck)
            ->withRequiredDocument($requiredDocument)
            ->withClientSessionTokenTtl(600)
            ->withResourcesTtl(90000)
            ->withUserTrackingId('some-user-tracking-id')
        // ->withRequestedCheck($livenessCheck)
            // ->withRequestedCheck($faceMatchCheck)
        // ->withRequestedTask($textExtractionTask)
            ->withSdkConfig($sdkConfig)
            ->withNotifications($notificationConfig)
            ->withBlockBiometricConsent(false)
            ->build();
        // echo "<pre>"; print_r($docScanClient); die;
        $session                = $docScanClient->createSession($sessionSpec);
        // echo "<pre>"; print_r($session); die;
        $clientSessionTokenTtl  =   $session->getClientSessionTokenTtl();
        $clientSessionToken     =   $session->getClientSessionToken();
        $sessionId              =   $session->getSessionId();
        
		Session::put('sessionId', $sessionId);
        $userView               =   $api_url."/web/index.html?sessionID={$sessionId}&sessionToken={$clientSessionToken}";
        /*
        $documentAuthenticityCheckConfig = (new SandboxDocumentAuthenticityCheckBuilder())
            ->withRecommendation(
                (new SandboxRecommendationBuilder())->withValue('APPROVE')->build()
            )
            ->withBreakdown(
                (new SandboxBreakdownBuilder())
                    ->withSubCheck('document_in_date')
                    ->withResult('PASS')
                    ->build()
            )
            ->build();

        $textDataCheckConfig = (new SandboxDocumentTextDataCheckBuilder())
            ->withRecommendation(
                (new SandboxRecommendationBuilder())->withValue('APPROVE')->build()
            )
            ->withBreakdown(
                (new SandboxBreakdownBuilder())
                    ->withSubCheck('text_data_readable')
                    ->withResult('PASS')
                    ->build()
            )
            ->withDocumentFields([
                'full_name' => 'John Doe',
                'nationality' => 'GBR',
                'date_of_birth' => '1986-06-01',
                'document_number' => '123456789',
            ])
            ->build();

        $livenessCheckConfig = (new SandboxZoomLivenessCheckBuilder())
            ->withRecommendation(
                (new SandboxRecommendationBuilder())->withValue('APPROVE')->build()
            )
            ->withBreakdown(
                (new SandboxBreakdownBuilder())
                    ->withSubCheck('liveness_auth')
                    ->withResult('PASS')
                    ->build()
            )
            ->build();

        $faceMatchCheckConfig = (new SandboxDocumentFaceMatchCheckBuilder())
            ->withRecommendation(
                (new SandboxRecommendationBuilder())->withValue('APPROVE')->build()
            )
            ->withBreakdown(
                (new SandboxBreakdownBuilder())
                    ->withSubCheck('ai_face_match')
                    ->withResult('PASS')
                    ->withDetail('confidence_score', '0.81')
                    ->build()
            )
            ->build();

        $textExtractionConfig = (new SandboxDocumentTextDataExtractionTaskBuilder())
            ->withDocumentFields([
                'full_name' => 'John Doe',
                'nationality' => 'GBR',
                'date_of_birth' => '1986-06-01',
                'document_number' => '123456789',
            ])
            ->build();

        $checkReportsConfig = (new SandboxCheckReportsBuilder())
            ->withAsyncReportDelay(5)
            ->withDocumentAuthenticityCheck($documentAuthenticityCheckConfig)
            ->withDocumentTextDataCheck($textDataCheckConfig)
            ->withLivenessCheck($livenessCheckConfig)
            ->withDocumentFaceMatchCheck($faceMatchCheckConfig)
            ->build();

        $taskResultsConfig = (new SandboxTaskResultsBuilder())
            ->withDocumentTextDataExtractionTask($textExtractionConfig)
            ->build();

        $responseConfig = (new SandboxResponseConfigBuilder())
            ->withCheckReports($checkReportsConfig)
            ->withTaskResults($taskResultsConfig)
            ->build();

        $response = $sandboxClient->configureSessionResponse($sessionId, $responseConfig); */
        return View::make('frontend.myaccount.verify_identity', compact('userView','sessionId','clientSessionToken','origin'));
        echo $userView; die;
        echo $sessionId; die;
        // echo "<pre>"; print_r($response);  die;
        // https://api.yoti.com/sandbox/idverify/v1/web/index.html?sessionID=<inputsessionID>&sessionToken=<yoursessionToken>
    }
    /** Function to Verify Age 
     * 
     * @param null
     * 
     * return null
    */
    public function verifyAge(){
        if(Auth::check()){
			
			$sessionId	=	Session::get('sessionId');
            $YOTI_CLIENT_SDK_ID = Config::get('values.YOTI_SDK_ID');
            $api_url        = "https://api.yoti.com/sandbox/idverify/v1";
            $YOTI_PEM = ROOT.DS.Config::get('values.YOTI_KEY_FILE_PATH'); //echo $YOTI_CLIENT_SDK_ID; die;
            $docScanClient  = new DocScanClient($YOTI_CLIENT_SDK_ID, $YOTI_PEM,['api.url' => $api_url]);
            $sessionResult = $docScanClient->getSession($sessionId);
                // Returns the session state
                $state = $sessionResult->getState();
				print_R($state);die;
                // Returns session resources
                $resources = $sessionResult->getResources();
                $resources;
                // Returns all checks on the session
                $checks = $sessionResult->getChecks();
                print_r($checks);
                // Return specific check types
                $authenticityChecks = $sessionResult->getAuthenticityChecks();
                 print_r($authenticityChecks);
                $faceMatchChecks = $sessionResult->getFaceMatchChecks();
                 print_r($faceMatchChecks);
                $textDataChecks = $sessionResult->getTextDataChecks();
                 print_r($textDataChecks);
                $livenessChecks = $sessionResult->getLivenessChecks();
                 print_r($livenessChecks);

            
            // $update_status = User::where('id',Auth::user()->id)->update(['can_see_fsk18'=>true]);
        }
    }
    public function verifyData(Request $request){
    //     $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    // $txt = "John Doe\n";
    // fwrite($myfile, $txt);
    // $txt = "Jane Doe\n";
    // fwrite($myfile, $txt);
    // fclose($myfile);
        file_put_contents(__DIR__."/response.txt",json_encode($request));
        echo 222; die;
    }
}
// UPDATE `users` SET `can_see_fsk18` = 2 WHERE `users`.`can_see_fsk18` = Null;