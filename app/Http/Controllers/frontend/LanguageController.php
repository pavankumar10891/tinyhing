<?php
namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use View;
use Input;
use Validator;
use Hash;
use Session;
use App\Model\User;
use App;
class LanguageController extends Controller
{
   public function switchLang(Request $request){
        $lang = $request->lang;
        Session::put('applocale', $lang);
        App::setLocale($lang);
        
        return Redirect::back();
    }
}

