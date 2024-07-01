<?php
DB::enableQueryLog() ;
include(app_path().'/global_constants.php');
include(app_path().'/settings.php');
require_once(APP_PATH.'/libraries/CustomHelper.php');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/base/uploder','BaseController@saveCkeditorImages');
Route::post('/base/uploder','BaseController@saveCkeditorImages'); 


###################################### Front Routing start here ############################################

Route::group(array('namespace'=>'front'), function() {
	Route::get('register-stripe-account','GlobalUsersController@registeredStripAccount')->name('user.registerStripe');
	Route::any('disconnect-stripe-account','GlobalUsersController@disconnectStripeAccount')->name('user.disConnectStripe');
});

Route::group(array('middleware' => 'App\Http\Middleware\GuestFront','namespace'=>'front'), function() {

	Route::post('/newsletter','UsersController@newslettersend')->name('user.news_letter_send');
	Route::get('/unsubscribe-newsletter/{id}','UsersController@unsubscribeNewsletter')->name('user.unsubscribeNewsletter');
	Route::get('/',array('as'=>'user.index','uses'=>'UsersController@index'));

	//login
	Route::get('/login','UsersController@loginForm')->name('user.login');
	Route::post('/login', 'UsersController@login')->name('web.login.submit');

	//Nanny signup
	Route::get('/signup','UsersController@signUpForm')->name('user.signup');
	Route::post('/signup', 'UsersController@signUp')->name('web.signup.create');

    //client login 
	Route::get('/client-login','UsersController@clientloginForm')->name('client.login');


	//client signup
	Route::get('/client-signup','UsersController@clientSignUpform')->name('clients.signup');
	Route::post('/user-signup','UsersController@userSignUp')->name('user.user.signup');
	Route::get('/user-signup','UsersController@userSignUpform')->name('client.signup');

	//Price Form 
	Route::post('/user-plan','UsersController@planFormSubmit')->name('user.plan.submit');
	
	//Schedule
	Route::post('/user-check-info','UsersController@userCheckInfo')->name('user.userCheckInfo');

	//Coupen Code Checking
	Route::post('/coupen-code-check','GlobalUsersController@checkCoupenCode')->name('user.checkCoupenCode');
	
	//User Password Update
	Route::get('/forgot-password','UsersController@forgotPassword')->name('user.forgot_password');
	Route::post('/forgot-password','UsersController@forgotPasswordSend')->name('user.forgot_password_send');
	Route::get('/reset-password/{validstring}','UsersController@resetPassword')->name('user.resetPassword');
	Route::post('/reset-password','UsersController@saveResetPassword')->name('user.saveResetPassword');
	Route::get('/reset-password-msg','UsersController@resetPasswordMsg')->name('user.resetPasswordMsg');
	Route::post('/password-update', 'GlobalUsersController@changePassword')->name('nanny.password.update');
	
	// user account varification by email
	Route::get('/account-verification/{validstring}','UsersController@accountVerification')->name('user.accountvarify');
	Route::get('login/{provider}/callback','UsersController@Callback');
	Route::get('login/{type}/{provider}', 'UsersController@redirecttoSocial');

	

	Route::get('our-nannies','PageController@nannyListing')->name('user.nannylist');
	Route::get('our-nannies/profile/{id}','PageController@nannyProfile')->name('user.nanny.profile');
	Route::post('nanny-loadmore','PageController@nannyListLoadMore')->name('user.nannylistloadmore');
	
	Route::get('contact-us','PageController@conatctUs')->name('user.contact');
	Route::post('contact-us','PageController@contactUsSend')->name('user.contact.send');
	Route::get('about-us','PageController@aboutUs')->name('user.aboutus');
	Route::get('pricing','PageController@pricing')->name('user.pricing');
	Route::get('faqs','PageController@faqslist')->name('user.faqs');
	Route::get('terms-and-conditions','PageController@termsAndConditions')->name('user.termsandcondition');
	Route::get('user-verificaion/{validstring}','UsersController@userVerificaion')->name('user.verication');
	Route::post('/generate-password','UsersController@generateNewPassword')->name('user.generatePassword');

	Route::get('/blogs','BlogController@blog')->name('user.blog');
	Route::post('/loadmore-blogs','BlogController@loadmoreBlog')->name('user.loadmoreblog');
	Route::get('/blog-deatils/{slug}','BlogController@blogDetail')->name('user.blogdetail');
	Route::get('/pages/{slug}','PageController@cms')->name('user.page');

	
	Route::get('/get-facebook-review','CronController@getFacebookReview');
	Route::get('/testimonials','PageController@testimonials')->name('user.testimonials');
	Route::post('/loadmore-testimonials','PageController@loadmoreTestimonials')->name('user.loadmortestimonials');
	Route::post('/quote','UsersController@quote')->name('user.quote');
	Route::post('/get-curent-location','GlobalUsersController@getcurentLocation')->name('user.current.location');
	
	Route::post('/get-timeslots','GlobalUsersController@gettimeSlots')->name('user.time.slots');

	Route::post('/schedule-interview','GlobalUsersController@scheduleInterviewSubmit')->name('user.schedule.interview');

	Route::get('/deduct-payment','CronController@deductPayment');

	Route::get('/add-contact-list','CronController@addContactList');
	Route::get('/update-refresh-token','CronController@refreshToken');

	/*Nanny Interview List*/

	Route::get('interviews-nanny','GlobalUsersController@nannyInterviewList')->name('user.nannyInterviewList');
	/*Client Interview List*/
	Route::get('interviews-client','GlobalUsersController@clientInterviewList')->name('user.clientInterviewList');
	Route::get('join-interview/{id}','GlobalUsersController@joinInterview')->name('user.joinInterview');

	/*Nanny Earning List*/
	Route::get('my-earning-nanny','GlobalUsersController@nannyEarningList')->name('user.nannyEarningList');
	/*Client Earning List*/
	Route::get('my-earning-client','GlobalUsersController@clientEarningList')->name('user.clientEarningList');
	Route::get('my-earning-client-search','GlobalUsersController@clientEarningListSearch')->name('user.clientEarningListSearch');
	Route::get('my-earning-nanny-search','GlobalUsersController@nannyEarningListSearch')->name('user.nannyEarningListSearch');

	/*Nanny Notification List*/
	Route::any('notification-nanny','GlobalUsersController@nannyNotificationList')->name('user.nannyNotificationList');
	/*Client Notification List*/
	Route::any('notification-client','GlobalUsersController@clientNotificationList')->name('user.clientNotificationList');
	Route::post('change-read-status','GlobalUsersController@changeNotificationReadStatus')->name('user.changeNotificationReadStatus');

	/*Nanny Booking List*/
	Route::any('my-booking-nanny','GlobalUsersController@nannyBookingList')->name('user.nannyBookingList');
	/*Client Booking List*/
	Route::any('my-booking-client','GlobalUsersController@clientBookingList')->name('user.clientBookingList');

	Route::get('interviews','GlobalUsersController@nannyinterviewList')->name('user.nannyInterviewlist');

	/*booking*/
	Route::post('/get-nanny-avaiblity','GlobalUsersController@getNannyAvaiblity')->name('user.getNannyAvaiblity');
	Route::post('/get-booking-slots','GlobalUsersController@getBookingSlots')->name('user.getBookingSlots');
	Route::post('/nanny-booking','GlobalUsersController@nannyBooking')->name('user.nannybooking');
	Route::get('/approve-booking/{id}','GlobalUsersController@bookingApproved');
	Route::post('/approve-rejected/','GlobalUsersController@bookingRejected');
	Route::post('/stop-client-booking/','BookingController@stopClientBooking')->name('user.stop.booking');
	Route::get('/restart-client-booking/{id}','BookingController@restartClientBooking')->name('user.restart.booking');
	Route::post('/stop-nanny-booking/','BookingController@stopNannyBooking')->name('nanny.stop.booking');
	Route::get('/restart-nanny-booking/{id}','BookingController@restartNannyBooking')->name('nanny.restart.booking');
	/*booking*/
	
	/*Nanny Rating Review List*/
	Route::any('ratings-nanny','GlobalUsersController@nannyRatingList')->name('user.nannyRatingList');
	Route::post('give-feedback-nanny','GlobalUsersController@nannyGiveFeedback')->name('user.nannyGiveFeedback');

	/*Client Rating Review List*/
	Route::any('ratings-client','GlobalUsersController@clientRatingList')->name('user.clientRatingList');
	Route::post('give-feedback-client','GlobalUsersController@clientGiveFeedback')->name('user.clientGiveFeedback');
	Route::post('give-feedback-client-for-site','GlobalUsersController@clientGiveFeedbackToSite')->name('user.clientGiveFeedbacktinyhug.site');

	// Plan Routing
	Route::any('my-plan','GlobalUsersController@myPlanDetail')->name('user.myPlanDetail');
	Route::post('change-plan-status','GlobalUsersController@changeUserPlanStatus')->name('user.changeUserPlanStatus');

	/*Payment Setting Routing*/
	Route::any('payment-setting-client','GlobalUsersController@clientPaymentSetting')->name('user.clientPaymentSetting');

	Route::get('/tip/{validate_string}', 'GlobalUsersController@tip')->name('user.tip');
	Route::post('/tip-save','GlobalUsersController@tipSave')->name('user.tip-save');
	Route::get('/review-ratings/{validate_string}', 'globaluserscontroller@reviewratings')->name('user.review-ratings');
	
	Route::get('/thank-you', 'PageController@thankYou');
	Route::get('/weekly-deduct-payment', 'CronController@deductPaymentWeekly');
	Route::get('/send-weekly-invoice', 'CronController@sendInvoiceWeekly');
	
	Route::get('payouts', 'PayoutController@payouts');

	Route::get('/review-ratings/{validate_string}', 'GlobalUsersController@reviewRatings')->name('user.review-ratings');
	Route::post('/reviewRating','GlobalUsersController@reviewRatingSave')->name("reviewRating");
	Route::post('/get-nanychat-data','ChatsController@chatNanny')->name("chat.nanny-data");
	Route::post('/save-nanychat-data','ChatsController@saveChatNanny')->name("chat.save.nanny-data");
	Route::get('my-invoice', 'PageController@myInvoice')->name('user.invoice');
	Route::get('/schedule-interview-reminder','CronController@scheduleInteviewReminder');
	Route::get('/schedule-zoom-meeting','CronController@sheduleZoomMetting');
	Route::get('/meeting-join/{id}','PageController@meetingJoin')->name('meeting.join');
	Route::get('/payout-weekly-payment', 'CronController@payOutWeeklyPayment');

	

    //ReminderInrerviewEMail
	Route::get('/reminder-interview-before-5-minute', 'CronController@remiderInterviewBefore5Minute');
	Route::get('/reminder-interview-before-15-minute', 'CronController@remiderInterviewBefore15Minute');
	


	
});
Route::group(array('middleware' => 'App\Http\Middleware\AuthFront','namespace'=>'front'), function() {
	Route::get('/dashboard','UsersController@nannyDashboard')->name('user.nannydashboard');
	Route::get('/logout', 'GlobalUsersController@logout')->name('user.logout');
	//user profile update
	Route::get('/profile','GlobalUsersController@profile')->name('nanny.edit.profile');
	Route::post('/profile-update', 'GlobalUsersController@profileUpdate')->name('nanny.profile.update');
	Route::post('/image-upload', 'GlobalUsersController@uploadImage')->name('user.imageupload');

	Route::get('/set-availability','GlobalUsersController@setAvailability')->name('user.set.availability');
	Route::post('/set-availability','GlobalUsersController@savesetAvailability')->name('user.set.availability');
	Route::get('/add-holiday/{date}','GlobalUsersController@add_holiday')->name('user.add_holiday');
	Route::get('/delete-holiday/{id}','GlobalUsersController@deleteHolidays');
	
	Route::get('/payment-setting','UsersController@paymentSettings')->name('user.payment-setting');
	Route::post('/weekly-recurring-status','UsersController@weeklyRecurringStatus')->name('user.weekly-recurring');
	Route::post('/add-card','UsersController@addCard')->name('user.addCard');
	Route::post('/update-card','UsersController@updateCard')->name('user.updateCard');
	Route::post('/delete-card','UsersController@deleteCard')->name('user.deleteCard');
	Route::get('/nanny-payment-setting','UsersController@nannyPaymentSettings')->name('user.nanny-payment-setting');

	Route::any('support-chat','SupportController@chatlist')->name('support.chatlist');
	/*Nanny Inbox List*/
	Route::any('inbox-nanny','ChatsController@nannyInboxList')->name('user.nannyInboxList');
	/*Client Inbox List*/
	Route::any('inbox-client','ChatsController@clientInboxList')->name('user.clientInboxList');
	Route::post('get-chat-history','ChatsController@chatHistory')->name('user.chatHistory');

	Route::get('/my-newplan/{id}', 'GlobalUsersController@myNewPlan')->name('user.mynewplan.submit');
	Route::post('/my-newplan', 'GlobalUsersController@myNewPlanSubmit')->name('user.mynewplan.submit');
	Route::post('/get-cart-data', 'GlobalUsersController@getCardData')->name('get.cart.data');
	Route::get('/approve-interview/{id}','GlobalUsersController@interviewApproved');
	Route::post('/reject-interview','GlobalUsersController@interviewRejected');
	Route::post('/clear-setaviblity','GlobalUsersController@clearSetaviblity')->name('clear.setavablity');

});

