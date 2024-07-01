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
use App\Models\User;
use App\Models\Escort;
use App\Models\EscortFigure;
use App\Models\UrlType;
use App\Models\Category;
use App\Models\GeoLocationCode;
use Str,DB,Config;
use App\Models\Image;


class AdController extends Controller
{
    public function addDetailsPage(Request $request)
    {
    	//echo "sa";die;
		$slug_arr           =   array();
		$attribute_arr      =   array();
		$service_radius     =  isset($request->radius) ? $request->radius:9999999999;
		$pagelimit 			=  !empty($request->page_no) ? $request->page_no :20; 
		if($service_radius==0){
			$service_radius     =  9999999999;
		}
		$userLatlong        =  Session::get('user_lat_long');
		$pageLimit          = 20;
		$check = 0;
		$regex 							= '/^[^0-9][_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';


		if(!empty($request->escort_id)){
			return $this->getAdDetails($request->escort_id);
		}
		
		$sag1 = request()->segment(2);
		$sag2 = request()->segment(3);

		if($sag1 != "" && strpos($sag1, ':') === false){
			$Code =  explode('-',  $sag1);
		   if(is_numeric($Code[0])){
			   $slug_arr[] =   $Code[0]; 
		   }else{
		   	$slug_arr[] =   $sag1;
		   	
		   }
			
		}else if($sag1 != ""){
			$attribue_v = explode('&', $sag1);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}

		//echo "<pre>";print_r($slug_arr);die;

		if(request()->segment(1) == 'reset-password'){

			 if(filter_var($sag1,FILTER_VALIDATE_EMAIL)){

			 	$userDetail	=	User::where('validate_string',$sag2)->first();
			 	if($userDetail){
			 		$email = $userDetail->email;
			 		return View::make('frontend.users.reset_password',compact('email'));
			 	}else{
			 		Session::flash('error', 'email does not exits');
					return Redirect::to('/');
			 	}
			 }else{
			 	Session::flash('error', 'Somthing went wrong. Please again after some time.');
				return Redirect::to('/');
			 }
		}

		//email vefication for change email
		if(filter_var($sag1,FILTER_VALIDATE_EMAIL)){
			$validate_string = request()->segment(3);
			if($validate_string!="" && $validate_string!=null){
				$userDetail	=	User::where('validate_string',$validate_string)->first();
				if(!empty($userDetail)){

					if($userDetail->email_verified_at	 == null && $userDetail->username != null) {
						Session::flash('error', 'Your are using wrong link.');
						return Redirect::to('/');
					}else{
						  $checkUser = User::where('email',$sag1)->where('validate_string',null)->where('username','!=',null)->first();
						  if(!empty($checkUser)){
						   Session::flash('error', 'Email Address already exits.');
								return Redirect::to('/');
						  }else{
						  	Session::flash('Success', 'Email Vefified Successfully');
						  	if($userDetail->form_type == 'register' && $userDetail->username == Null ||  $userDetail->username == ''){
						  		//User::where('id', $userDetail->id)->update(['email' => $sag1, 'validate_string' => '', 'form_type'=> '' ]);
						  		 //User::where('id', $userDetail->id)->update(['validate_string' => '']);
						  		 $postal_code = GeoLocationCode::pluck('postal_code', 'postal_code')->toArray();
        					 $postal_code = array_unique($postal_code);
        					 $locations = GeoLocationCode::pluck('location_name', 'location_id')->toArray();
        					 $locations = array_filter(array_unique($locations));
						  		return View::make('frontend.users.register')->with(['email' => $sag1,'postal_code' => $postal_code, 'locations' => $locations]);
						  	}else{
						  		User::where('id', $userDetail->id)->update(['email' => $sag1, 'validate_string' => '']);
									return Redirect::to('/');
						  	}
						  	
						  }
						
					}
				}else{
					  Session::flash('error', 'Your are using wrong link.');
						return Redirect::to('/');
				}
			}else{
				Session::flash('error', 'Somthing went wrong. Please again after some time.');
				return Redirect::to('/');
			}
		}


		
		if($sag2 != "" && strpos($sag2, ':') === false){
			
			$slug_arr[] =   $sag2;
		  
		}else if($sag2 != ""){
			$attribue_v = explode('&', $sag2);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}

		$sag3 = request()->segment(4);
		if($sag3 != "" && strpos($sag3, ':') === false){
		   
		   $slug_arr[] =   $sag3;
		}else if($sag3 != ""){
			$attribue_v = explode('&', $sag3);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}
		
		$sag4 = request()->segment(5);
		if($sag4 != "" && strpos($sag4, ':') === false){
		   
			$slug_arr[] =   $sag4;
		}else if($sag4 != ""){
			$attribue_v = explode('&', $sag4);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}

		$sag5 = request()->segment(6);
		if($sag5 != "" && strpos($sag5, ':') === false){
			$slug_arr[] =   $sag5;
		}else if($sag5 != ""){
			$attribue_v = explode('&', $sag5);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}

		$sag6 = request()->segment(7);
		if($sag6 != "" && strpos($sag6, ':') === false){
		   $slug_arr[] =   $sag6;
		}else if($sag6 != ""){
			$attribue_v = explode('&', $sag6);
			if(!empty($attribue_v)){
				foreach($attribue_v as $attribue){
					$attribute_arr[] = $attribue;
				}
			}
		}
		$search_attributes	=	['figure', 'type', 'hair_color'];
		$bust_sizes			=	['mini'=>1, 'klein'=>2, 'normal'=>3, 'groß'=>4, 'riesig'=>5];     
		$escorts =  Escort::with('escortimages')->with('receivedcity');
		$dataArr = UrlType::whereIn('url_value', $slug_arr)->get()->toArray();

		$fromAge = '';
		$toAge = '';
		$fromheight = '';
		$toheight = '';
		
		//echo "<pre>";
		//print_r($dataArr);die;
		if(empty($dataArr)){
		   return Redirect::back();
		}
		
		if(!empty($attribute_arr)){
			
			
			foreach($attribute_arr as $attribute_arr_v){
				$e_attribute_arr_v  =   explode(":",$attribute_arr_v);
				$attribue_key       =   $e_attribute_arr_v[0];
				$attribue_value     =   $e_attribute_arr_v[1];

				if($attribue_key == 'bust_size'){
					$bustsizeValue = explode(',',$attribue_value); 
					if(!empty($bustsizeValue)){
						$newbustsizes 	=	[];
						foreach($bustsizeValue as $bust_size){
							if(isset($bust_sizes[$bust_size])){
								$newbustsizes[]	=	$bust_sizes[$bust_size];
							}
						}
						$escorts->whereIn('bust_size_id',$newbustsizes); 
					}
				}else if($attribue_key == 'age'){
				   $ageValue = explode(',',$attribue_value); 
					if(count($ageValue) == 1){
						$escorts->where('age','=',str_replace("year", "", $attribue_value));
					}else{
						$escorts->whereBetween('age',[str_replace("year", "", $ageValue[0]), str_replace("year", "", $ageValue[1])]);
					}
				}else if($attribue_key == 'height'){
					$heightexplode = explode(',',$attribue_value);
					if(count($heightexplode) == 1){
						$escorts->where('height','=',str_replace("cm", "", $attribue_value));
					}else{
						$escorts->whereBetween('height',[str_replace("cm", "", $ageValue[0]), str_replace("cm", "", $ageValue[1])]);
					}
				}else if($attribue_key == 'from_height'){
					$fromheight = $attribue_value; 
				   $escorts->where('height','=',str_replace("cm", "", $attribue_value));
				}else if($attribue_key == 'to_height'){
					$toheight = $attribue_value; 
				   $escorts->where('height','>=',str_replace("cm", "", $attribue_value));
				}else if($attribue_key == 'radius'){
					if($attribue_value!="without"){
						$service_radius	=	str_replace("km", "", $attribue_value); 
					}
				}else{
					$featureexplode = explode(',',$attribue_value);
					
					if(in_array($attribue_key, $search_attributes)){
						$relation_table_name    =   "escort_".$attribue_key."s";
						$relation_table_id      =  $attribue_key."_id";
						$escorts->leftjoin($relation_table_name,$relation_table_name.".id","escorts.".$relation_table_id);

						if($attribue_key == 'figure'){
							if(count($featureexplode) == 1){
							   $escorts->where($relation_table_name.".slug",$attribue_value);
							} else{
							  $escorts->whereIn($relation_table_name.".slug",$featureexplode);  
							} 
						}else{
							$escorts->where($relation_table_name.".name",$attribue_value); 
						}
					}
				}
			}
		}
	   
		if(!empty($fromheight) && is_numeric($fromheight) && $toheight != '' && is_numeric($toheight)){
			$escorts->whereBetween('escorts.height',[$fromAge,$toheight]);
		}
		$escorts->leftJoin('user_tags', 'user_tags.taggable_id', 'escorts.id');
		if(!empty($dataArr)){
			foreach($dataArr as $dataArr_v){
				if($dataArr_v["url_type"] == "escort"){
				   
					$escorts->escort_images = null;
					$profileData = $escorts->where("escorts.id",$dataArr_v["url_id"])
					->with('category')->with('sexuality')->with('figure')->with('escorttype')->with('escortPiercing')->where('type_of_escort', 'escort')->orderBy('escorts.created_at', 'desc')->select('escorts.*')->first();
					$profileData->escort_images = array();

					
					$subSericeArray = array();
					$escortServiefor = '';
					$language = array();
					if(!empty($profileData)){
						$serviceArray = array();
						 $tagsArray = array();

						$profileData->escort_images = Image::where('imageable_id', $profileData->id)->get();

						 
						$user_tags = DB::table('user_tags')->where('taggable_id', $profileData->id)->get();
						if(!empty($user_tags)){
							foreach($user_tags as $ks=>$vs){
								$tagsArray[] = $vs->tag_id;
							}

							if(!empty($tagsArray)){
								$serviceArray = DB::table('service_tags')->leftjoin('services', 'services.id', 'service_tags.service_id')->whereIn('tag_id', $tagsArray)->select('services.name', 'service_tags.*')->get();



								if(!empty($serviceArray)){
									$checkSerice = array();
									foreach ($serviceArray as $keyss => $valuess) {
										$singleService                  = array();
										$tags                           =  DB::table('tags')->where('id', $valuess->tag_id)->get();
										$singleService['service_name']  = $valuess->name;
										$singleService['tags']          = array();
										foreach($tags as $tag){
											$tagArray = array(); 
											$tagArray['tag_name']       = $tag->name;
											$tagArray['tag_id']         = $tag->id; 
											$singleService['tags'][]      = $tagArray;
										}
										$subSericeArray[] = $singleService;
									}
								}

							   
							}
						}
						$escortServiefor = DB::table('escort_escort_service_for')->leftjoin('escort_services_for', 'escort_services_for.id', '=', 'escort_escort_service_for.escort_service_for_id')->where('escort_escort_service_for.escort_id',$profileData->id)->pluck('escort_services_for.name')->toArray();

						$escorts =  Escort::with('escortimages')
						->leftjoin('categories', 'categories.id','=','escorts.category_id')
						->with('receivedcity')
						->where('escorts.deleted_at', NULL)
						->where('escorts.active', 1)
						->where('escorts.type_of_escort', 'escort')
						->orderBy('escorts.created_at', 'desc')
						->where('escorts.i_receive_city_id', $profileData->i_receive_city_id)
						->where('escorts.id', '!=', $profileData->id)
						->select('escorts.*', 'categories.slug as category_name')
						->limit(20)
						->orderBy('escorts.id','desc')
						->get();

						$preEscorts =  Escort::where('escorts.deleted_at', NULL)->where('escorts.active', 1)->orderBy('escorts.id','desc')->where('escorts.id','<', $profileData->id)->first();

						$nextEscorts =  Escort::where('escorts.deleted_at', NULL)->where('escorts.active', 1)->orderBy('escorts.id','asc')->where('escorts.id', '>', $profileData->id)->first();

						$languages = DB::table('languagables')->where('languagable_id',$profileData->id)->get();
						if(!empty($languages)){
							foreach($languages as $keyl=>$valuel){
							  $lngname 	= DB::table('languages')->where('id', $valuel->language_id)->value('name');	
							  $language[] =  ucfirst($lngname);
							}
						}


					}
					return View::make('frontend.users.ad_details', compact('profileData', 'nextEscorts' ,'preEscorts' ,'escorts', 'subSericeArray', 'escortServiefor','language'));
				}elseif($dataArr_v["url_type"] == "postcode"){
					
					$checklatlong =  DB::table('geo_location_codes')->select('location_lat', 'location_lon')->where('postal_code', $dataArr_v["url_value"])->first();
					if(!empty($checklatlong)){
						$userLatlong	=	[$checklatlong->location_lat, $checklatlong->location_lon];
					}
					$escorts = $escorts->where("escorts.i_receive_post_code",$dataArr_v["url_value"]); 
				}
				elseif($dataArr_v["url_type"] == "city"){
					$checklatlong =  DB::table('cities')->select('latitude', 'longitude')->where('id', $dataArr_v["url_id"])->first();
					if(!empty($checklatlong)){
						$userLatlong	=	[$checklatlong->latitude, $checklatlong->longitude];
					}
					// $escorts = $escorts->where("escorts.i_receive_city_id",explode(',',$dataArr_v["url_id"]));
				}elseif($dataArr_v["url_type"] == "sub_service"){
					$escorts =  $escorts->where('user_tags.tag_id', $dataArr_v["url_id"]);
				}elseif($dataArr_v["url_type"] == "community_name"){
					$checklatlong =  DB::table('geo_location_codes')->select('community_name')->where('community_name', $dataArr_v["url_value"])->first();
					if(!empty($checklatlong)){
						Session::put('check', $check);
						if(!empty($checklatlong)){
							$userLatlong	=	[$checklatlong->local_community_lat, $checklatlong->local_community_lon];
						}
					}
				}else if($dataArr_v["url_type"] == "category"){
					$escorts = $escorts->where("escorts.category_id",$dataArr_v["url_id"]);
				}
				
				if(!empty($userLatlong) &&  !empty(array_filter($userLatlong))){
					
					$escorts = $escorts->where(\DB::raw("((3959 * acos( cos(radians(" . $userLatlong[0] . ") ) * cos(radians( IFNULL(latitude,escorts.latitude))) * cos( radians(IFNULL( longitude,escorts.longitude)  ) - radians(" . $userLatlong[1] . ") ) + sin(radians(" . $userLatlong[0] . ") ) * sin( radians( IFNULL(latitude,escorts.latitude) ) ) )))"),"<=" ,$service_radius);
					 
					if(!empty($escorts)){
						$check = 1;
					}else{
						$check = 0;
					}
					Session::put('check', $check);
					$escorts->orderBy(\DB::raw("((3959 * acos( cos(radians(" . $userLatlong[0] . ") ) * cos(radians( IFNULL(latitude,escorts.latitude))) * cos( radians(IFNULL( longitude,escorts.longitude)  ) - radians(" . $userLatlong[1] . ") ) + sin(radians(" . $userLatlong[0] . ") ) * sin( radians( IFNULL(latitude,escorts.latitude) ) ) )))"),'asc');
				}else{
					
					$escorts->orderBy('escorts.name','asc');
				}
			}
		}
		$totalexport = $escorts->where('escorts.deleted', 0)->where('escorts.active', 1)->distinct()->count();
		\Cookie::make('total_escorts', $totalexport, 120);
		Session::put('total_escorts', $totalexport); 
		DB::enableQueryLog();
		$escorts->leftjoin('categories', 'categories.id','=','escorts.category_id');
		$escortsData = $escorts->select('escorts.*','categories.slug as category_name')->where('escorts.deleted', 0)->distinct()->paginate($pagelimit);

		//echo "<pre>";print_r($escortsData);die;
		
		$escortsFeatured = $escorts->with('receivedcity')->select('escorts.id','escorts.name','figure_id','escorts.is_featured','categories.slug as category_name')->where('escorts.deleted', 0)->where('escorts.active', 1)->where('escorts.is_featured', 'Yes')->select('escorts.*')->distinct()->limit(10)->paginate($pagelimit);

		// echo "</pre>";
		if(!empty($escorts)){
			$check = 1;
			 \Cookie::make('locationSet', '1', 1);
		}else{
			$check = 0;
		}
		
		//$excortArray = $escortsData; 
		Session::put('check', $check);
		$urlName = "Search";
		if($request->type=='ajax'){
			//return json_encode([compact('escortsData', 'escortsFeatured', 'check')]);
			$escortData = View::make('frontend.users.search_details_ajax', compact('escortsData', 'escortsFeatured', 'check'))->render();
			return array('escortData' => $escortData, 'totalexport' => $totalexport); 
		}elseif($request->type=='json'){
			 //echo $request->type;
			if(!empty($escortsData)){
				$excortArray = array();
				$excorfeaturedtArray = array();
				$lang = Config::get('app.fallback_locale');
				if (Session::has('locale')){
				    $lang        =       Session::get('locale');
				}

				foreach($escortsData as $ekey=>$evalue){
					$singleEscortArray = array();
					$image = WEBSITE_IMG_URL.'no-female.jpg';

                     if(!empty($evalue->escortimages[0]))
                     {
                       $image = 'https://cdn.intim.de/'.$evalue->escortimages[0]->path;
                     }elseif(empty($evalue->escortimages[0]) && strtolower($evalue->gender) == 'male'){
                      $image = WEBSITE_IMG_URL.'escort-avatar.jpg';  
                     }else{
                       $image = WEBSITE_IMG_URL.'no-female.jpg'; 
                     }

					$singleEscortArray['id'] 	= $evalue['id'];
					$singleEscortArray['name'] 	= !empty($evalue->name) ? $evalue->name :'';
					$singleEscortArray['city'] 	= !empty($evalue->receivedcity->name) ? $evalue->receivedcity->name :'';
					$singleEscortArray['image'] = $image;
					$singleEscortArray['slug'] 	= $lang.'/'.Str::slug($evalue->name).'-'.$evalue['id'];
					$excortArray[] =  $singleEscortArray; 
				}

				foreach($escortsFeatured as $ekey=>$evalue){
					$singleEscortfeaturesArray = array();
							$image = WEBSITE_IMG_URL.'no-female.jpg';

               if(!empty($evalue->escortimages[0]))
               {
                 $image = 'https://cdn.intim.de/'.$evalue->escortimages[0]->path;
               }elseif(empty($evalue->escortimages[0]) && strtolower($evalue->gender) == 'male'){
                $image = WEBSITE_IMG_URL.'escort-avatar.jpg';  
               }else{
                 $image = WEBSITE_IMG_URL.'no-female.jpg'; 
               }

					$singleEscortfeaturesArray['id'] 	= $evalue['id'];
					$singleEscortfeaturesArray['name'] 	= !empty($evalue->name) ? $evalue->name :'';
					$singleEscortfeaturesArray['city'] 	= !empty($evalue->receivedcity->name) ? $evalue->receivedcity->name :'';
					$singleEscortfeaturesArray['image'] = $image;
					$singleEscortfeaturesArray['slug'] 	= $lang.'/'.Str::slug($evalue->name).'-'.$evalue['id'];
					$excorfeaturedtArray[] 				= $singleEscortfeaturesArray; 
				}



				return response()->json(['status'=>1,'message'=>'Success','escortsData'=>$excortArray, 'exportfeatured' => $excorfeaturedtArray]);
			}else{
				return response()->json(['status'=>0,'message'=>'Failed','data'=>array()]);
			}
		}else{
			return View::make('frontend.users.search_details', compact('escortsData', 'escortsFeatured', 'check'));
		}
    }

