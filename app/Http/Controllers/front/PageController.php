<?php

/**
 * User Controller
 */
namespace App\Http\Controllers\front;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Redirect;
use View;
use Input;
use Validator;
use Hash;
use Session;
use CustomHelper;
use Auth;
use App\Model\Block;
use App\Model\Cms;
use Config;
use URL,Mail,DB;
use App\Model\Faq;
use App\Model\DropDown;
use App\Model\Package;
use App\Model\User;
use App\Model\Testimonial;
use App\Model\CustomerContact;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\FacebookSetting;
use App\Model\Earning;
use App\Model\ScheduleInterview;
use App\Model\ClientReview;



class PageController extends BaseController {
	
	public function cms($slug){
		/* $lang 				= 	App::getLocale();
		if(!in_array($lang,array('eng'))){
			$lang 			= 	'eng';
		}
		$language_id			=	DB::table("languages")->where("lang_code",$lang)->value("id"); */
		$language_id			=	1;
		$result    =	DB::table("cms_pages")->where("slug",$slug)->first();				
		return View::make('front.pages.cms', compact('result'));
	}

	/* public function Faqs()
	{
		$faqs = Faq::where('is_active', 1)->get();
		return View::make('front.pages.faqs', compact('faqs'));
		
	} */
	
	public function conatctUs()
	{
		return View::make('front.pages.conatct_us');
		
	}

	public function nannyListing(Request $request)
	{
		$searchData						=	$request->all();
		Session::put('nannay_search_data',$searchData);
		
		$zipcode = '';
		$sortBy = '';
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		$offset = 9;
		$DB		=	User::query();
		$totalJobs			=	$this->get_nannay_count($searchData);
		$lists =  $this->get_nannay_data($searchData,$limit=9,$offset=0);  

		//echo "<pre>";
		//print_r($lists);die; 		
		return View::make('front.pages.nanny_listing',compact('lists','zipcode','offset' , 'sortBy'));
	}

	public function get_nannay_data($keywordData = null,$limit=0,$offset=0)
	{
		$DB			=		User::query();
		$DB->select('users.*',DB::raw("(SELECT SUM(rating) as total_rating FROM client_reviews where nanny_id = users.id) as total_rating"), DB::raw("(SELECT COUNT(rating) as total_rating_count FROM client_reviews where nanny_id = users.id) as total_rating_count"));		
		$DB->where(['is_approved' => 1 ,  'is_deleted' => 0 , 'user_role_id' => NANNY_ROLE_ID ]);
		$default = DEFAULT_ZIPCODE;
		if(!empty($keywordData)){

			if(isset($keywordData["zipcode"])){
				$service_radius = 15;
				$location 		= $this->getLatLngFromZipCode($keywordData["zipcode"]);
				
				if(isset($location)){
					$my_lat 	= $location['zipLat'];
					$my_lang 	= $location['ziplng'];

					if(!empty($service_radius)){
						$DB->where(DB::raw("((3959 * acos( cos(radians(" . $my_lat . ") ) * cos(radians( IFNULL(users.latitude,0))) * cos( radians(IFNULL( users.longitude,0)  ) - radians(" . $my_lang . ") ) + sin(radians(" . $my_lat . ") ) * sin( radians( IFNULL(users.latitude,0) ) ) )) *1.60934)"),"<=" ,$service_radius);
					}
				}
				else{
					$DB->where("postcode","LIKE","%".$keywordData["zipcode"]."%");
				}
			}
			if(isset($keywordData["sort_by"]) && $keywordData["sort_by"] == 1){
				$sortBy = $keywordData["sort_by"];
				$location = $this->getGeoLocation();
				if(isset($location['zip_code'])){
					$my_lat = $location['latitude'];
					$my_lang = $location['longitude'];
					$DB->orderBy(DB::raw("((3959 * acos(cos(radians(".$my_lat.")) * cos(radians(users.latitude)) * cos( radians(users.longitude) - radians(".$my_lang . ") ) + sin( radians(" . $my_lat . ") ) * sin( radians( users.latitude ) ) )) *1.60934)"),'ASC');
				}else{
					$DB->orWhere("postcode", $default)->orderBy('id', 'desc');
				}
			}
			if(isset($keywordData["sort_by"]) && $keywordData["sort_by"] == 2){
				$sortBy = $keywordData["sort_by"];
				$DB->orderBy('created_at', 'desc');
			}
			if(isset($keywordData["sort_by"]) && $keywordData["sort_by"] == 3){

				$sortBy = $keywordData["sort_by"];
				$DB->inRandomOrder();
			}

			if(isset($keywordData["nanny_type"]) && $keywordData["nanny_type"] == 2){
				$DB->where("nanny_type", 2);
			}elseif(isset($keywordData["nanny_type"]) && $keywordData["nanny_type"] == 1){
				$DB->where("nanny_type", 1);
			}
			$DB->orderBy('created_at', 'desc');
		}
		$result		=	$DB->limit($limit)->offset($offset)->get();
		return $result;
	}


