<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB,Str,Session,Auth,View,Config,Validator;
use App\Models\GeoLocationCode;
use App\Models\User;
use App\Models\Category;
use App\Models\UserLanguage;
use App\Models\EscortHairColor;
use App\Models\EscortFigure;
use App\Models\EscortBodyHair;
use App\Models\EscortHairLength;
use App\Models\EscortIntimateHair;
use App\Models\EscortPiercing;
use App\Models\EscortService;
use App\Models\EscortSubService;
use App\Models\EscortSexuality;
use App\Models\BustSize;
use App\Models\Escort;
use App\Models\Image;
use App\Models\EscortLanguage;
use App\Models\ServiceTag;
use App\Models\UserTag;
use App\Models\EscortTime;
use GuzzleHttp;
use Hash,Mail,File;

class AccountController extends BaseController
{
    
    public function createAd()
    {   
       
        $categories     = Category::pluck('name','id')->toArray();
        $laguages       = UserLanguage::get(['name','id']);
        $haircolors     = EscortHairColor::pluck('name','id')->toArray();
        $IntimateHairs  = EscortIntimateHair::pluck('name','id')->toArray();
        $piercings      = EscortPiercing::get(['name','id']);
        $sezes          = BustSize::get(['name','id']);
        $locations = GeoLocationCode::pluck('location_name', 'location_id')->toArray();
        $locations = array_filter(array_unique($locations));
        $postal_code = GeoLocationCode::pluck('postal_code', 'postal_code')->toArray();
        $postal_code = array_unique($postal_code);

        return View::make('frontend.myaccount.create_ad', compact('categories','laguages','haircolors','IntimateHairs', 'piercings','sezes','locations', 'postal_code'));
    }

