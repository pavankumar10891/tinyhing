<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\NewsLetter;
use App\Model\NewsLetterTemplate;
use App\Model\NewsLettersubscriber; 
use App\Model\Subscriber; 
use App\Model\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

/**
 * NewsLetter Controller
 *
 * Add your methods in the class below
 *
 * This file will render views from views/admin/newsletter
 */
 
class NewsLetterController extends BaseController {

	public $model		=	"newsletter";
	public $sectionName	=	"Newsletter Template's List";
	public $sectionNameSingular	= "News Letter";
	public $subscribersNameSingular	= "Newsletter Subscribers's List";
	public $scheduledNameSingular	= "Newsletter Scheduled List";
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		View::share('subscribersNameSingular',$this->subscribersNameSingular);
		View::share('scheduledNameSingular',$this->scheduledNameSingular);
		$this->request = $request;
	}
	/**
	 * Function for display all newslatter template
	 *
	 * @param null
	 *
	 * @return view page. 
	 */
	function newsletterTemplates(Request $request){ 
		$DB				=	NewsLetterTemplate::query();
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
		
		if ($request->all()) {
			$searchData	=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);
			
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['per_page'])){
				unset($searchData['per_page']);
			}
			
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
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
		return  View::make('admin.newsletter.newsletter_templates',compact('results','searchVariable','sortBy','order','query_string'));
	}// end newsletterTemplates()
	 
	/**
	 * Function for display page for add new newslatter template
	 *
	 * @param null
	 *
	 * @return view page. 
	 */
	function addTemplates(){ 
		return  View::make('admin.newsletter.add_newsletter_templates');
	}// end addTemplates()
		
		
/**
 * Function for save created template
 *
 * @param null
 *
 * @return redirect page. 
 */
	function saveTemplates(Request $request){
		$validator = Validator::make(
			$request->all(),
			array(
				'subject' 		=> 'required',
				'body' 			=> 'required'
			),
			array(
				"body.required"			=>	trans("The email body field is required."),
				"subject.required"		=>	trans("The subject field is required."),														
			)
		);
		
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/news-letter/add-template')->withErrors($validator)->withInput();
		}else{
			NewsletterTemplate::insert(array(
				'subject'  		=> 	$request->input('subject'),
				'body' 	   		=> 	$request->input('body'),
				'created_at' 	=>  DB::raw('NOW()'),
				'updated_at' 	=> 	DB::raw('NOW()')
			));
			
			Session::flash('flash_notice', trans("Your newsletter template has been saved successfully")); 
			return Redirect::to('adminpnlx/news-letter/newsletter-templates');
		}
	}
	
	/**
	 * Function for display page for edit newslatter template
	 *
	 * @param $Id as id of newslatter
	 *
	 * @return view page. 
	 */
		function editNewsletterTemplate($Id){ 
			$result		    =	NewsletterTemplate::find($Id);
			return  View::make('admin.newsletter.edit_newsletter_templates',compact('result'));
		}// end editNewsletterTemplate()
		
	/**
	 * Function for save updated newslatter
	 *
	 * @param $Id as id of newslatter
	 *
	 * @return redirect page. 
	 */
	function updateNewsletterTemplate($Id,Request $request){
		$validator = Validator::make(
			$request->all(),
			array(
				'subject' 	=> 'required',
				'body' 		=> 'required'
			),
			array(
				"body.required"			=>	trans("The email body field is required."),
				"subject.required"		=>	trans("The subject field is required."),														
			)
		);
		
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/news-letter/edit-newsletter-templates/'.$Id)->withErrors($validator)->withInput();
		}else{
				NewsletterTemplate::where('id', $Id)
				->update(array(
					'subject'  		=> 	$request->input('subject'),
					'body' 	   		=> 	$request->input('body'),
					'updated_at' 	=> 	DB::raw('NOW()')
				));
			Session::flash('flash_notice',trans("Newsletter template has been updated successfully")); 
			return Redirect::to('adminpnlx/news-letter/newsletter-templates');
		}
	}//end updateNewsletterTemplate()
		
	/**
	 * Function for send newslatter template
	 *
	 * @param $Id as id of newslatter
	 *
	 * @return view page. 
	 */
	function sendNewsletterTemplate($Id){			
		$subscriberArray	=	NewsLettersubscriber::where('status', '=', 1)->pluck('email','id'); 
		$result				=	NewsletterTemplate::find($Id); 
		return  View::make('admin.newsletter.send_newsletter_templates',compact('result','subscriberArray'));
	}// end sendNewsletterTemplate()
		
	/**
	 * Function for update send newslatter
	 *
	 * @param $Id as id of newslatter
	 *
	 * @return redirect page. 
	 */
	function updateSendNewsletterTemplate($Id,Request $request){
	
		$validator = Validator::make(
			$request->all(),
			array(
				'scheduled_time' 	=> 'required',
				'subject' 			=> 'required',
				'body' 				=> 'required'
			),
			array(
				'scheduled_time.required' 	=> trans("The scheduled date field is required."),
				'subject.required' 			=> trans("The subject field is required."),
				'body.required' 			=> trans("The body field is required."),
			)
		);
		
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/news-letter/send-newsletter-templates/'.$Id)->withErrors($validator)->withInput();
		}else{
				$newsLetterInsertId	=	NewsLetter::insertGetId(array(
					'scheduled_time'  		 => 	$request->input('scheduled_time'),
					'subject'  				 => 	$request->input('subject'),
					'body' 	   				 => 	$request->input('body'),
					'newsletter_template_id' => 	$Id,
					'status'				 => 	0,
					'created_at'			 => 	date('Y-m-d H:i:s'),
					'updated_at'			 => 	date('Y-m-d H:i:s')
				)); 
			
			if($request->input('newsletter_subscriber_id') == ''){
				$subscriberArray	=	NewsLettersubscriber::where('status', '=', 1)->pluck('email','id'); 
					foreach($subscriberArray as $to =>$email){
					Subscriber::insert(
						array(
							'newsletter_subscriber_id' 	=>  $to,
							'newsletter_id' 			=>  $newsLetterInsertId,
							'created_at'			 	=> 	date('Y-m-d H:i:s'),
							'updated_at'			 	=> 	date('Y-m-d H:i:s')
						));
				}
			}else{
				foreach($request->input('newsletter_subscriber_id') as $to=>$val){
					Subscriber::insert(
						array(
							'newsletter_subscriber_id' 	=>  $val,
							'newsletter_id' 			=>  $Id,
							'created_at'			 	=> 	date('Y-m-d H:i:s'),
							'updated_at'			 	=> 	date('Y-m-d H:i:s')
						));
				}   
			}
			Session::flash('flash_notice', trans("Newsletter has been sent successfully") ); 
			return Redirect::to('adminpnlx/news-letter/newsletter-templates');
		}
	}//end updateSendNewsletterTemplate()
		
		
