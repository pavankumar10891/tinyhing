<?php
use App\Models\EscortFigure;
use App\Models\EscortPiercing; 
use App\Models\Setting;
use App\Models\EscortService;
use App\Models\EscortSexuality;
use App\Models\EscortSubService;
use App\Models\EscortType;
use App\Models\EscortHairColor;
use App\Models\Services;
use App\Models\Category;
use App\Models\City;	
use App\Models\User;
use App\Models\Escort;
use App\Models\Link;
// use App\Http\Controllers\BaseController; 

class CustomHelper {
	public static function  addhttp($url = "") {
		if($url == ""){
			return "";
		}
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}

	public static function priceformat($price) 
	{
		return '$'.number_format($price,2);
	}

	public static function getseachtabs(){
		$regions 	= DB::table('geo_location_codes')->select('region_name','local_community_lat','local_community_lon')->groupBY('region_name','local_community_lat','local_community_lon')->get(); 
		$cities 	= City::where('is_favourite',1)->where('deleted_at',Null)->orderBy('name', 'asc')->limit(20)->get();
		$figures 	= EscortFigure::where('deleted_at', Null)->get();
		$types 		= EscortType::where('deleted_at', Null)->get();
		$haicolors 	= EscortHairColor::where('deleted_at', Null)->get();
		$services 	= Services::where('deleted_at', Null)->where('status', 1)->get();
		$bustsize 	= Escort::select('bust_size')->where('deleted_at', Null)->where('deleted',0)->where('bust_size', '!=', '')->where('bust_size', '!=', null)->groupBy('bust_size')->get();
		$serviceArray = array();
		if(!empty($services)){
			foreach ($services as $key => $value) {
			  $singleSerive = array();
			  $singleSerive['id'] = $value->id;
			  $singleSerive['name'] = $value->name;
			  $singleSerive['tags'] = DB::table('tags')->leftJoin('service_tags', 'service_tags.tag_id', '=', 'tags.id')->where('service_tags.service_id', $value->id)->select('tags.name as tag_name', 'tags.id as tag_id')->where('deleted_at', Null)->get();
			  $serviceArray[] = $singleSerive; 


			}
		}
		$services = $serviceArray;
		$categories = Category::where('deleted_at', Null)->get()->toArray();
		$bustsizesData 	= DB::table('bustsizes')->select('*')->get();
		return array('figures' => $figures, 'types' => $types, 'haicolors' =>$haicolors, 'services' =>$services, 'categories' =>$categories,'bustsize' => $bustsize, 'cities' => $cities, 'regions' => $regions, 'bustsizesData' => $bustsizesData);

	}
	public static function getMenuList($type="header"){
		$data = Category::get();
		// $base = new BaseController;
		// foreach($data as &$row){
		// 	$slug = $base->getSlug($row->name,'name','Category');
		// 	$row->slug		=	$slug;
		// 	$row->save();
		// }die;
		$records	=	Link::select('links.id','links.name','categories.slug')
							->leftjoin('categories','links.category_id','categories.id')
							->where('links.type',$type)
							->where('links.parent_id',0)
							->get();
							// echo "<pre>"; print_r($records); die;
		$menu	=	[];
		if(!$records->isEmpty()){
			foreach($records as $key => $record){
				$menu[$key]['name'] 	=	$record->name;
				$menu[$key]['slug'] 	=	$record->slug;
				$c_records		=	Link::select('links.id','links.name','categories.slug')
									->leftjoin('categories','links.category_id','categories.id')
									->where('links.type',$type)
									->whereRaw('FIND_IN_SET('.$record->id.',links.parent_id)')
									->get();
				foreach($c_records as $c_key => $c_record){
					$menu[$key]['child'][$c_key]['name'] 	=	$c_record->name;
					$menu[$key]['child'][$c_key]['slug'] 	=	$c_record->slug;
				}
			}
		}
		return $menu;
		// echo "<pre>"; print_r($menu); die;
	}

}
