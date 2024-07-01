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
use App\Model\User;
use App\Model\Testimonial;
use App\Model\WhyChooseUs;
use App\Model\OurCoreValues;
use App\Model\Cms;
use App\Model\Block;
use App\Model\Partners;
use App\Model\Banner;
use App\Model\Blog;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\NewsLettersubscriber;
use Auth,Blade,Config,Cache,Cookie,DB,File,Mail,Response,URL;
use Socialite;
use App\Model\Booking;
use Illuminate\Support\Facades\Crypt;

class BookingController extends BaseController {
	
/** 
* Function to redirect website on main page
*
* @param null
* 
* @return
*/
	
	public function stopClientBooking(Request $request)
	{
		if(Auth::user()){
			$bookingid = Crypt::decrypt($request->bookingid);
			if(empty($request->stop_reason)) {
		        return response()->json(['success' => false, 'message' => 'Please enter the stop reason before stoping the request.']);
		    }else{
		    	$booking = Booking::where('id', $bookingid)->first();
		    	$booking->status = 2;
		    	$booking->client_stop_reason = $request->stop_reason;
		    	$booking->stop_date 		 = date('Y-m-d');
		    	$booking->updated_by 		 = Auth::user()->id;
		    	$booking->save();
		    	Session::flash('success','booking successfully stop');
		    	return response()->json(['success' => true, 'message' => 'booking successfully stop']);
		    }
		}
	}

	public function restartClientBooking($id)
	{   
		if(!empty($id)){
			if(Auth::user()){
			    	$booking = Booking::where('id',$id)->first();
			    	if(!empty($booking)){
				    	$booking->status = 1;
				    	$booking->restart_date 		 = date('Y-m-d');
				    	$booking->updated_by 		 = Auth::user()->id;
				    	$booking->save();
				    	Session::flash('success','booking successfully start again');
				    	return Redirect::back();
			    	}else{
			    		Session::flash('error','something went to wrong');
			    		return Redirect::back();
			    	}
			    	
			}
		}
		
	}


	
	
}// end BookingController class
