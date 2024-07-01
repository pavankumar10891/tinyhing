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

class PageController extends BaseController {
	
	public function cms($slug){
		/* $lang 				= 	App::getLocale();
		if(!in_array($lang,array('eng'))){
			$lang 			= 	'eng';
		}
		$language_id			=	DB::table("languages")->where("lang_code",$lang)->value("id"); */
		$language_id			=	1;
		$result    =	DB::table("cms_pages")
						->where("is_active",1)
						->where("slug",$slug)
						->select(
							DB::raw("(SELECT source_col_description FROM cms_page_descriptions WHERE foreign_key = cms_pages.id AND language_id = $language_id and source_col_name='title') as title"),
							DB::raw("(SELECT source_col_description FROM cms_page_descriptions WHERE foreign_key = cms_pages.id AND language_id = $language_id and source_col_name='body') as body")
						)
						->first();
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

	public function nannyListing(Request $request){
		
		$zipcode = '';
		$request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		$offset = 9;
		$DB		=	User::query();
		$DB->where(['verified' => '1', 'is_approved' => 1 ,  'is_deleted' => 0 , 'user_role_id' => NANNY_ROLE_ID ]);
		
		if(!empty($formData)){
			$zipcode   = $request->input('zipcode');
			if($zipcode!=''){
				$DB->where("postcode", $request->input('zipcode'));
			}
			
	     }
     	  $lists = $DB->limit(9)->get();	
	//	dd($list);	
		return View::make('front.pages.nanny_listing',compact('lists','zipcode','offset'));
	  }

	  public function nannyListLoadMore(Request $request){
		   //dd($request);
        $output = '';
		$offset =  $request->offset + 9;
        $offsetdb = $request->offset;
		$DB		=	User::query();
		$zipcode   = $request->input('zipcode');
		if($zipcode!=''){
			$DB->where("postcode", $request->input('zipcode'));
		}
		$DB->where(['verified' => '1', 'is_approved' => 1 ,  'is_deleted' => 0 , 'user_role_id' => NANNY_ROLE_ID ]);
		$lists = $DB->offset($offsetdb)->limit(9)->get();
		$list_count   = count($lists) ; 
		if(count($lists) > 0){

		foreach($lists as $listsk=>$listsv){
		$output .='<a href="'. route('user.nanny.profile', base64_encode($listsv->id)).'">
		<div class="col-sm-6 col-lg-4 mb-md-5 mb-4">
			<div class="bg-white mr-md-auto">';
	     $image =  !empty($listsv->photo_id) ? CERTIFICATES_AND_FILES_URL.$listsv->photo_id: WEBSITE_IMG_URL.'listing-img.jpg'; 
         $fimage = WEBSITE_URL.'image.php?width=253px&height=226px&cropratio=3:2&image='.$image;
	    
		 $output .='<div class="img-wall" style="background-image: url('.$fimage.')">
		
				<img src="'.$fimage.'" class="w-100" alt="">
				
			</div>
			<div class="text-block">
				<div class="d-flex align-items-center">';

				if(!empty($listsv->name)){
					$output .='<h3>'.$listsv->name.'</h3> '; 
				}else{

					$output .='<h3></h3> '; 
				}
				$output .='<div class="rating-block">
						<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star"
							role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
							class="svg-inline--fa fa-star fa-w-18 fa-2x">
							<path fill="currentColor"
								d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
								class=""></path>
						</svg>
						4.3
					</div></div>
					<ul class="">
						<li> <label>age:</label>';
						if(!empty($listsv->age)){
							$output .='<strong>'.$listsv->age.'<strong> </li>'; 
						}else{
		
							$output .='<strong>'.$listsv->age.'<strong> </li>'; 
						}

						$output .='<li> <label>Exp:</label>';
						if(!empty($listsv->experience)){
							$output .='<strong>'.$listsv->experience.'<strong> </li></ul>'; 
						}else{
		
							$output .='<strong>'.$listsv->experience.'<strong> </li></ul>'; 
						}
						
						$output .='<div class="">';
						if(!empty($listsv->experience)){
							$output .='<p>'.$listsv->description.'</p>'; 
						}else{
		
							$output .='<p></p>';  
						}
						$output .='	</div>
						</div>
				
			<div class="btn-block mt-1 text-center">
				<a href="javascript:void(0);" class="btn-theme mw-100">
					Schedule Interview
				</a>
			</div>
		</div>
		</div>
		</a>';
		       }

					return response()->json(['success' => true, 'data' => $output , 'offset'=>$offset, 'list_count'=>$list_count]);
				}else{

					return response()->json(['success' => true, 'data' => '' , 'offset'=>$offset]);
				}
		  	
	      }
	

 	public function nannyProfile($id)
 	{
 		if(!empty($id)){
 		  $id = base64_decode($id);
 		  $nannyProfile =  User::where(['verified' => '1', 'is_approved' => 1 ,  'is_deleted' => 0, 'id' => $id ])->first();
 		  if(!empty($nannyProfile)){
			$nannyProfile->other_certificates =DB::table('user_certificates')->where(['user_id' =>  $nannyProfile->id ])->get();
 		  	return View::make('front.pages.nanny-profile',compact('nannyProfile'));
 		  }
 		}else{
 			return Redirect::to('/')->with('error', trans('Sorry, you are using wrong link.'));
 		}

 	}

	 public function aboutUs()
 	{
	    $aboutUs 		= Block::where('slug', 'about-us')->first();
		$testimomials 			= Testimonial::orderBy('id', 'desc')->limit(2)->get();
		$testimomialsHeading 	= Block::where('slug', 'what-parents-say')->first();
		return View::make('front.pages.about_us', compact('aboutUs','testimomials' , 'testimomialsHeading'));
	}


	public function pricing()
 	{
		return View::make('front.pages.pricing');
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

			$validator 					=	Validator::make(
				$request->all(),
				array(
					'name'			        => 'required',
					'email' 				=> 'required|email',
					'subject' 			    => 'required',
					'message'				=> 'required',
				),	
				array(
					"name.required"			    =>	trans("The name field is required."),
					"email.required"		    =>	trans("The email field is required."),
					"email.email"			    =>	trans("The email is not valid email address."),
					"email.unique"			    =>	trans("The email must be unique."),
					"subject.required"		    => 	trans("The subject field is required"),
					"message.required"		    =>	trans("The message field is required"),
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



	


	
	
}// end 