###################################### Front Routing end here ##############################################


###################################### Admin Routing start here ############################################

Route::group(array('prefix' => 'adminpnlx'), function() {
	Route::group(array('middleware' => 'App\Http\Middleware\GuestAdmin','namespace'=>'adminpnlx'), function() {
		Route::get('','AdminLoginController@login');
		Route::any('/login','AdminLoginController@login');
		Route::get('forget_password','AdminLoginController@forgetPassword');
		Route::get('reset_password/{validstring}','AdminLoginController@resetPassword');
		Route::post('send_password','AdminLoginController@sendPassword');
		Route::post('save_password','AdminLoginController@resetPasswordSave');
	});


	Route::group(array('middleware' => 'App\Http\Middleware\AuthAdmin','namespace'=>'adminpnlx'), function() {
		Route::get('/logout','AdminLoginController@logout');
		Route::get('dashboard' ,array('as'=>'dashboard','uses'=>'AdminDashboardController@showdashboard'));
		Route::get('/myaccount','AdminDashboardController@myaccount');
		Route::post('/myaccount','AdminDashboardController@myaccountUpdate');
		Route::get('/change-password','AdminDashboardController@change_password');
		Route::post('/changed-password','AdminDashboardController@changedPassword');
		

		/** settings routing**/
		Route::any('/settings',array('as'=>'settings.listSetting','uses'=>'SettingsController@listSetting'));
		Route::get('/settings/add-setting','SettingsController@addSetting');
		Route::post('/settings/add-setting','SettingsController@saveSetting');
		Route::get('/settings/edit-setting/{id}','SettingsController@editSetting');
		Route::post('/settings/edit-setting/{id}','SettingsController@updateSetting');
		Route::get('/settings/prefix/{slug}','SettingsController@prefix');
		Route::post('/settings/prefix/{slug}','SettingsController@updatePrefix');
		Route::delete('/settings/delete-setting/{id}','SettingsController@deleteSetting');
		/** settings routing**/


		/* cms-manager routes */
		Route::get('cms-manager',array('as'=>'Cms.index','uses'=>'CmspagesController@index'));
		Route::post('cms-manager',array('as'=>'Cms.index','uses'=>'CmspagesController@index'));
		Route::get('cms-manager/add',array('as'=>'Cms.add','uses'=>'CmspagesController@add'));
		Route::post('cms-manager/add',array('as'=>'Cms.add','uses'=>'CmspagesController@save'));
		Route::get('cms-manager/edit/{id}',array('as'=>'Cms.edit','uses'=>'CmspagesController@edit'));	
		Route::post('cms-manager/edit/{id}',array('as'=>'Cms.edit','uses'=>'CmspagesController@update'));	
		Route::get('cms-manager/delete/{id}',array('as'=>'Cms.delete','uses'=>'CmspagesController@delete'));
		Route::get('cms-manager/view/{id}',array('as'=>'Cms.view','uses'=>'CmspagesController@view'));	
		Route::get('cms-manager/update-status/{id}/{status}',array('as'=>'Cms.status','uses'=>'CmspagesController@changeStatus'));
		/* cms-manager routes */


		/** Lookups manager  module  routing start here **/
		Route::get('lookups-manager/add-lookups/{type}',array('as'=>'Lookups.add','uses'=>'LookupsController@addLookups'));
		Route::post('lookups-manager/add-lookups/{type}','LookupsController@saveLookups');
		Route::get('lookups-manager/edit-lookups/{id}/{type}','LookupsController@editLookups');
		Route::post('lookups-manager/edit-lookups/{id}/{type}','LookupsController@updateLookups');
		Route::get('lookups-manager/update-lookups/{id}/{status}/{type}',array('as'=>'Lookups.status','uses'=>'LookupsController@updateLookupStatus'));
		Route::get('lookups-manager/delete-lookups/{id}/{type}','LookupsController@deleteLookups');
		Route::delete('lookups-manager/delete-lookups/{id}/{type}','LookupsController@deleteLookups');
		Route::get('/lookups-manager/{type}',array('as'=>'Lookups.listLookups','uses'=>'LookupsController@listLookups'));
		Route::get('/lookups-manager/{type}/{isimage}',array('as'=>'Lookups.listLookups','uses'=>'LookupsController@listLookups'));
		Route::post('/lookups-manager/{type}','LookupsController@listLookups');
		/** Lookups manager  module  routing start here **/


		/* Subscriber routes */
		Route::get('subscribers',array('as'=>'Subscriber.index','uses'=>'SubscribersController@index'));
		Route::post('subscribers',array('as'=>'Subscriber.index','uses'=>'SubscribersController@index'));
		Route::get('subscribers/add-new-subscriber',array('as'=>'Subscriber.add','uses'=>'SubscribersController@add'));
		Route::post('subscribers/add-new-subscriber',array('as'=>'Subscriber.add','uses'=>'SubscribersController@save'));
		Route::get('subscribers/edit-new-subscriber/{id}',array('as'=>'Subscriber.edit','uses'=>'SubscribersController@edit'));
		Route::post('subscribers/edit-new-subscriber/{id}',array('as'=>'Subscriber.edit','uses'=>'SubscribersController@update'));
		Route::get('subscribers/delete-subscriber/{id}',array('as'=>'Subscriber.delete','uses'=>'SubscribersController@delete'));
		Route::get('subscribers/subscriber/{id}',array('as'=>'Subscriber.view','uses'=>'SubscribersController@view'));
		Route::get('subscribers/send-verification/{id}',array('as'=>'Subscriber.verification','uses'=>'SubscribersController@sendverification'));
		Route::get('subscribers/update-subscriber-status/{id}',array('as'=>'Subscriber.status','uses'=>'SubscribersController@changeStatus'));
		/* Subscriber routes */

		
		/* Nanny routes */
		Route::get('nanny',array('as'=>'Nanny.index','uses'=>'NannyController@index'));
		Route::post('nanny',array('as'=>'Nanny.index','uses'=>'NannyController@index'));
		Route::get('nanny/add-new-nanny',array('as'=>'Nanny.add','uses'=>'NannyController@add'));
		Route::post('nanny/add-new-nanny',array('as'=>'Nanny.add','uses'=>'NannyController@save'));
		Route::get('nanny/edit-new-nanny/{id}',array('as'=>'Nanny.edit','uses'=>'NannyController@edit'));
		Route::post('nanny/edit-new-nanny/{id}',array('as'=>'Nanny.edit','uses'=>'NannyController@update'));
		Route::get('nanny/delete-nanny/{id}',array('as'=>'Nanny.delete','uses'=>'NannyController@delete'));
		Route::get('nanny/nanny/{id}',array('as'=>'Nanny.view','uses'=>'NannyController@view'));
		Route::get('nanny/send-verification/{id}',array('as'=>'Nanny.verification','uses'=>'NannyController@sendverification'));
		Route::get('nanny/send-user-verification/{id}/{status}',array('as'=>'Nanny.senduserverification','uses'=>'NannyController@senduserverification'));
		Route::get('nanny/update-nanny-status/{id}',array('as'=>'Nanny.status','uses'=>'NannyController@changeStatus'));

		Route::get('nanny/removeCertificates/{id}',array('as'=>'Nanny.certificates','uses'=>'NannyController@removenannyCertificates'));

		/* Vender routes */


		/** email-manager routing**/
		Route::get('/email-manager',array('as'=>'EmailTemplate.index','uses'=>'EmailtemplateController@listTemplate'));
		Route::get('/email-manager/add-template',array('as'=>'EmailTemplate.add','uses'=>'EmailtemplateController@addTemplate'));
		Route::post('/email-manager/add-template','EmailtemplateController@saveTemplate');
		Route::get('/email-manager/edit-template/{id}',array('as'=>'EmailTemplate.edit','uses'=>'EmailtemplateController@editTemplate'));
		Route::post('/email-manager/edit-template/{id}','EmailtemplateController@updateTemplate');
		Route::post('/email-manager/get-constant','EmailtemplateController@getConstant');
		/** email-manager routing**/


		/* Email Logs Manager routing */
		Route::get('/email-logs',array('as'=>'EmailLogs.listEmail','uses'=>'EmailLogsController@listEmail'));
		Route::any('/email-logs/email_details/{id}','EmailLogsController@EmailDetail');
		/** email-manager routing**/


		/* block routes */
		Route::get('blocks',array('as'=>'Blocks.index','uses'=>'BlocksController@index'));
		Route::post('blocks',array('as'=>'Blocks.index','uses'=>'BlocksController@index'));
		Route::get('blocks/add',array('as'=>'Blocks.add','uses'=>'BlocksController@add'));
		Route::post('blocks/add',array('as'=>'Blocks.add','uses'=>'BlocksController@save'));
		Route::get('blocks/{id}',array('as'=>'Blocks.edit','uses'=>'BlocksController@edit'));	
		Route::post('blocks/{id}',array('as'=>'Blocks.edit','uses'=>'BlocksController@update'));	
		Route::get('blocks/delete-block/{id}',array('as'=>'Blocks.delete','uses'=>'BlocksController@delete'));
		Route::get('blocks/update-block-status/{id}/{status}',array('as'=>'Blocks.status','uses'=>'BlocksController@changeStatus'));
		/* block routes */


		/*Faq routes */
		Route::get('faqs',array('as'=>'Faqs.index','uses'=>'FaqController@index'));				
		Route::get('faqs/add-faq',array('as'=>'Faqs.add','uses'=>'FaqController@add'));							
		Route::post('faqs/add-faq',array('as'=>'Faqs.save','uses'=>'FaqController@save'));
		Route::get('faqs/edit-faq/{id}',array('as'=>'Faqs.edit','uses'=>'FaqController@edit'));
		Route::post('faqs/edit-faq/{id}',array('as'=>'Faqs.update','uses'=>'FaqController@update'));
		Route::get('faqs/update-faq-status/{id}/{status}',array('as'=>'Faqs.status','uses'=>'FaqController@changeStatus'));
		Route::any('faqs/delete-faq/{id}',array('as'=>'Faqs.delete','uses'=>'FaqController@delete'));
		Route::get('faqs/view-faq/{id}',array('as'=>'Faqs.view','uses'=>'FaqController@view'));	
		/*Faq routes */


		### Language setting start //
		Route::get('/language-settings',array('as'=>'LanguageSetting.index','uses'=>'LanguageSettingsController@listLanguageSetting'));
		Route::get('/language-settings/add-setting',array('as'=>'LanguageSetting.add','uses'=>'LanguageSettingsController@addLanguageSetting'));
		Route::post('/language-settings/add-setting',array('as'=>'LanguageSetting.save','uses'=>'LanguageSettingsController@saveLanguageSetting'));
		Route::get('/language-settings/edit-setting/{id}',array('as'=>'LanguageSetting.edit','uses'=>'LanguageSettingsController@editLanguageSetting'));
		Route::post('/language-settings/edit-setting',array('as'=>'LanguageSetting.update','uses'=>'LanguageSettingsController@updateLanguageSetting'));
		

		// How it works routes
		Route::get('/how-it-work',array('as'=>'HowItWork.index','uses'=>'HowItWorkController@index'));
		Route::get('/how-it-work/add-how-it-work',array('as'=>'HowItWork.add','uses'=>'HowItWorkController@add'));
		Route::post('/how-it-work/add-how-it-work',array('as'=>'HowItWork.add','uses'=>'HowItWorkController@save'));
		Route::get('/how-it-work/edit-how-it-work/{id}',array('as'=>'HowItWork.edit','uses'=>'HowItWorkController@edit'));
		Route::post('/how-it-work/edit-how-it-work/{id}',array('as'=>'HowItWork.edit','uses'=>'HowItWorkController@update'));
		Route::get('/how-it-work/delete-how-it-work/{id}',array('as'=>'HowItWork.delete','uses'=>'HowItWorkController@delete'));
		Route::get('/how-it-work/view-how-it-work/{id}',array('as'=>'HowItWork.view','uses'=>'HowItWorkController@view'));

		
		// Testimonials routes
		Route::get('/testimonial',array('as'=>'Testimonial.index','uses'=>'TestimonialController@index'));
		Route::get('/testimonial/add-testimonial',array('as'=>'Testimonial.add','uses'=>'TestimonialController@add'));
		Route::post('/testimonial/add-testimonial',array('as'=>'Testimonial.add','uses'=>'TestimonialController@save'));
		Route::get('/testimonial/edit-testimonial/{id}',array('as'=>'Testimonial.edit','uses'=>'TestimonialController@edit'));
		Route::post('/testimonial/edit-testimonial/{id}',array('as'=>'Testimonial.edit','uses'=>'TestimonialController@update'));
		Route::get('/testimonial/delete-testimonial/{id}',array('as'=>'Testimonial.delete','uses'=>'TestimonialController@delete'));
		Route::get('/testimonial/view-testimonial/{id}',array('as'=>'Testimonial.view','uses'=>'TestimonialController@view'));
		//Testimonials routes

		// Why Choose Us Routing Start From Here
		Route::get('/why-choose-us',							array('as'=>'WhyChooseUs.index',	'uses'=>'WhyChooseUsController@index'));
		Route::get('/why-choose-us/add-why-choose-us',			array('as'=>'WhyChooseUs.add',		'uses'=>'WhyChooseUsController@add'));
		Route::post('/why-choose-us/add-why-choose-us',			array('as'=>'WhyChooseUs.add',		'uses'=>'WhyChooseUsController@save'));
		Route::get('/why-choose-us/edit-why-choose-us/{id}',	array('as'=>'WhyChooseUs.edit',		'uses'=>'WhyChooseUsController@edit'));
		Route::post('/why-choose-us/edit-why-choose-us/{id}',	array('as'=>'WhyChooseUs.edit',		'uses'=>'WhyChooseUsController@update'));
		Route::get('/why-choose-us/view-why-choose-us/{id}',	array('as'=>'WhyChooseUs.view',		'uses'=>'WhyChooseUsController@view'));
		Route::get('/why-choose-us/delete-why-choose-us/{id}',	array('as'=>'WhyChooseUs.delete',	'uses'=>'WhyChooseUsController@delete'));
		// Why Choose Us Routing End Here

		// Partners Routing Start From Here
		Route::get('/partners',									array('as'=>'Partners.index',	'uses'=>'PartnersController@index'));
		Route::get('/partners/add-partner',						array('as'=>'Partners.add',		'uses'=>'PartnersController@add'));
		Route::post('/partners/add-partner',					array('as'=>'Partners.add',		'uses'=>'PartnersController@save'));
		Route::get('/partners/edit-partner/{id}',				array('as'=>'Partners.edit',	'uses'=>'PartnersController@edit'));
		Route::post('/partners/edit-partner/{id}',				array('as'=>'Partners.edit',	'uses'=>'PartnersController@update'));
		Route::get('/partners/view-partner/{id}',				array('as'=>'Partners.view',	'uses'=>'PartnersController@view'));
		Route::get('/partners/delete-partner/{id}',				array('as'=>'Partners.delete',	'uses'=>'PartnersController@delete'));
		// Partners Routing End Here

		// Our Core Values Routing Start From Here
		Route::get('/our-core-value',							array('as'=>'OurCoreValues.index',	'uses'=>'OurCoreValuesController@index'));
		Route::get('/our-core-value/add-our-core-value',		array('as'=>'OurCoreValues.add',	'uses'=>'OurCoreValuesController@add'));
		Route::post('/our-core-value/add-our-core-value',		array('as'=>'OurCoreValues.add',	'uses'=>'OurCoreValuesController@save'));
		Route::get('/our-core-value/edit-our-core-value/{id}',	array('as'=>'OurCoreValues.edit',	'uses'=>'OurCoreValuesController@edit'));
		Route::post('/our-core-value/edit-our-core-value/{id}',	array('as'=>'OurCoreValues.edit',	'uses'=>'OurCoreValuesController@update'));
		Route::get('/our-core-value/view-our-core-value/{id}',	array('as'=>'OurCoreValues.view',	'uses'=>'OurCoreValuesController@view'));
		Route::get('/our-core-value/delete-our-core-value/{id}',array('as'=>'OurCoreValues.delete',	'uses'=>'OurCoreValuesController@delete'));
		// Our Core Values Routing End Here

		// News Letter Routing Start From Here
		Route::get('/newsletter',						array('as'=>'Newsletter.index',	'uses'=>'NewsletterController@index'));
		Route::get('/newsletter/add-newsletter',		array('as'=>'Newsletter.add',	'uses'=>'NewsletterController@add'));
		Route::post('/newsletter/add-newsletter',		array('as'=>'Newsletter.add',	'uses'=>'NewsletterController@save'));
		Route::get('/newsletter/update-status/{id}',	array('as'=>'Newsletter.status','uses'=>'NewsletterController@changeStatus'));
		Route::get('/newsletter/view-newsletter/{id}',	array('as'=>'Newsletter.view',	'uses'=>'NewsletterController@view'));
		Route::get('/newsletter/edit-newsletter/{id}',	array('as'=>'Newsletter.edit',	'uses'=>'NewsletterController@edit'));
		Route::get('/newsletter/delete-newsletter/{id}',array('as'=>'Newsletter.delete','uses'=>'NewsletterController@delete'));

		// News Letter Routing End Here

		// blog Mangement
		Route::any('/blog',								array('as'=>'Blog.listBlog',	'uses'=>'BlogController@listBlog'));
		Route::get('/blog/add-blog',  					array('as'=>'Blog.add',			'uses'=>'BlogController@addBlog'));
		Route::post('/blog/add-blog', 					array('as'=>'Blog.add',			'uses'=>'BlogController@saveBlog'));
		Route::get('/blog/edit-blog/{id}', 				array('as'=>'Blog.edit',		'uses'=>'BlogController@editBlog'));
		Route::post('/blog/edit-blog/{id}', 			array('as'=>'Blog.edit',		'uses'=>'BlogController@updateBlog'));
		Route::get('/blog/view-blog/{id}', 				array('as'=>'Blog.view',		'uses'=>'BlogController@viewBlog'));
		Route::get('/blog/delete-blog/{id}', 			array('as'=>'Blog.delete',		'uses'=>'BlogController@deleteBlog'));
		Route::get('/blog/comment-blog/{id}', 			array('as'=>'Blog.comment',		'uses'=>'BlogController@commentBlog'));
		Route::post('/blog/comment-blog/{id}', 			array('as'=>'Blog.comment',		'uses'=>'BlogController@saveCommentBlog'));
		Route::any('/blog/delete-comment/{id}', 		array('as'=>'Blog.deletecomment','uses'=>'BlogController@deletetCommentBlog'));
		Route::any('/blog/edit-comment/{blogId}/{id}', 	array('as'=>'Blog.editcomment',	'uses'=>'BlogController@editCommentBlog'));
		Route::post('/blog/reply-comment-blog/{id}', 	array('as'=>'Blog.editcomment',	'uses'=>'BlogController@saveReplyCommentBlog'));
		Route::get('/blog/update-status/{id}/{status}',	array('as'=>'Blog.status',		'uses'=>'BlogController@updateBlogStatus'));
		// blog Mangement

		// Support Account
		Route::get('/support-acc',						array('as'=>'Support.index',	'uses'=>'SupportController@index'));
		Route::get('/support-acc/add',					array('as'=>'Support.add',		'uses'=>'SupportController@add'));
		Route::post('/support-acc/add',					array('as'=>'Support.add',		'uses'=>'SupportController@save'));
		Route::get('/support-acc/edit/{id}',			array('as'=>'Support.edit',		'uses'=>'SupportController@edit'));
		Route::post('/support-acc/edit/{id}',			array('as'=>'Support.edit',		'uses'=>'SupportController@update'));
		Route::get('/support-acc/view/{id}',			array('as'=>'Support.view',		'uses'=>'SupportController@view'));
		Route::get('/support-acc/delete/{id}',			array('as'=>'Support.delete',	'uses'=>'SupportController@delete'));
		Route::get('support-acc/status/{id}',			array('as'=>'Support.status',	'uses'=>'SupportController@changeStatus'));
		//Support Account

		/* system documents route */
		Route::get('system-documents',array('as'=>'SystemDocument.index','uses'=>'systemDocumentsController@index'));
		Route::post('system-documents',array('as'=>'SystemDocument.index','uses'=>'systemDocumentsController@index'));
		Route::get('system-documents/add',array('as'=>'SystemDocument.add','uses'=>'systemDocumentsController@add'));
		Route::post('system-documents/add',array('as'=>'SystemDocument.add','uses'=>'systemDocumentsController@save'));
		Route::get('system-documents/edit/{id}',array('as'=>'SystemDocument.edit','uses'=>'systemDocumentsController@edit'));
		Route::post('system-documents/edit/{id}',array('as'=>'SystemDocument.edit','uses'=>'systemDocumentsController@update'));	
		Route::get('system-documents/delete/{id}',array('as'=>'SystemDocument.delete','uses'=>'systemDocumentsController@delete'));
		Route::get('system-documents/update-system-document-status/{id}/{status}',array('as'=>'SystemDocument.status','uses'=>'systemDocumentsController@changeStatus'));
		/* system documents route */

		/* banners route */
		Route::get('banners',array('as'=>'Banner.index','uses'=>'BannersController@index'));
		Route::post('banners',array('as'=>'Banner.index','uses'=>'BannersController@index'));
		Route::get('banners/add',array('as'=>'Banner.add','uses'=>'BannersController@add'));
		Route::post('banners/add',array('as'=>'Banner.add','uses'=>'BannersController@save'));
		Route::get('banners/edit/{id}',array('as'=>'Banner.edit','uses'=>'BannersController@edit'));	
		Route::post('banners/edit/{id}',array('as'=>'Banner.edit','uses'=>'BannersController@update'));	
		Route::get('banners/delete/{id}',array('as'=>'Banner.delete','uses'=>'BannersController@delete'));
		Route::get('banners/view/{id}',array('as'=>'Banner.view','uses'=>'BannersController@view'));
		Route::get('banners/update-banner-status/{id}/{status}',array('as'=>'Banner.status','uses'=>'BannersController@changeStatus'));
		/* banners route */

		/* News Letter routes starts from here */
		Route::get('/news-letter','NewsLetterController@listTemplate');
		Route::post('/news-letter','NewsLetterController@listTemplate');
		Route::get('/news-letter/edit-template/{id}','NewsLetterController@editTemplate');
		Route::post('/news-letter/edit-template/{id}','NewsLetterController@updateTemplate');
		Route::get('/news-letter','NewsLetterController@listTemplate');
		Route::get('/news-letter',array('as'=>'NewsLetter.listTemplate','uses'=>'NewsLetterController@listTemplate'));
		Route::post('/news-letter','NewsLetterController@listTemplate');
		Route::get('/news-letter/edit-template/{id}','NewsLetterController@editTemplate');
		Route::post('/news-letter/edit-template/{id}','NewsLetterController@updateTemplate');
		Route::get('/news-letter/newsletter-templates',array('as'=>'NewsTemplates.newsletterTemplates','uses'=>'NewsLetterController@newsletterTemplates'));
		Route::get('/news-letter/add-template','NewsLetterController@addTemplates');
		Route::any('/news-letter/add-subscriber','NewsLetterController@addSubscriber');
		Route::post('/news-letter/add-template','NewsLetterController@saveTemplates');
		Route::get('/news-letter/edit-newsletter-templates/{id}','NewsLetterController@editNewsletterTemplate');
		Route::post('/news-letter/edit-newsletter-templates/{id}','NewsLetterController@updateNewsletterTemplate');
		Route::get('/news-letter/send-newsletter-templates/{id}','NewsLetterController@sendNewsletterTemplate');
		Route::post('/news-letter/send-newsletter-templates/{id}','NewsLetterController@updateSendNewsletterTemplate');
		Route::get('/news-letter/subscriber-list',array('as'=>'Subscriber.subscriberList','uses'=>'NewsLetterController@subscriberList'));
		Route::get('/news-letter/subscriber-active/{id}/{status}','NewsLetterController@subscriberActive');
		Route::any('news-letter/subscriber-delete/{id}','NewsLetterController@subscriberDelete');
		Route::any('news-letter/delete-template/{id}','NewsLetterController@templateDelete');
		Route::any('news-letter/view-subscriber/{id}','NewsLetterController@viewSubscrieber');
		Route::any('news-letter/delete-newsletter-template/{id}','NewsLetterController@deleteNewsTemplate');
		Route::post('news-letter/delete-multiple-subscriber','NewsLetterController@deleteMultipleSubscriber');
		Route::get('/news-letter/export',array('as'=>'Subscriber.export','uses'=>'NewsLetterController@export_all_subscribers'));
		Route::get('/unsubscribe/{enc_id}',array('as'=>'User.unsubscribe','uses'=>'NewsLetterController@unsubscribe_newsletter'));
		/* News Letter routes end here */

		/* package routes */
		Route::get('package',array('as'=>'Package.index','uses'=>'PackageController@index'));
		Route::post('package',array('as'=>'Package.index','uses'=>'PackageController@index'));
		Route::get('package/add-new-package',array('as'=>'Package.add','uses'=>'PackageController@add'));
		Route::post('package/add-new-package',array('as'=>'Package.add','uses'=>'PackageController@save'));
		Route::get('package/edit-package/{id}',array('as'=>'Package.edit','uses'=>'PackageController@edit'));	
		Route::post('package/edit-package/{id}',array('as'=>'Package.edit','uses'=>'PackageController@update'));	
		Route::get('package/delete-package/{id}',array('as'=>'Package.delete','uses'=>'PackageController@delete'));
		Route::get('package/view-package/{id}',array('as'=>'Package.view','uses'=>'PackageController@view'));	
		Route::get('package/update-package-status/{id}/{status}',array('as'=>'Package.status','uses'=>'PackageController@changeStatus'));
		/* user routes */

		/* Facebook Setting routes */
			//Route::post('facebook-setting',array('as'=>'Facebook.index','uses'=>'FacebookSettingController@index'));
			//Route::get('facebook-setting/add',array('as'=>'Package.add','uses'=>'PackageController@add'));
			//Route::post('package/add-new-package',array('as'=>'Package.add','uses'=>'PackageController@save'));
		Route::get('facebook-setting/edit/',array('as'=>'Facebook.edit','uses'=>'FacebookSettingController@edit'));	
		Route::post('facebook-setting/edit/',array('as'=>'Facebook.edit','uses'=>'FacebookSettingController@update'));
		Route::post('facebook-setting/update_token/',array('as'=>'Facebook.update.token','uses'=>'FacebookSettingController@updateToken'));	
		/* user routes */


		/* Schedule Interview routes */

		Route::get('/schedule-interview',			array('as'=>'ScheduleInterview.index',	'uses'=>'ScheduleInterviewController@index'));
		Route::get('/schedule-interviewview/{id}',	array('as'=>'ScheduleInterview.view',	'uses'=>'ScheduleInterviewController@view'));
		Route::get('/schedule-interview/meeting-join/{id}','ScheduleInterviewController@meetingJoin')->name('admin.meeting.join');

		/* User Plans routes */

		Route::get('/user-plans',			array('as'=>'UserPlans.index',	'uses'=>'UserPlanController@index'));
		Route::get('/user-plans/view/{id}',	array('as'=>'UserPlans.view',		'uses'=>'UserPlanController@view'));

		/* Payout request routes */
		Route::get('/payout-request',array('as'=>'Payout.index','uses'=>'PayoutRequestController@index'));
		Route::get('payout-request/payout-download', 'PayoutRequestController@payoutDownload')->name('payout-request.payoutdownload');
		Route::get('payout-request/edit/{id}',array('as'=>'Payout.edit','uses'=>'PayoutRequestController@edit'));
		Route::post('payout-request/edit/{id}',array('as'=>'Payout.edit','uses'=>'PayoutRequestController@update'));
		Route::get('payout-request/status/{id}/{status}',array('as'=>'Payout.status','uses'=>'PayoutRequestController@changeStatus'));

		/* Inquiry routes */

		Route::get('/inquiries',			array('as'=>'Inquiry.index',	'uses'=>'InquiryController@index'));
		Route::get('/inquiries/view/{id}',	array('as'=>'Inquiry.view',	    'uses'=>'InquiryController@view'));
		Route::get('/inquiries/delete/{id}',array('as'=>'Inquiry.delete',	'uses'=>'InquiryController@delete'));
		Route::post('/inquiries/reply',		array('as'=>'Inquiry.reply',	'uses'=>'InquiryController@reply'));


		/* coupon codes routes */
		Route::get('coupon-codes',array('as'=>'CouponCodes.index','uses'=>'CouponCodesController@index'));
		Route::get('coupon-codes/add-new-coupon-code',array('as'=>'CouponCodes.add','uses'=>'CouponCodesController@add'));
		Route::post('coupon-codes/add-new-coupon-code',array('as'=>'CouponCodes.add','uses'=>'CouponCodesController@save'));
		Route::get('coupon-codes/edit-coupon-code/{id}',array('as'=>'CouponCodes.edit','uses'=>'CouponCodesController@edit'));
		Route::post('coupon-codes/edit-coupon-code/{id}',array('as'=>'CouponCodes.edit','uses'=>'CouponCodesController@update'));
		Route::get('coupon-codes/delete-coupon-code/{id}',array('as'=>'CouponCodes.delete','uses'=>'CouponCodesController@delete'));
		Route::get('coupon-codes/view-coupon-code/{id}',array('as'=>'CouponCodes.view','uses'=>'CouponCodesController@view'));
		Route::get('coupon-codes/update-coupon-code-status/{id}',array('as'=>'CouponCodes.status','uses'=>'CouponCodesController@changeStatus'));
		/* coupon codes routes */

		// notification routes
		Route::get('notifications',array('as'=>'Notification.index','uses'=>'NotificationController@index'));

		/* bookings codes routes */
		Route::get('booking',array('as'=>'booking.index','uses'=>'BookingController@index'));
		Route::get('booking/add',array('as'=>'booking.add','uses'=>'BookingController@add'));
		Route::post('booking/add',array('as'=>'booking.add','uses'=>'BookingController@save'));
		Route::get('booking/edit/{id}',array('as'=>'booking.edit','uses'=>'BookingController@edit'));
		Route::post('booking/edit/{id}',array('as'=>'booking.edit','uses'=>'BookingController@update'));
		Route::get('booking/delete/{id}',array('as'=>'booking.delete','uses'=>'BookingController@delete'));
		Route::get('booking/view/{id}',array('as'=>'booking.view','uses'=>'BookingController@view'));
		Route::get('booking/status/{id}',array('as'=>'booking.status','uses'=>'BookingController@changeStatus'));
		/* bookings routes */
		Route::get('booking/delete/{id}',array('as'=>'booking.delete','uses'=>'BookingController@delete'));

		// Prices Routes
		Route::get('prices',array('as'=>'price.index','uses'=>'PriceController@index'));
		Route::get('prices/add',array('as'=>'price.add','uses'=>'PriceController@add'));
		Route::post('prices/add',array('as'=>'price.add','uses'=>'PriceController@save'));
		Route::get('prices/edit/{id}',array('as'=>'price.edit','uses'=>'PriceController@edit'));
		Route::post('prices/edit/{id}',array('as'=>'price.edit','uses'=>'PriceController@update'));
		Route::get('prices/delete/{id}',array('as'=>'price.delete','uses'=>'PriceController@delete'));
		// Prices Routes

		// Tax Routes
		Route::get('taxes',array('as'=>'tax.index','uses'=>'TaxController@index'));
		Route::get('taxes/add',array('as'=>'tax.add','uses'=>'TaxController@add'));
		Route::post('taxes/add',array('as'=>'tax.add','uses'=>'TaxController@save'));
		Route::get('taxes/edit/{id}',array('as'=>'tax.edit','uses'=>'TaxController@edit'));
		Route::post('taxes/edit/{id}',array('as'=>'tax.edit','uses'=>'TaxController@update'));
		Route::get('taxes/delete/{id}',array('as'=>'tax.delete','uses'=>'TaxController@delete'));
		// Tax Routes
		
		Route::any('support-chat','ChatController@chatlist')->name('support.chatlist');
		Route::post('get-chat-history','ChatController@getChatHistory')->name('support.getChatHistory');

		// Staff Attendence
		Route::get('staff-attendance',array('as'=>'StaffAttendance.index','uses'=>'StaffAttendanceController@index'));
		Route::get('staff-attendance/edit/{id}',array('as'=>'StaffAttendance.edit','uses'=>'StaffAttendanceController@edit'));
		Route::post('staff-attendance/edit/{id}',array('as'=>'StaffAttendance.edit','uses'=>'StaffAttendanceController@update'));
		
		Route::get('staff-attendance/status/{id}',array('as'=>'StaffAttendance.status','uses'=>'StaffAttendanceController@changeStatus'));
		// Staff Attendence

	});
});
################################################ Admin Routing end here ##################################################