    public function getAdDetails($id)
    {	
     // echo $id = $request->escort_id;die;	
      $profileData = Escort::where("escorts.id",$id)
		->with('category')->with('sexuality')->with('figure')->with('escorttype')->with('escortPiercing')
		->where('type_of_escort', 'escort')
		->orderBy('escorts.created_at', 'desc')
		->select('escorts.*')->first();
			$subSericeArray = array();
			$escortServiefor = '';
			$language = array();
			$serviceArray = '';
			$lang = Config::get('app.fallback_locale');
				if (Session::has('locale')){
				    $lang        =       Session::get('locale');
				}
			if(!empty($profileData)){
				
				 $tagsArray = array();

				 
				$user_tags = DB::table('user_tags')->where('taggable_id', $profileData->id)->get();
				if(!empty($user_tags)){
					foreach($user_tags as $ks=>$vs){
						$tagsArray[] = $vs->tag_id;
					}

					if(!empty($tagsArray)){
						$serviceArray = DB::table('service_tags')->leftjoin('services', 'services.id', 'service_tags.service_id')->whereIn('tag_id', $tagsArray)->select('services.name', 'service_tags.*')->get();
					   
					}
				}

				$escortServiefor = DB::table('escort_escort_service_for')->leftjoin('escort_services_for', 'escort_services_for.id', '=', 'escort_escort_service_for.escort_service_for_id')->where('escort_escort_service_for.escort_id',$profileData->id)->pluck('escort_services_for.name')->toArray();

				//echo "<pre>";print_r($subSericeArray);die;
				//echo  $profileData->current_city_id;
				$escorts =  Escort::with('escortimages')
				->with('receivedcity')
				->where('escorts.deleted_at', NULL)
				->where('escorts.active', 1)
				->where('escorts.type_of_escort', 'escort')
				->orderBy('escorts.created_at', 'desc')
				->where('escorts.current_city_id', $profileData->current_city_id)
				->where('escorts.id', '!=', $profileData->id)
				->get();


				$preEscorts =  Escort::where('escorts.deleted_at', NULL)->where('escorts.active', 1)->orderBy('escorts.id','desc')->where('escorts.id','<', $profileData->id)->first();

				$nextEscorts =  Escort::where('escorts.deleted_at', NULL)->where('escorts.active', 1)->orderBy('escorts.id','asc')->where('escorts.id', '>', $profileData->id)->first();
				$languages = DB::table('languagables')->where('languagable_id',$profileData->id)->get();
				if(!empty($languages)){
					foreach($languages as $keyl=>$valuel){
					  $lngname 	= DB::table('languages')->where('id', $valuel->language_id)->value('name');	
					  $language[] =  ucfirst($lngname);
					}
				}
			}

			$escortArray  = array();
			if(!empty($profileData)){
				$escortArray['id'] = $profileData->id;
				$escortArray['name'] = $profileData->name;
				$escortArray['category'] =  $profileData->category->name;
				$escortArray['mobile_public'] = $profileData->mobile_public;
				$escortArray['whatsapp'] = $profileData->whatsapp;
				$escortArray['age'] = isset($profileData->age)  ? $profileData->age:0;
				$escortArray['height'] = isset($profileData->height) ? $profileData->height.' cm':'';
				$escortArray['weight'] = isset($profileData->weight) ? $profileData->weight.' kg':'';
				$figure = '';
				 if(!empty($profileData->figure->name) && $profileData->figure_id > 0){
				 	$figure = $profileData->figure->name;
				 }
				$escortArray['figure'] = $figure;
				$escortArray['gender'] = isset($profileData->gender) ? $profileData->gender:'';
				$sexuality = '';
				if(!empty($profileData->sexuality->name) && $profileData->sexuality_id > 0){
					$sexuality = isset($profileData->sexuality->name) ? $profileData->sexuality->name:'';
				}

				$escortArray['sexuality'] = $sexuality;
				$escortArray['orientation'] = isset($profileData->sexuality->name) ? $profileData->sexuality->name:'';
				 $escotyType = '';
				 if(!empty($profileData->escorttype->name) && $profileData->type_id > 0){
				 	$escotyType = isset($profileData->sexuality->name) ? $profileData->escorttype->name :'';
				 }
				$escortArray['type'] = $escotyType;
				$escortArray['bust_size'] = isset($profileData->bust_size) ? $profileData->bust_size :'';
				$piercing = '';
				if(!empty($profileData->escortPiercing->name) && $profileData->piercing_id > 0){
					$piercing = $profileData->escortPiercing->name;
				}
				$escortArray['piercing'] 				= $piercing;
				$escortArray['shaved'] 					= '';
				$escortArray['languages'] 			= !empty($language) ? implode(',',$language) :'';
				$escortArray['service_for'] 		=	 !empty($escortServiefor) ? implode(',',$escortServiefor) :'';
				$escortArray['about_me'] 				= !empty($profileData->about_me) ? $this->strip_tags_content(trim($profileData->about_me)):'';
				$escortArray['post_code'] 			= isset($profileData->i_receive_post_code) ? $profileData->i_receive_post_code:'';
				$escortArray['city'] 						= isset($profileData->receivedcity->name) ? $profileData->receivedcity->name:'';
				$escortArray['distance'] 				= isset($profileData->distance) && $profileData->distance > 0  ? $profileData->distance.' KM':'0 KM';
				$escortArray['current_address'] = isset($profileData->current_address) ? $profileData->current_address:'';
				$escortArray['i_do_for'] 				= isset($profileData->i_do_for) ? $profileData->i_do_for:'';
				$escortArray['images'] 					= array();
				$escortArray['services'] 				= array();
				$escortArray['related_escorts'] =  array();
				$escort_images = Image::where('imageable_id', $profileData->id)->get();
				if(!empty($escort_images)){
					$singleImageArray = array();
					foreach($escort_images as $image){
						$newimage = WEBSITE_IMG_URL.'no-female.jpg';
	                     if(!empty($image->path))
	                     {
	                       $newimage = 'https://cdn.intim.de/'.$image->path;
	                     }elseif(empty($image->path) && strtolower($image->gender) == 'male'){
	                      $newimage = WEBSITE_IMG_URL.'escort-avatar.jpg';  
	                     }else{
	                       $newimage = WEBSITE_IMG_URL.'no-female.jpg'; 
	                     }
	                     $singleImageArray['path'] = $newimage;
						 					 $escortArray['images'][] = $singleImageArray['path']; 
					}
				}
				//echo "<pre>";print_r($serviceArray );die;
				$servbigArray = array();
				if (!empty($serviceArray)) {
					$matchName = '';
					foreach($serviceArray as $keyser=>$serval){
						$singlserArray = array();
						if($matchName != $serval->name){
							$singlserArray['service_name'] = !empty($serval->name) ? $serval->name:'';
							
						}
						$singlserArray['tags'] = array();
						$tags                           =  DB::table('tags')->where('id', $serval->tag_id)->get();

						if(!empty($tags)){
							foreach ($tags as $tagsktagsey => $tagsvalue) {
								$singleTag = array();
								$singleTag['id'] 		= $tagsvalue->id;
								$singleTag['name'] 		= $tagsvalue->name;
								$singlserArray['tags'][] 	= $singleTag; 
							}
						}

						if(!empty($singlserArray)){

							$escortArray['services'][] = $singlserArray;
						}
						$matchName = $serval->name;
						
					}	
				}

				if(!empty($escorts)){
					foreach($escorts as $keyescort=>$escortvalue){
						$singleEscortArray = array();

						    $image = WEBSITE_IMG_URL.'no-female.jpg';

               if(!empty($escortvalue->escortimages[0]))
               {
                 $image = 'https://cdn.intim.de/'.$escortvalue->escortimages[0]->path;
               }elseif(empty($escortvalue->escortimages[0]) && strtolower($escortvalue->gender) == 'male'){
                $image = WEBSITE_IMG_URL.'escort-avatar.jpg';  
               }else{
                 $image = WEBSITE_IMG_URL.'no-female.jpg'; 
               }
						$singleEscortArray['id'] 		= $escortvalue->id;
						$singleEscortArray['name'] 	= $escortvalue->name;
						$singleEscortArray['city'] 	= !empty($escortvalue->receivedcity->name) ? $escortvalue->receivedcity->name :'';
						$singleEscortArray['image'] = $image;
						$singleEscortArray['slug'] 	= $lang.'/'. $escortvalue->slug;
						$escortArray['related_escorts'][] = $singleEscortArray;
					}
				}


				
				return response()->json(['status'=>1,'message'=>'Success','escortsData'=>$escortArray]);
				/*$escortArray['name'] = $profileData->id;
				
				$escortArray['name'] = $profileData->id;*/
			}else{
				return response()->json(['status'=>0,'message'=>'Failed','escortsData'=>array()]);
			}
    }

		 function strip_tags_content($string) { 
		    // ----- remove HTML TAGs ----- 
		    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
		    // ----- remove control characters ----- 
		    $string = str_replace("\r", '', $string);
		    $string = str_replace("\n", ' ', $string);
		    $string = str_replace("\t", ' ', $string);
		    // ----- remove multiple spaces ----- 
		    $string = trim(preg_replace('/ {2,}/', ' ', $string));
		    return $string; 

		}
}
