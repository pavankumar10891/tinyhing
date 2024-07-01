<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Model\FacebookSetting;
use App\Model\FacebookReview;
use DB,Config;
use Cartalyst\Stripe\Stripe;
use App\Model\User;
use App\Model\UserPlans;
use App\Model\UserTransactionHistory;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\Earning;
use App\Model\Holiday;
use App\Model\ScheduleInterview;
use App\Model\Payout;
use App\Model\PayoutDetail;
use App\Model\NewsletterContact;
use CustomHelper;

class CronController extends BaseController{

	public function __construct() {
		$secretKey          = env('STRIPE_SECRET');  
		$this->stripe       = Stripe::make($secretKey); 
		date_default_timezone_set('Asia/Kolkata'); 
	}

	public function getFacebookReview(){
		$data =  FacebookSetting::where('id', 1)->first();

		if(!empty($data)){
			$page_token = $data->page_token;;
			$page_id = $data->page_id;
			$url = "https://graph.facebook.com/v10.0/".$page_id."/ratings?access_token=".$page_token;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($response,true);
			//echo "<pre>";
            //print_r($response);die;
			if(!empty($response)){
				if(!empty($response['data'])){
					foreach($response['data'] as $data){
						$createdTime = date("Y-m-d H:i:s",strtotime($data['created_time']));
						$checkExist = FacebookReview::where('created_time',$createdTime)->first();
						if(empty($checkExist)){
							$obj = new FacebookReview;
							$obj->reviewer_name = '';
							$obj->reviewer_image = '';
							$obj->review_text = $data['review_text'];
							$obj->created_time = $createdTime;
							$obj->jsondata =  json_encode($data);
							$obj->save();
						}
					}
				}else{
					FacebookSetting::where('id', 1)->update(['page_token'=>'']);
				}
			}
		}
		die;
	}

