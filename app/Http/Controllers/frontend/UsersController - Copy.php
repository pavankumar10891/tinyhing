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

use Auth,Blade,Config,Cache,Cookie,DB,File,Mail,Response,URL,CustomHelper;



class UsersController extends BaseController {
	
/** 
* Function to redirect website on main page
*
* @param null
* 
* @return
*/
public function index(){
	//echo "string";die;
	return View::make('front.users.index');
}



}// end UsersController class