	public function get_nannay_count($keywordData = null){
		$DB		=	User::query();
		$DB->where(['is_approved' => 1 ,  'is_deleted' => 0 , 'user_role_id' => NANNY_ROLE_ID ]);
		$result		=	$DB->count();
		return $result;
	}



	public function nannyListLoadMore(Request $request){
		$searchData = 	Session::get('nannay_search_data');
		$limit  			= 	9;
		$offset 			= 	$request->get('offset') * $limit;
		$DB					=	new User();
		$lists			=	$this->get_nannay_data($searchData,$limit,$offset); 
		return View::make('front.pages.load_more_nannay' , compact('lists'));
	}

	

	public function nannyProfile(Request $request, $id)
	{
		if(!empty($id)){
			$id = base64_decode($id);
			$nannyProfile =  User::where([ 'is_approved' => 1 ,  'is_deleted' => 0, 'id' => $id ])->first();
			
			if(!empty($nannyProfile)){
				$limit  			= 	9;
				$offset 			= 	$request->get('offset') * $limit;
				$results=	ClientReview::leftJoin('users','client_reviews.user_id','users.id')->select('client_reviews.*','users.name','users.id as data_id', 'users.photo_id')->orderBy('client_reviews.created_at', 'DESC')->where('nanny_id', $id)->offset($offset)->limit($limit)->get();

				/*echo "<pre>";
				print_r($results);die;*/
				$totalRating = ClientReview::where('nanny_id', $id)->sum('rating');
				$averageRating = ClientReview::where('nanny_id', $id)->avg('rating');
				$totalRatingCount=ClientReview::where('nanny_id', $id)->count();
				$excellentRatingCount=ClientReview::where('nanny_id', $id)->where('rating',5)->count();
				$goodRatingCount=ClientReview::where('nanny_id', $id)->where('rating',4)->count();
				$averageRatingCount=ClientReview::where('nanny_id', $id)->where('rating',3)->count();
				$belowaverageRatingCount=ClientReview::where('nanny_id', $id)->where('rating',2)->count();
				$poorRatingCount=ClientReview::where('nanny_id', $id)->where('rating',1)->count();

				

				$nannyProfile->other_certificates =DB::table('user_certificates')->where(['user_id' =>  $nannyProfile->id ])->get();
				return View::make('front.pages.nanny-profile',compact('nannyProfile', 'results', 'averageRating', 'totalRatingCount', 'excellentRatingCount', 'goodRatingCount', 'averageRatingCount', 'belowaverageRatingCount', 'poorRatingCount', 'totalRating'));
			}
		}else{
			return Redirect::to('/')->with('error', trans('Sorry, you are using wrong link.'));
		}

	}

	public function aboutUs()
	{
		$aboutUs 		= Block::where('slug', 'about-us-page')->first();
		$testimomials 			= Testimonial::orderBy('id', 'desc')->limit(2)->get();
		$testimomialsHeading 	= Block::where('slug', 'what-parents-say')->first();
		return View::make('front.pages.about_us', compact('aboutUs','testimomials' , 'testimomialsHeading'));
	}