/**
 * Function for display list of all newslatter subscriber
 *
 * @param null
 *
 * @return view page. 
 */
	function subscriberList(Request $request){  
		$DB				=	NewsLettersubscriber::query();
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
	
		if ($request->all()) {
			$searchData	=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);
			
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['per_page'])){
				unset($searchData['per_page']);
			}
			
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		
		
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
	    $order  = ($request->input('order')) ? $request->input('order')  : 'DESC';
		$results = $DB->orderBy($sortBy, $order)->where('status', '=', 1)->paginate(Config::get("Reading.records_per_page"));
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($request->all())->render();
		return  View::make('admin.newsletter.subscriber_list',compact('results','searchVariable','sortBy','order','query_string'));
	}// end subscriberList()
	
/**
 * Function for add subscriber
 *
 * @param null
 * 
 * @return view page. 
 */
	public function addSubscriber(Request $request){
		 
		if($request->isMethod('post')){
			
			$validator = Validator::make(
				$request->all(),
				array(
					'email' 				=> 'required|email|unique:newsletter_subscribers',
				),
				array(
					"email.required"		=>	trans("The email field is required."),
					"email.email"			=>	trans("The email is not valid email address."),
					"email.unique"			=>	trans("This email is already exists.")
				)
			);
		
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$encId			=	md5(time() . $request->input('email'));
				NewsLettersubscriber::insert(array(
						'email'	  		=>  $request->input('email'),
						'is_verified' 	=>  1,
						'status' 		=>  1,
						'enc_id' 		=>  $encId,
						'created_at' 	=>  date("Y-m-d H:i:s"),
						'updated_at' 	=>  date("Y-m-d H:i:s")
					));
				Session::flash('flash_notice', trans("Subscriber added successfully")); 
				return Redirect::to('adminpnlx/news-letter/subscriber-list');
			}
		}
		return  View::make('admin.newsletter.add_subscriber');
	}//end addSubscriber()
	
