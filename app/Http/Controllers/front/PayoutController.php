<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB,Config,Auth;
use App\Model\User;
use App\Model\Earning;
use App\Model\Payout;

class PayoutController extends BaseController
{


	public function payouts()
	{
		$date		=	date('d-m-Y');
		// $date		=	'18-06-2021';
		$todayDay 	= 	date("D",strtotime($date));
		$nannyID 	=   Auth::user()->id;

		if($todayDay == 'Fri'){

			$totalAmount = Earning::where('nanny_id',$nannyID)->where('is_payout',0)->where('type',1)->sum('amount');

			if($totalAmount > 0){

				$payout 					 =  new Payout;
				$payout->nanny_id		     =  $nannyID;
				$payout->amount		         =  $totalAmount; 
				$payout->payout_date		 =  date('Y-m-d'); 
				$payout->save();

				Earning::where('nanny_id',$nannyID)->where('is_payout',0)->where('type',1)->update(['is_payout' => 1]);

			}
			
		}
	}
	


}