	public function deductPayment(){
		$userPlans      =   DB::table('user_plans')
		->where('status',1)
		// ->where('payment_type','pay_later')
		// ->where('installment_no',"!=",'3')
		->where('plan_end_date',">=",date("Y-m-d"))
		->get();

		if(!empty($userPlans)){
			foreach($userPlans as $plan){

				$currentDate    =   strtotime($plan->plan_start_date);
				$nextMonthDate  =   date("Y-m-d", strtotime("+1 month", $currentDate));
				
				if($nextMonthDate == date('Y-m-d')){
					$nextinstDate  =   date("Y-m-d", strtotime("+1 month", strtotime($nextMonthDate)));
					$customerId         =   User::where('id',$plan->user_id)->value('customer_id');

					if(!empty($customerId)){
						$planDetail             =   DB::table('packages')->where('id',$plan->plan_id)->first();

						if(!empty($planDetail)){

							$planPrice          =   round($planDetail->price);
							$charge             =   $this->stripe->charges()->create(array(
								"amount"   => $planPrice,
								"currency" => "USD",
								"customer" => $customerId,
								'description'   =>  'Product',
							));


							$array              =   json_decode(json_encode($charge), true); 

							if(isset($array['status']) && $array['status'] == 'succeeded'){
								$chargeId                       =   $array['id'];
								$payment                        =   $this->stripe->charges()->find($chargeId);
								$order_number                   =   rand(); 
								$worldpayOrderCode              =   $chargeId; 
								$planObj                        =   UserPlans::find($plan->id);
								// $planObj->amount                =   $planObj->amount+$planPrice;
								$planObj->installment_no        =   $planObj->installment_no+1;
								$planObj->save();


								$self_history                       =  new UserTransactionHistory;
								$self_history->user_id              =  $plan->user_id;
								// $self_history->transaction_type     =  PLAN_PURCHASED;
								$self_history->transaction_id       =  $worldpayOrderCode;
								$self_history->order_number         =  $order_number;
								$self_history->transaction_amount   =  $planPrice;
								$self_history->transaction_currency =   Config::get("Site.currencyCode");
								$self_history->transaction_json_data =  json_encode($array);
								$self_history->save();

                                //Send Mail To User ()
								$userDetail                         =   DB::table('users')->where('id',$plan->user_id)->select("name","email")->first();
								$settingsEmail                      =   Config::get('Site.email');
								$full_name                          =   $userDetail->name; 
								$email                              =   $userDetail->email;
								// $installment_no                     =   ($planObj->installment_no == 3) ? 'Last' : $planObj->installment_no;
								// 
								$installment_no                     =   $planObj->installment_no;
								
								$emailActions                       =   EmailAction::where('action','=','installment_payment_received')->get()->toArray();
								$emailTemplates                     =   EmailTemplate::where('action','=','installment_payment_received')->get(array('name','subject','action','body'))->toArray();
								$cons                               =   explode(',',$emailActions[0]['options']);
								$constants                          =   array();
								foreach($cons as $key => $val){
									$constants[]                    = '{'.$val.'}';
								}
								$subject                            =   $emailTemplates[0]['subject'];
								$rep_Array                          =   array($full_name,$planDetail->name,Config::get("Site.currencyCode"),$planPrice,$planObj->plan_end_date); 
								$messageBody                        =   str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
								$mail                               =   $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
							} 
						}
					}
				}
			}
		} 
	}
	public function deductPaymentWeekly()
	{
		
		if(date("l") == "Saturday"){
			$bookings = DB::table('bookings')->select('bookings.*', 'n.name as nanny', 'n.nanny_price','u.name', 'u.email', 'u.customer_id', 'u.weekly_recurring')->leftJoin('users as u', 'u.id', '=', 'bookings.user_id')->where('bookings.status', 1)->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->where('bookings.start_date', '<',  date('Y-m-d'))->where('bookings.end_date', '>=',  date('Y-m-d'))->where('u.is_deleted', 0)->where('u.deleted_at', NUll)->where('u.user_role_id', 2)->where('u.weekly_recurring', 1)->get();


			if(!empty($bookings)){
					foreach($bookings as $key=>$value){
						$checkHolidayDate = Holiday::where('user_id', $value->nanny_id)->where('holiday_date', '>=', date('Y-m-d', strtotime($value->start_date)))->where('holiday_date', '<=', date('Y-m-d'))->get();
						$holidayArray = array();
						foreach($checkHolidayDate as $holidaydate){
							$holidayArray[] =  $holidaydate->holiday_date;
						}
						$endDate = date('Y-m-d'); 
						$existinvoice = Earning::where('end_date', $endDate)->first();
						
						if(!empty($existinvoice)){
							$startDate =  date('Y-m-d', strtotime($value->end_date. ' + 1 days'));;
						}else{
							$startDate = $value->start_date;
						}
					
						$hours = 0;
						$startDate = strtotime($value->start_date);
					
						$range = array();

						$date = strtotime("-1 day", $startDate);  
						while($date < strtotime($endDate))  { 
						   $date = strtotime("+1 day", $date);
						   if(!in_array(date('Y-m-d',$date), $holidayArray)){
						   	 $range[] = date('Y-m-d', $date);
						   }
						   
						}  
						
						//echo date("l", strtotime('2021-06-29'));die;
						if(!empty($range)){
							foreach($range as $range){
								$days = DB::table('booking_details')->where('booking_id', $value->id)->orderBy('id', 'desc')->get();
								/*echo "<pre>";
								print_r($days);*/
								
								foreach($days as  $key=>$day){
									if($day->sunday == 1){
											if(date("l", strtotime($range)) == 'Sunday'){
												$hours = $hours + 2;	
											}

											if(date("l", strtotime($range)) == 'Monday'){
												$hours = $hours + 2;	
											}

											if(date("l", strtotime($range)) == 'Tuesday'){
												$hours = $hours + 2;	
											}

											if(date("l", strtotime($range)) == 'Wednesday'){
												$hours = $hours + 2;	
											}

											if(date("l", strtotime($range)) == 'Thursday'){
												$hours = $hours + 2;	
											}
											if(date("l", strtotime($range)) == 'Friday'){
												$hours = $hours + 2;	
											}
											if(date("l", strtotime($range)) == 'Saturday'){
												$hours = $hours + 2;	
											}
										}
								}
							}
						}
						//echo $hours;die;
						if($hours > 0){
								$price = 0;
								if ($value->nanny_price > 0){
									$price =  $value->nanny_price;
								}
								else{

									if(strtolower($value->user_country) == 'canada'){
										$price = Config::get('Site.nanny_default_price_for_canada');
									}else{
										$price =  Config::get('Site.nanny_default_price_for_us');
									}

								}
							  	
							  	$taxpercentage = 0;
							  	$usercityTax = CustomHelper::checkUserInvoiceTax($value->user_id);
							  	if(!empty($usercityTax)){
							  		$taxpercentage = $usercityTax;
							  	}else{
							  		$taxpercentage = Config::get('Site.invoice_tax_percentage');
							  	}
								$totalpaybleAmount = $price * $hours;
								$planPrice          =   round($totalpaybleAmount);
								
								$taxamount = ($planPrice*$taxpercentage)/100;
								$totalAmount = round($planPrice+$taxamount);
								$charge             =   $this->stripe->charges()->create(array(
									"amount"   => $totalAmount,
									"currency" => "USD",
									"customer" => $value->customer_id,
								));

								if(!empty($charge) && $charge['status'] == 'succeeded'){
									$earning = new Earning;
									$earning->user_id 					= $value->user_id;
									$earning->nanny_id 					= $value->nanny_id;
									$earning->amount 					= $planPrice;
									$earning->total_amount 				= $totalAmount;
									$earning->start_date 				= date('Y-m-d', $startDate); 
									$earning->end_date 					= date('Y-m-d', strtotime($endDate));
									$earning->status 					= 1;
									$earning->is_payout 				= 0;
									$earning->booking_id 				= $value->id;
									$earning->total_nanny_working_hour 	= $hours;
									$earning->save();

								
									$self_history                       =  new UserTransactionHistory;
									$self_history->user_id              =  $value->user_id;
									$self_history->transaction_id       =  $charge['balance_transaction'];
									$self_history->order_number         =  rand().time();
									$self_history->transaction_amount   =  $totalAmount;
									$self_history->booking_id 		    = 	$value->id;
									$self_history->transaction_currency =   Config::get("Site.currencyCode");
									$self_history->transaction_json_data =  json_encode($charge);
									$self_history->save();

									
									$nannyprice = number_format($price, 2);
									$currencyCode = Config::get("Site.currencyCode");
									$totaworkigHour 	= 	$hours;
									$totalPlanprice  	= 	$totalAmount;
									$planPrice 			=  	Config::get("Site.currencyCode").$totalPlanprice; 

									$settingsEmail                    	=   Config::get('Site.email');
									$nanny_name                         =   $value->nanny;
									$full_name                          =   $value->name; 
									$email                              =   $value->email;
									
									$plan_start_date					=   date('m/d/Y', strtotime($earning->start_date));
									$plan_end_date						=   date('m/d/Y', strtotime($earning->end_date));
									$nanny_price						=   $currencyCode.$nannyprice;
									$currncy 							=   Config::get("Site.currencyCode");
									$emailActions                       =   EmailAction::where('action','=','send_user_invoice')->get()->toArray();
									$emailTemplates                     =   EmailTemplate::where('action','=','send_user_invoice')->get(array('name','subject','action','body'))->toArray();
									$cons                               =   explode(',',$emailActions[0]['options']);
									$constants                          =   array();
									foreach($cons as $key => $val){
										$constants[]                    = '{'.$val.'}';
									}
									$subject                            =   $emailTemplates[0]['subject'];
									$rep_Array                          =   array($full_name,$nanny_name,$plan_start_date,$plan_end_date,$nanny_price,$earning->total_nanny_working_hour, $planPrice,); 
									$messageBody                        =   str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
									$mail                               =   $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);



									//review Mail
									$validateString		            	=   md5(time() . $email);
									$review_url							 =   route('user.review-ratings',$validateString);
									$emailActions1                       =   EmailAction::where('action','=','give_review')->get()->toArray();
									$emailTemplates1                     =   EmailTemplate::where('action','=','give_review')->get(array('name','subject','action','body'))->toArray();
									$cons1                               =   explode(',',$emailActions1[0]['options']);
									$constants1                          =   array();
									foreach($cons1 as $key => $val){
										$constants1[]                    = '{'.$val.'}';
									}
									$subject1                            =   $emailTemplates1[0]['subject'];
									$rep_Array                          =   array($full_name,$nanny_name, $review_url); 
									$messageBody1                        =   str_replace($constants1, $rep_Array, $emailTemplates1[0]['body']);
									$mail1                               =   $this->sendMail($email,$full_name,$subject1,$messageBody1,$settingsEmail);
									//end review Mail

									//tip Mail
									$tipvalidateString		            	=   md5(time().rand(1000,9999) . $email);
									$tip_url							 =   route('user.tip',$tipvalidateString);
									$emailActions2                       =   EmailAction::where('action','=','send_tip')->get()->toArray();
									$emailTemplates2                     =   EmailTemplate::where('action','=','send_tip')->get(array('name','subject','action','body'))->toArray();
									$cons2                               =   explode(',',$emailActions2[0]['options']);
									$constants2                          =   array();
									foreach($cons2 as $key => $val){
										$constants2[]                    = '{'.$val.'}';
									}
									$subject2                            =   $emailTemplates2[0]['subject'];
									$rep_Array2                          =   array($full_name,$nanny_name,$tip_url); 
									$messageBody2                        =   str_replace($constants2, $rep_Array2, $emailTemplates2[0]['body']);
									$mail2                               =   $this->sendMail($email,$full_name,$subject2,$messageBody2,$settingsEmail);
									//end tip Mail


									DB::table('earnings')->where('id', $earning->id)->update([
										'validate_string' => $tipvalidateString, 'review_validate_string' => $validateString
									]);

								}
						}
					}
			}
		}
	}
	/*public function deductPaymentWeekly(){
		echo date("l");die;
		$bookings = DB::table('bookings')->select('bookings.*', 'n.name as nanny','u.name', 'u.email', 'u.customer_id', 'u.weekly_recurring')->leftJoin('users as u', 'u.id', '=', 'bookings.user_id')->where('bookings.status', 1)->leftJoin('users as n', 'n.id', 'bookings.nanny_id')->where('bookings.start_date', '<',  date('Y-m-d'))->where('bookings.end_date', '>=',  date('Y-m-d'))->where('u.is_deleted', 0)->where('u.deleted_at', NUll)->where('u.user_role_id', 2)->where('u.weekly_recurring', 1)->get();

		
		if(count($bookings) > 0){
			
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			foreach($bookings as $key=>$value){
				if($value->customer_id != ''){
					$days = DB::table('booking_details')->where('booking_id', $value->id)->orderBy('id', 'desc')->get();
					$hours = 0;

					$weekStart  = date("Y-m-d", strtotime('sunday this week'));
					$weekend    = date("Y-m-d", strtotime('saturday this week'));

					 	$date = date('Y-m-d');
						foreach($days as $day){
	                        if($day->monday == 1 && (date('D', strtotime($date)) == 'Mon')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->tuesday == 1 && (date('D', strtotime($date)) == 'Tue')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->wednesday == 1 && (date('D', strtotime($date)) == 'Wed')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->thursday == 1 && (date('D', strtotime($date)) == 'Thu')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->friday == 1 && (date('D', strtotime($date)) == 'Fri')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->saturday == 1 && (date('D', strtotime($date)) == 'Sat')){
	                        	$hours = $hours + 2;
	                        }
	                        if($day->sunday == 1 && (date('D', strtotime($date)) == 'Sun')){
	                        	$hours = $hours + 2;
	                        }
						}
 					 if($hours > 0){
						 $totalpaybleAmount = 0;
						 if(strtolower($value->user_country) == 'canada'){
						 	$price = Config::get('Site.nanny_default_price_for_canada');
						 }else{
						 	$price =  Config::get('Site.nanny_default_price_for_us');
						 }

						 $totalpaybleAmount = $price * $hours;
						 $planPrice          =   round($totalpaybleAmount);
							$charge             =   $this->stripe->charges()->create(array(
								"amount"   => $planPrice,
								"currency" => "USD",
								"customer" => $value->customer_id,
							));

							if($charge['status'] == 'succeeded'){
							   $amount = $charge['amount']/100;	
							   $earning = new Earning;
							   $earning->user_id 					= $value->user_id;
							   $earning->nanny_id 					= $value->nanny_id;
							   $earning->amount 					= $amount;
							   $earning->total_amount 				= $amount;
							   $earning->start_date 				= date("Y-m-d"); 
							   $earning->end_date 					= date("Y-m-d", strtotime('saturday this week'));
							   $earning->status 					= 1;
							   $earning->is_payout 					= 1;
							   $earning->booking_id 				= $value->id;
							   $earning->total_nanny_working_hour 	= $hours;
							   $earning->save();

							   
							    $self_history                       =  new UserTransactionHistory;
								$self_history->user_id              =  $value->user_id;
								$self_history->transaction_id       =  $charge['balance_transaction'];
								$self_history->order_number         =  rand().time();
								$self_history->transaction_amount   =  $amount;
								$self_history->booking_id 		    = $value->id;
								$self_history->transaction_currency =   Config::get("Site.currencyCode");
								$self_history->transaction_json_data =  json_encode($charge['source']);
								$self_history->save();

								$currencyCode = Config::get('Site.currencyCode');
							   $nannyprice = 0;
							   if(strtolower($value->user_country) == 'canada'){
							   	$nannyprice = Config::get('Site.nanny_default_price_for_canada');
							   	$currencyCode = Config::get('Site.CanadacurrencyCode');
							   }else{
							   	$nannyprice =  Config::get('Site.estimation_price_usa');
							   	$currencyCode = Config::get('Site.currencyCode');
							   }
							    if($nannyprice > 0){
							    	$nannyprice = number_format($nannyprice, 2);
							    } 

							    $totaworkigHour = $earning->total_nanny_working_hour;
							    $totalPlanprice  = $nannyprice * $totaworkigHour;
							    $planPrice 		=  $currencyCode.$totalPlanprice; 

								$settingsEmail                    	=   Config::get('Site.email');
								$nanny_name                         =   $value->nanny;
								$full_name                          =   $value->name; 
								$email                              =   $value->email;
								$validateString		            	=   md5(time() . $email);
								$tip_url              				=  	route('user.tip',$validateString);
								$plan_start_date					=   date('m/d/Y', strtotime($earning->start_date));
								$plan_end_date						=   date('m/d/Y', strtotime($earning->end_date));
								$nanny_price						=   $currencyCode.$nannyprice;
								$currncy 							=   Config::get("Site.currencyCode");
								$emailActions                       =   EmailAction::where('action','=','send_user_invoice')->get()->toArray();
								$emailTemplates                     =   EmailTemplate::where('action','=','send_user_invoice')->get(array('name','subject','action','body'))->toArray();
								$cons                               =   explode(',',$emailActions[0]['options']);
								$constants                          =   array();
								foreach($cons as $key => $val){
									$constants[]                    = '{'.$val.'}';
								}
								$subject                            =   $emailTemplates[0]['subject'];
								$rep_Array                          =   array($full_name,$nanny_name,$plan_start_date,$plan_end_date,$nanny_price,$earning->total_nanny_working_hour, $planPrice,); 
								$messageBody                        =   str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
								$mail                               =   $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);



								//review Mail
								$review_url							 =   route('user.review-ratings',$validateString);
								$emailActions1                       =   EmailAction::where('action','=','give_review')->get()->toArray();
								$emailTemplates1                     =   EmailTemplate::where('action','=','give_review')->get(array('name','subject','action','body'))->toArray();
								$cons1                               =   explode(',',$emailActions1[0]['options']);
								$constants1                          =   array();
								foreach($cons1 as $key => $val){
									$constants1[]                    = '{'.$val.'}';
								}
								$subject1                            =   $emailTemplates1[0]['subject'];
								$rep_Array                          =   array($full_name,$nanny_name, $review_url); 
								$messageBody1                        =   str_replace($constants1, $rep_Array, $emailTemplates1[0]['body']);
								$mail1                               =   $this->sendMail($email,$full_name,$subject1,$messageBody1,$settingsEmail);
								//end review Mail

								//tip Mail
								$tip_url							 =   route('user.tip',$validateString);
								$emailActions2                       =   EmailAction::where('action','=','send_tip')->get()->toArray();
								$emailTemplates2                     =   EmailTemplate::where('action','=','send_tip')->get(array('name','subject','action','body'))->toArray();
								$cons2                               =   explode(',',$emailActions2[0]['options']);
								$constants2                          =   array();
								foreach($cons2 as $key => $val){
									$constants2[]                    = '{'.$val.'}';
								}
								$subject2                            =   $emailTemplates2[0]['subject'];
								$rep_Array2                          =   array($full_name,$nanny_name,$tip_url); 
								$messageBody2                        =   str_replace($constants2, $rep_Array2, $emailTemplates2[0]['body']);
								$mail2                               =   $this->sendMail($email,$full_name,$subject2,$messageBody2,$settingsEmail);
								//end tip Mail


								DB::table('earnings')->where('id', $earning->id)->update(['validate_string' => $validateString, 'review_validate_string' => $validateString
								]);

							}

					 }
			    }	
			}
		    
		}
	}*/

	/*public function sendInvoiceWeekly()
	{
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		$users =  DB::table('users')->select('email', 'name', 'id', 'customer_id')->where('is_deleted', 0)->where('deleted_at', null)->where('weekly_recurring', 1)->get();

		foreach($users as $userkey=>$user){
			$earning =  DB::table('earnings')->where('user_id', $user->id)->where('status', 1)->where('end_date' , '>', date('Y-m-d h:i:s'))->get();
			if(count($earning) > 0){
				foreach($earning as $earning){
					$booking = DB::table('bookings')->where('id', $earning->booking_id)->where('status', 1)->first();
					  if(!empty($booking)){
						   $currencyCode = Config::get('Site.currencyCode');
						   $nannyprice = 0;
						   if(strtolower($booking->user_country) == 'canada'){
						   	$nannyprice = Config::get('Site.nanny_default_price_for_canada');
						   	$currencyCode = Config::get('Site.CanadacurrencyCode');
						   }else{
						   	$nannyprice =  Config::get('Site.estimation_price_usa');
						   	$currencyCode = Config::get('Site.currencyCode');
						   }
						    if($nannyprice > 0){
						    	$nannyprice = number_format($nannyprice, 2);
						    } 

						    $totaworkigHour = $earning->total_nanny_working_hour;
						    $totalPlanprice  = $nannyprice * $totaworkigHour;
						    $planPrice 		=  $currencyCode.$totalPlanprice;  

							$settingsEmail                    	=   Config::get('Site.email');
							$subcription_type                   =    "Weekly";
							$full_name                          =   $user->name; 
							$email                              =   $user->email;
							$validateString		            	=   md5(time() . $email);
							$tip_url              				=  	route('user.tip',$validateString);
							$plan_start_date					=   date('m/d/Y', strtotime($earning->start_date));
							$plan_end_date						=   date('m/d/Y', strtotime($earning->end_date));
							$nanny_price						=   $currencyCode.$nannyprice;
							$currncy 							=  Config::get("Site.currencyCode");
							$emailActions                       =   EmailAction::where('action','=','send_user_invoice')->get()->toArray();
							$emailTemplates                     =   EmailTemplate::where('action','=','send_user_invoice')->get(array('name','subject','action','body'))->toArray();
							$cons                               =   explode(',',$emailActions[0]['options']);
							$constants                          =   array();
							foreach($cons as $key => $val){
								$constants[]                    = '{'.$val.'}';
							}
							$subject                            =   $emailTemplates[0]['subject'];
							$rep_Array                          =   array($full_name,$subcription_type,$plan_start_date,$plan_end_date,$nanny_price,$planPrice,$earning->total_nanny_working_hour, $tip_url); 
							$messageBody                        =   str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
							$mail                               =   $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

							DB::table('earnings')->where('user_id', $user->id)->update(['validate_string' => $validateString]);
					  }
					   
				}
			}
		}
	}*/

	public function scheduleInteviewReminder(){
		$hours  = 2;

		$interviews  = ScheduleInterview::leftjoin('day_availabilities', 'schedule_interview.day_availabilities_id', '=', 'day_availabilities.id')->where('schedule_interview.is_reminder',0)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->select("schedule_interview.*" ,"day_availabilities.from_time",'day_availabilities.time_slot')->get();

		foreach ($interviews as $key => $value) {

			$nanny = User::where('id',$value->nanny_id)->first();
			$user  = User::where('id',$value->user_id)->first();
			// echo"<pre>";print_r($value);die;
			$date  = date('d-m-Y',strtotime($value->interview_date));
			//mail to admin
			$email 			    	=	Config::get('Admin.email');
			$full_name				= 	'Admin'; 
			$settingsEmail		    = 	Config::get('Site.to_email');
			$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder_to_admin')->get()->toArray();
			$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder_to_admin')->get(array('name','subject','action','body'))->toArray();
			$cons 					= 	explode(',',$emailActions[0]['options']);
			$constants 				= 	array();
			foreach($cons as $key => $val){
				$constants[] 		= 	'{'.$val.'}';
			}
			$subject 				= 	$emailTemplates[0]['subject'];
			$rep_Array 				= 	array($user->name,$nanny->name,$date,$value->time_slot);  
			$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
			$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);


			//mail to user
			$email 			    	=	$user->email;
			$full_name				= 	$user->name; 
			$settingsEmail		    = 	Config::get('Site.to_email');
			$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
			$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
			$cons 					= 	explode(',',$emailActions[0]['options']);
			$constants 				= 	array();
			foreach($cons as $key => $val){
				$constants[] 		= 	'{'.$val.'}';
			}
			$subject 				= 	$emailTemplates[0]['subject'];
			$rep_Array 				= 	array($user->name,$date,$value->time_slot);  
			$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
			$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

			//mail to nanny
			
			$email 			    	=	$nanny->email;
			$full_nanny_name		= 	$nanny->name; 
			$settingsEmail		    = 	Config::get('Site.to_email');
			$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
			$emailTemplates	    	= 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
			$cons 					= 	explode(',',$emailActions[0]['options']);
			$constants 				= 	array();
			foreach($cons as $key => $val){
				$constants[] 		= 	'{'.$val.'}';
			}
			$subject 				= 	$emailTemplates[0]['subject'];
			$rep_Array 				= 	array($full_nanny_name,$date,$value->time_slot);  
			$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
			$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);


			ScheduleInterview::where('id',$value->id)->update(array('is_reminder'=>1));

		}
	}

	public function remiderInterviewBefore5Minute()
	{
		$interviews  = ScheduleInterview::where('schedule_interview.is_reminder_before_5_minute',0)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->select("schedule_interview.*")->get();

		if(!empty($interviews)){
			foreach($interviews as $key=>$value){
                $timeSlotData = explode('-', $value->meeting_day_time); 
                $fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
                //$toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):''; 
                $todayDate = strtotime (date('Y-m-d H:i')); 
			    $interviewStartTime = strtotime($value->interview_date.$fromTIme);
			    $duration='-5 minutes';
				$time1 = date('Y-m-d h:i', strtotime($duration, $interviewStartTime));
				//$time1 = date('Y-m-d h:i', strtotime('2021-07-16 09:15'));
			    $time2 = date('Y-m-d h:i');
			    if(strtotime($time1) == strtotime($time2)){
			    	$nanny = User::where('id',$value->nanny_id)->first();
					$user  = User::where('id',$value->user_id)->first();
					// echo"<pre>";print_r($value);die;
					$date  = date('d-m-Y',strtotime($value->interview_date));
					//mail to admin
					$email 			    	=	Config::get('Admin.email');
					$full_name				= 	'Admin'; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder_to_admin')->get()->toArray();
					$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder_to_admin')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($user->name,$nanny->name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);


					//mail to user
					$email 			    	=	$user->email;
					$full_name				= 	$user->name; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
					$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($user->name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

					//mail to nanny
					
					$email 			    	=	$nanny->email;
					$full_nanny_name		= 	$nanny->name; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
					$emailTemplates	    	= 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($full_nanny_name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);


					ScheduleInterview::where('id',$value->id)->update(array('is_reminder_before_5_minute'=>1));
			    }
			}
		}

	}


	public function remiderInterviewBefore15Minute()
	{
		$interviews  = ScheduleInterview::where('schedule_interview.is_reminder_before_15_minute',0)->where('schedule_interview.interview_date', '>=', date('Y-m-d'))->select("schedule_interview.*")->get();

		if(!empty($interviews)){
			foreach($interviews as $key=>$value){
                $timeSlotData = explode('-', $value->meeting_day_time); 
                $fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
                //$toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):''; 
                $todayDate = strtotime (date('Y-m-d H:i')); 
			    $interviewStartTime = strtotime($value->interview_date.$fromTIme);
			    $duration='-15 minutes';
				$time1 = date('Y-m-d h:i', strtotime($duration, $interviewStartTime));
				//$time1 = date('Y-m-d h:i', strtotime('2021-07-16 09:15'));
			    $time2 = date('Y-m-d h:i');
			    if(strtotime($time1) == strtotime($time2)){
			    	$nanny = User::where('id',$value->nanny_id)->first();
					$user  = User::where('id',$value->user_id)->first();
					// echo"<pre>";print_r($value);die;
					$date  = date('d-m-Y',strtotime($value->interview_date));
					//mail to admin
					$email 			    	=	Config::get('Admin.email');
					$full_name				= 	'Admin'; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder_to_admin')->get()->toArray();
					$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder_to_admin')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($user->name,$nanny->name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);


					//mail to user
					$email 			    	=	$user->email;
					$full_name				= 	$user->name; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
					$emailTemplates	    = 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($user->name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);

					//mail to nanny
					
					$email 			    	=	$nanny->email;
					$full_nanny_name		= 	$nanny->name; 
					$settingsEmail		    = 	Config::get('Site.to_email');
					$emailActions			= 	EmailAction::where('action','=','interview_schedule_reminder')->get()->toArray();
					$emailTemplates	    	= 	EmailTemplate::where('action','=','interview_schedule_reminder')->get(array('name','subject','action','body'))->toArray();
					$cons 					= 	explode(',',$emailActions[0]['options']);
					$constants 				= 	array();
					foreach($cons as $key => $val){
						$constants[] 		= 	'{'.$val.'}';
					}
					$subject 				= 	$emailTemplates[0]['subject'];
					$rep_Array 				= 	array($full_nanny_name,$date,$fromTIme);  
					$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
					$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);


					ScheduleInterview::where('id',$value->id)->update(array('is_reminder_before_15_minute'=>1));
			    }
			}
		}

	}

	/*public function sheduleZoomMetting()
	{
			$interViews = ScheduleInterview::where('interview_date', date('Y-m-d'))->get();

			if(!empty($interViews)){
				print_r($interViews);die;	
			}


		        $todatDate = date('Y-m-d').'T'.'12:44:00'.'Z';
				$encoded_params = json_encode(array("topic"=> 'Interview',"type"=>"2",'start_time'=>$todatDate, "duration"=>(String)60,'timezone'=>"IST",'agenda'=> "Interview"));


				$URL= "https://api.zoom.us/v2/users/me/meetings";
				$ch 			= 	curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array (
															"Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6Iks2bkRkUHhpUTNtM2hLU1ViZWF3M2ciLCJleHAiOjE5MDg2MjM2NDAsImlhdCI6MTYyNDYyMTkzNH0.Lx2ifZQ3uvAuM9qw8M5isCy0dxCdPfXuW0N3n2cfTUc",
															'Content-Type: application/json'
														));
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_params);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				$result = curl_exec($ch);
				$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$result     = json_decode($result, true);
				$outPutArr	=	array('status_code'=>$http_status, 'result'=>$result);
				echo "<pre>";print_r($outPutArr);die;
				die;

	}*/

	public function payOutWeeklyPayment()
	{
	  	if(date("l") == "Friday"){
	   		$earnings =  Earning::where('is_payout',0)->select('nanny_id',DB::raw('SUM(amount) AS sum_a'))->groupBy('nanny_id')->get();
			//$earnings =  Earning::where('is_payout',0)->where('start_date', '<',  date('Y-m-d'))->where('end_date', '>=',  date('Y-m-d'))->get();
			//echo "<pre>";print_r($earnings);die;
	   		if(!empty($earnings)){
	   	    	$amout = 0; 
	   	    	$earningId = array();
	   			foreach($earnings as $key=>$value){
	   		 		$payoutObj = 	new Payout;
	   		 		$payoutObj->nanny_id = $value->nanny_id;
	   		 		$payoutObj->amount = $value->sum_a;
	   		 		$payoutObj->actual_amout = $value->sum_a;
	   		 		$payoutObj->payout_date = date('Y-m-d');
	   		 
	   		 		if($payoutObj->save()){
		   		 		$payoutId = $payoutObj->id;
		   		 		$nannys = Earning::where('is_payout',0)->where('nanny_id',$value->nanny_id)->get();

		   		 		foreach($nannys as $nanny){
		   		 			$payoutDetail 				=  new PayoutDetail;
		   		 			$payoutDetail->invoice_id 	= $nanny->id;
		   		 			$payoutDetail->nanny_id 	= $nanny->nanny_id;
		   		 			$payoutDetail->payout_id 	= $payoutId;
		   		 			$payoutDetail->amount 		= $nanny->nanny_id;
		   		 			$payoutDetail->save();
		   		 			Earning::where('is_payout', 0)->where('nanny_id', $nanny->nanny_id)->update(['is_payout' => 1]);
		   		 		}

		   		 		$earming =  new Earning;
		   		 		$earming->nanny_id = 2;
		   		 		$earming->type = 3;
		   		 		$earming->amount = $value->sum_a;;
		   		 		$earming->status = 2;
		   		 		$earming->payout_id = $payoutId;
		   		 		$earming->save();

		   		 		//mail to nanny
						$userDetail             =   DB::table('users')->where('id',$value->nanny_id)->select("name","email")->first();
						$email 			    	=	$userDetail->email;
						$full_nanny_name		= 	$value->sum_a;
						$amount					= 	$userDetail->name; 
						$payemtDate				= 	 date('m/d/Y'); 
						$settingsEmail		    = 	Config::get('Site.to_email');
						$emailActions			= 	EmailAction::where('action','=','nanny_payment_email')->get()->toArray();
						$emailTemplates	    	= 	EmailTemplate::where('action','=','nanny_payment_email')->get(array('name','subject','action','body'))->toArray();
						$cons 					= 	explode(',',$emailActions[0]['options']);
						$constants 				= 	array();
						foreach($cons as $key => $val){
							$constants[] 		= 	'{'.$val.'}';
						}
						$subject 				= 	$emailTemplates[0]['subject'];
						$rep_Array 				= 	array($full_nanny_name,$payemtDate,$amount);  
						$messageBody			= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
						$mail					= 	$this->sendMail($email,$full_nanny_name,$subject,$messageBody,$settingsEmail);

		   		 	}
	   		 	
	   		  	}
	   		}
	   	}
	}

	public function addContactList(Request $request){
		$result  = NewsletterContact::where('is_added',0)->pluck('contact_id')->toArray();
		if(!empty($result)){
			$accessToken = DB::table('constantcontact')->where('id',1)->value('access_token');
			$params = json_encode(array("source"=>array('contact_ids'=>$result),"list_ids"=>"05871006-d95a-11eb-9c52-fa163e2743c5"));
			$defaults = array(
				CURLOPT_URL => 'https://api.cc.email/v3/activities/add_list_memberships',
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $params
			);
			$ch = curl_init();
			$header = array();
			$header[] = 'Content-type: application/json';
			$header[] = 'Authorization: Bearer '.$accessToken;
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt_array($ch, $defaults);
			$rest = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpcode == '201'){
				//$result = json_decode($rest,true);
			}
			NewsletterContact::where('is_added',0)->update(['is_added'=>1]);
		}
		die;
	}		
	
	public function refreshToken(Request $request){
		$accessToken = DB::table('constantcontact')->where('id',1)->first();
		if(!empty($accessToken)){
			$ch = curl_init();
			// Define base URL
			$base = 'https://idfed.constantcontact.com/as/token.oauth2';
			// Create full request URL
			$url = $base . '?refresh_token=' . $accessToken->refresh_token . '&grant_type=refresh_token';
			curl_setopt($ch, CURLOPT_URL, $url);
			$auth = "69ffa720-ece3-4f4b-ae96-612521ecb95d" . ':' . "7FOqrQaCOmEd4IeSGSKSiA";
			// Base64 encode it
			$credentials = base64_encode($auth);
			// Create and set the Authorization header to use the encoded credentials
			$authorization = 'Authorization: Basic ' . str_replace(" ","",$credentials);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));
			// Set method and to expect response
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Make the call
			$result = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpcode == '200'){
				$result = json_decode($result,true);
				DB::table('constantcontact')->where('id',1)->update(array('access_token'=>$result['access_token'],'refresh_token'=>$result['refresh_token']));
			}
			die;
			
		}	
	}
}