	public function pricing()
	{
		$pakages   	= Package::where('is_active', 1)->where('is_deleted', 0)->orderBy('order_type', 'asc')->get();
		$standard 	= CustomHelper::getmasterByType('standard ');
		$pro 		= CustomHelper::getmasterByType('pro');
		$advanced	= CustomHelper::getmasterByType('advanced');
		return View::make('front.pages.pricing', compact('pakages', 'standard', 'pro', 'advanced'));
	}


	public function termsAndConditions()
	{
		$terms 		= Cms::where('slug', 'terms-conditions')->first();
		return View::make('front.pages.terms_and_conditions',compact('terms'));
	}


	public function faqslist()
	{
		$faqs 		= Faq::where('is_active', '1')->orderBy('faq_order', 'asc')->get();
		return View::make('front.pages.faqs',compact('faqs'));
	}
	

	public function contactUsSend(Request $request){

		$thisData				=	$request->all();
		$request->replace($this->arrayStripTags($thisData));

		if(!empty($thisData)){
			Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'			        => 'required',
					'email' 				=> 'required|email',
					'subject' 			    => 'required',
					'message'				=> 'required',
					'g-recaptcha-response' => 'required|recaptcha'
				),	
				array(
					"name.required"			    =>	trans("The name field is required."),
					"email.required"		    =>	trans("The email field is required."),
					"email.email"			    =>	trans("The email is not valid email address."),
					"email.unique"			    =>	trans("The email must be unique."),
					"subject.required"		    => 	trans("The subject field is required"),
					"message.required"		    =>	trans("The message field is required"),
					'g-recaptcha-response.recaptcha' => trans("Captcha verification failed"),
					'g-recaptcha-response.required'  =>trans("Please complete the captcha"), 
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
				$obj 				     =  new CustomerContact;
				$obj->name 		    	 =  $request->input('name');
				$obj->subject 			 =  $request->input('subject');
				$obj->message 		     =  $request->input('message');
				$obj->email 		     =  $request->input('email'); 
				$obj->save();
				$userId					    =  $obj->id;
				if(!$userId){
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::to('/');
				} 
				$settingsEmail 			=	$request->input('email'); 
				$full_name				= 	$obj->name; 
				//$email					=   'floriyas@mailinator.com';
				$email					= 	Config::get('Site.contact_us_email');
				$emailActions			= 	EmailAction::where('action','=','user_contact')->get()->toArray();
				$emailTemplates			= 	EmailTemplate::where('action','=','user_contact')->get(array('name','subject','action','body'))->toArray();
				$cons 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($cons as $key => $val){
					$constants[] 		= 	'{'.$val.'}';
				} 

				$subject 				= 	$emailTemplates[0]['subject'];	
				$rep_Array 				= 	array($obj->name ,$obj->email,$obj->subject,$obj->message); 
				$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
				$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

				/*************************Thanks Mail*******************************/
				$fromsettingsEmail 		    =	Config::get('Site.to_email');
				$full_names				    = 	$obj->name; 
				//$toemail					=   $request->input('email');
				$toemail					= 	$request->input('email');
				$emailActionsThanks			= 	EmailAction::where('action','=','contact_thanks')->get()->toArray();
				$emailTemplatesThanks		= 	EmailTemplate::where('action','=','contact_thanks')->get(array('name','subject','action','body'))->toArray();
				$const 					= 	explode(',',$emailActions[0]['options']);
				$constants 				= 	array();
				foreach($const as $key => $val){
					$thanksconstants[] 		= 	'{'.$val.'}';
				} 

				$subject 				= 	$emailTemplatesThanks[0]['subject'];	
				$rep_Arrays 			= 	array($obj->name); 
				$messageBody			= 	str_replace($thanksconstants, $rep_Arrays, $emailTemplatesThanks[0]['body']);
				$mail					= 	$this->sendMail($toemail,$full_names,$subject,$messageBody,$fromsettingsEmail);

				/*************************Thanks Mail*******************************/

				Session::flash('success',trans("Thanks for contact with us"));
				return response()->json(['success' => true, 'page_redirect' => url('/')]);

			}

		}else{

			Session::flash('error', 'There is some problem. Please try after some time.');
			return response()->json(['success' => false, 'page_redirect' => url('/')]);
		}


	}



	public function getFacebookReview()
	{
		$data =  FacebookSetting::where('id', 1)->first();

		if(!empty($data)){
			$token = $data->token;;
			$app_id = $data->app_id;
			$app_id ='112191513987849';
			$url = "https://graph.facebook.com/v10.0/".$app_id."/ratings?access_token=".$token;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($response);

			echo "<pre>";
			print_r($response);
		}

		
	}

	public function testimonials()
	{
		$testimomials 			= Testimonial::orderBy('id', 'desc')->limit(5)->get();
		$testimomialsHeading 	= Block::where('slug', 'what-parents-say')->first();
		return View::make('front.pages.testimonials', compact('testimomials', 'testimomialsHeading'));
	}

	public function loadmoreTestimonials(Request $request)
	{
		$offset = $request->offset + 4;
		$offsetdb = $request->offset;
		$DB		=	Testimonial::query();
		//$DB->where(['is_deleted' => 0 ]);
		$testimonials = $DB->offset($offsetdb)->orderBy('created_at', 'desc')->limit(5)->get();
		$list_count   = count($testimonials) ;
		$output = '';
		if(count($testimonials) > 0){
			foreach($testimonials as $testimonialsk=>$testimonialsv){
				
				if($testimonialsk%2 == 0){
					$image = !empty($testimonialsv->image) ? WEBSITE_URL.'image.php?width=80px&height=80px&image='.$testimonialsv->image:WEBSITE_IMG_URL.'listing-img.jpg';
					$output .= '<div class="row align-items-center mb-md-5 mb-2">
					<div class="col-md-auto">
					<div class="py-2">
					<div class="img-wall text-center">
					<img src="'.$image.'" alt="">
					</div>


					<div class="text-center ">

					<h2> '.$testimonialsv->name.'</h2>
					<span> '.$testimonialsv->designation.'</span>
					</div>
					</div>
					</div>
					<div class="col">
					<div class="text-block pr-2">
					<div class="text-wall">
					<p>'.$testimonialsv->description.'”</p>
					</div>
					<div class="triangleone"></div>


					</div>
					</div>                    

					</div>';
				}else{
					$image = !empty($testimonialsv->image) ? WEBSITE_URL.'image.php?width=80px&height=80px&image='.$testimonialsv->image:WEBSITE_IMG_URL.'listing-img.jpg';
					$output .= '<div class="row align-items-center mb-md-5 mb-2">
					<div class="col-md-auto order-md-2">
					<div class="py-2">

					<div class="img-wall text-center">
					<img src="'.$image.'" alt="">
					</div>


					<div class="text-center ">

					<h2>'.$testimonialsv->name.'</h2>
					<span>'.$testimonialsv->designation.'</span>
					</div>
					</div>
					</div>
					<div class="col">
					<div class="text-block pr-2">
					<div class="text-wall">
					<p>'.$testimonialsv->description.'</p>
					</div>
					<div class="triangleright"></div>
					</div>
					</div>                    
					</div>';
				}
				
			}
			return response()->json(['success' => true, 'data' => $output , 'offset'=>$offset, 'list_count'=>$list_count]);
		}

	}

	public function thankYou()
	{
		return View::make('front.pages.thankyou');
	}

	public function myInvoice()
	{    
		if(Auth::user()){
			$Invoices =  Earning::where('user_id', Auth::user()->id)->leftJoin('users', 'users.id', '=', 'earnings.nanny_id')->where('status', 1)->select('earnings.*', 'users.name')->where('type', '1')->get();
			

			return View::make('front.dashboard.invoice.invoice', compact('Invoices'));	
		}else{
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
			return Redirect::to('/');
		}
	}
	public function meetingJoin($id)
	{

		if(Auth::user()){
			$id = \Crypt::decrypt($id);
			$result = ScheduleInterview::where('id', $id)->first();
			return View::make('front.dashboard.zoom', compact('result'));
		}else{
			Session::flash('error', 'Somthing went wrong. Please again after some time.');
			return Redirect::to('/');
		}

	}
	
}// end 