   public function createAdSave(Request $request)
   {
        $request->replace($this->arrayStripTags($request->all()));
        $formData   =   $request->all();
        if(!empty($formData)){
            $validator = Validator::make(
                $request->all(),
                array(
                    'titel'             => 'required',
                    'gender'            => 'required',
                    'age'               => 'required',
                    'laguages'          => 'required|min:1',
                    'laguages.*'        => 'required',
                    'sezes'             => 'required|min:1',
                    'sezes.*'           => 'required',
                    'services'          => 'required',
                    'services.*'        => 'required|min:1',
                    'phone_number'       => 'required',
                ),
                array(
                    'titel.required'            => 'This title field is required',
                    'gender.required'           => 'This gender field is required',
                    'age.required'              => 'This age field is required',
                    'phone_number.required'     => 'This phone number field is required',
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

                   //echo "<pre>"; print_r($image);die; 
                   $slug = Str::slug($request->titel);
                   echo "<pre>";print_r($request->all());
                   $obj = new Escort;
                   $obj->user_id            = Auth::user()->id;
                   $obj->name               = $request->titel; 
                   $obj->category_id        = $request->category;
                   $obj->type_of_escort     = 'escort';
                   $obj->age                = $request->age;
                   $obj->gender             = $request->gender;
                   $obj->mobile_number      = $request->phone_number;
                   $obj->distance           = 0;
                   $obj->active             = 0;
                   $obj->approved           = 0;
                   $obj->slug               = $slug;
                   $obj->bust_size_id       = $request->sezes;
                   $obj->hair_color_id      = $request->hair_color;
                   $obj->intimate_hair_id   = $request->intimate_hair;
                   $obj->piercing_id        = $request->piercings;
                   if($obj->save()){
                    $objTime                    = new EscortTime;
                    $objTime->escort_id         = $obj->id;
                    $objTime->working_hour      = $request->working_hour;
                    $objTime->monday            = !empty($request->monday) ? $request->monday:'';
                    $objTime->monday_from       = !empty($request->monday_from_time) ? $request->monday_from_time:'';
                    $objTime->monday_to         = !empty($request->monday_to_time) ? $request->monday_to_time:'';
                    $objTime->tuesday           = !empty($request->tuesday) ? $request->tuesday:'';
                    $objTime->tuesday_from      = !empty($request->tuesday_from_time) ? $request->tuesday_from_time:'';
                    $objTime->tuesday_to        = !empty($request->tuesday_to_time) ? $request->tuesday_to_time:'';
                    $objTime->wednesday         = !empty($request->wednesday) ? $request->wednesday:'';
                    $objTime->wednesday_from    = !empty($request->wednesday_from_time) ? $request->wednesday_from_time:'';
                    $objTime->wednesday_to      = !empty($request->wednesday_to_time) ? $request->wednesday_to_time:'';
                    $objTime->thursday          = !empty($request->thursday) ? $request->thursday:'';
                    $objTime->thursday_from     = !empty($request->thursday_from_time) ? $request->thursday_from_time:'';
                    $objTime->thursday_to       = !empty($request->thursday_to_time) ? $request->thursday_to_time:'';
                    $objTime->friday            = !empty($request->friday) ? $request->friday:'';
                    $objTime->friday_from       = !empty($request->friday_from_time) ? $request->friday_from_time:'';
                    $objTime->friday_to         = !empty($request->friday_to_time) ? $request->friday_to_time:'';
                    $objTime->saturday          = !empty($request->saturday) ? $request->saturday:'';
                    $objTime->saturday_from     = !empty($request->saturday_from_time) ? $request->saturday_to_time:'';
                    $objTime->saturday_to       = !empty($request->saturday_to_time) ? $request->saturday_to_time:'';
                    $objTime->sunday            = !empty($request->sunday) ? $request->sunday:'';
                    $objTime->sunday_from       = !empty($request->sunday_from_time) ? $request->sunday_from_time:'';
                    $objTime->sunday_to         = !empty($request->sunday_to_time) ? $request->sunday_to_time:'';
                    $objTime->save();

                        if(!empty($request->laguages)){
                            foreach($request->laguages as $keylang=>$valuelng){
                                
                                if(!empty($valuelng)){
                                    $objlng                     = new EscortLanguage;
                                    $objlng->languagable_id     = $obj->id;
                                    $objlng->language_id        = $valuelng;
                                    $objlng->languagable_type   = 'App\Escort';
                                    $objlng->created_at         = date('Y-m-d H:i:s');
                                    $objlng->updated_at         = date('Y-m-d H:i:s');
                                    $objlng->save();
                                    echo  $objlng->languagable_id;
                                }
                            }
                        }


                        if(!empty($request->services)){
                            foreach($request->services as $keyservice=>$valueservice){
                                if(!empty($valueservice)){
                                   $spliteService = explode('-', $valueservice);
                                   if(!empty($spliteService)){
                                     $objServicetag = new UserTag;
                                     $objServicetag->taggable_id    = $obj->id;
                                     $objServicetag->taggable_type  = 'App\Escort';
                                     $objServicetag->tag_id         = !empty($spliteService[1]) ? $spliteService[1]:0;
                                     $objServicetag->save();
                                   }
                                   if(!empty($spliteService)){
                                     $objServicetag = new ServiceTag;
                                     $objServicetag->service_id = !empty($spliteService[0]) ? $spliteService[0]:0;
                                     $objServicetag->tag_id     = !empty($spliteService[1]) ? $spliteService[1]:0;
                                     $objServicetag->save();
                                   }

                                }
                            }
                           
                        }
                        if($request->hasfile('files'))
                         {
                            foreach($request->file('files') as $file)
                            {
                                $extension  =    $file->getClientOriginalExtension();
                                $fileName   =   time().'-image.'.$extension;
                                
                                $folderName         =   strtoupper(date('M'). date('Y'))."/";
                                $folderPath         =   USERAD_IMAGE_ROOT_PATH.$folderName;
                                if(!File::exists($folderPath)) {
                                    File::makeDirectory($folderPath, $mode = 0777,true);
                                }
                                if($file->move($folderPath, $fileName)){
                                   $imageObj                    =  new Image;
                                    $imageObj->imageable_id     =   $obj->id;
                                   $imageObj->path              =   $folderName.$fileName;
                                   $imageObj->imageable_type    =   'App\Escort';
                                   $imageObj->status            =   'fsk16';
                                   $imageObj->save();
                                }
                                
                            }
                         }

                         
                   }

                   echo "success";die;
                   Session::flash('success', 'Persanal details change successfully');
                    return response()->json(['success' => 1, 'page_redirect' => url('/my-ad'),'message'=>'Persanal details change successfully']);

                   /* $user = User::find(Auth::user()->id);
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
                    return response()->json(['success' => 1, 'page_redirect' => url('/account'),'message'=>'Persanal details change successfully']);*/
                   
                }

        }

   } 

    public function myAdvertise()
    {
         $escorts =  Escort::where('user_id', Auth::user()->id)->with('escortimages')->with('receivedcity')->get();
         //echo "<pre>";print_r($escorts);die;
      return View::make('frontend.myaccount.my_ad', compact('escorts'));  
    }


}