/**
 * Function for delete newslatter subscriber
 *
 * @param $Id as subscriber id
 *
 * @return redirect page. 
 */
	function subscriberDelete($Id){
		$userId	=	NewsLettersubscriber::where('id', '=', $Id)->pluck('user_id');
		NewsLettersubscriber::where('id', '=', $Id)->delete();
		Session::flash('flash_notice', trans("Newsletter subscriber deleted successfully") );
		return Redirect::to('adminpnlx/news-letter/subscriber-list');
	}//end subscriberDelete()
	
	/**
	 * 
	 * Function for display all newslatter template
	 *
	 * @param null
	 *
	 * @return view page. 
	 */
	public function listTemplate(Request $request) { 			
		$DB				=	NewsLetter::query();
		$searchVariable	=	array(); 
		$inputGet		=	$request->all();
		
		if ($request->all()) {
			$searchData	=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);
			
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['per_page'])){
				unset($searchData['per_page']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		
		
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'updated_at';
	    $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		
		$results = $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($request->all())->render();
		return  View::make('admin.newsletter.index',compact('results','searchVariable','sortBy','order','query_string'));
	}// end listTemplate()
/**
 * Function for display page  for edit newslatter template
 *
 * @param null
 *
 * @return view page. 
 */
	function editTemplate($Id){
		$result						=	Newsletter::find($Id);
		$newsletter_template_id		=	$result->newsletter_template_id;
		
		$allReadySubscriberArray	=	NewsLettersubscriber::
										where('status', '=', 1)->
										whereIn('id',
								function($query) use ($newsletter_template_id)
									{
										$query->select('newsletter_subscriber_id')
											  ->from('subscribers')
											  ->whereRaw('subscribers.newsletter_id = '.$newsletter_template_id);
									})->
										pluck('email','id'); 
		
		$subscriberArray			=	NewsLettersubscriber::where('status', '=', 1)->pluck('email','id'); 
		
		return  View::make('admin.newsletter.edit',compact('result','subscriberArray','allReadySubscriberArray'));
	}//end editTemplate()
	
/**
 * Function for save updated newslatter template
 *
 * @param $Id as id of template 
 *
 * @return redirect page. 
 */
	function updateTemplate($Id,Request $request){
		$validator = Validator::make(
			$request->all(),
			array(
				'scheduled_time' 			=> 'required',
				'subject' 					=> 'required',
				// 'newsletter_subscriber_id' 	=> 'required',
				'body' 						=> 'required'
			),
			array(
				// 'newsletter_subscriber_id.required' => 'The newsletter subscribers field is required.'
			)
		);
		
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/news-letter/edit-template/'.$Id)->withErrors($validator)->withInput();
		}else{
			
			$subscriberArray	=	NewsLettersubscriber::where('status', '=', 1)->pluck('email','id'); 
			
			NewsLetter::where('id','=',$Id)
				->update(array(
					'scheduled_time'  		 => 	$request->input('scheduled_time'),
					'subject'  				 => 	$request->input('subject'),
					'body' 	   				 => 	$request->input('body'),
					'newsletter_template_id' => 	$Id,
					'status'				 => 	0
				));
			
			Subscriber::where('newsletter_id', '=', $Id)->delete();
			
			if($request->input('newsletter_subscriber_id') == ''){
				 foreach($subscriberArray as $to =>$email){
					Subscriber::insert(
						array(
							'newsletter_subscriber_id' =>  $to,
							'newsletter_id' =>  $Id
						));
				}
			}else{
				foreach($request->input('newsletter_subscriber_id') as $to){
					Subscriber::insert(
						array(
							'newsletter_subscriber_id' =>  $to,
							'newsletter_id' =>  $Id
						));
				}   
			}
			Session::flash('flash_notice',trans("Newsletter has been updated successfully")); 
			return Redirect::to('adminpnlx/news-letter');
		}
	}// end updateTemplate()
	
	
	/*Function for export all products*/	
	public function export_all_subscribers(){ 
		$DB 				= 	NewsLettersubscriber::query();
		$allSubscriber 		= 	 $DB->where('newsletter_subscribers.is_verified',1)
								->select("newsletter_subscribers.*")
								->get()
								->toArray(); 
		$thead[] = array('Email','Created Date');
		if(!empty($allSubscriber)){
			foreach($allSubscriber as $result){
				// $name					=	!empty($result['name']) ? $result['name'] : '';
				$email					=	!empty($result['email'])? $result['email'] : '';
				$created_at				=	!empty($result['created_at'])? $result['created_at'] : '';
				$thead[] 				= 	array($email,date("m/d/Y",strtotime($created_at)));
			}
			$this->get_csv($thead,'export_subscribers_reports');
			session::forget('result');
		}else{
			Session::flash('flash_notice', 'Sorry no report found.'); 
			return Redirect::to('admin/order');
		} 
		
	}// end export_all_products()		
}// end NewsLetterController class
