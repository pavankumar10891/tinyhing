<?php
namespace App\Model; 

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Eloquent,DB,App,Auth;

class Blog extends Eloquent  {
	
	
	protected $table = 'blogs';

    /**
     * The database table used by the model.
     *
     * @var string
     */

	public function blogData($blogId	=	0){
		$lang			=	App::getLocale();			
		$blogDetail	=	Blog::where('blogs.id', '=',  $blogId)
						//->Leftjoin('dropdown_managers' , 'dropdown_managers.id' , '=' , 'blogs.category_id')
						->select('blogs.*',DB::raw("(select slug from dropdown_managers where id=blogs.category_id) as category_slug"),DB::raw("(select name from dropdown_manager_descriptions where parent_id=blogs.category_id and language_id = (select id from languages WHERE languages.lang_code = '$lang')) as category_name"))
						->first();
		
		return $blogDetail;
	} 
	
	public function get_view_blog_details_by_id($blogId	=	0){
		$blogDetails	=	Blog::where("blogs.id",$blogId)->Leftjoin('dropdown_managers' , 'dropdown_managers.id' ,'=' ,'blogs.category_id')->select('blogs.*','dropdown_managers.name as category_name')->first();
		return $blogDetails;
	}
	
	/**
	 * Function for get slug
	 */
	public function get_blog_slug($blogId	=	0){
		
		$result	=	Blog::where('id',$blogId)->select('slug')->first();
		$slug	=	$result['slug'];
		return $slug;
	}
	
	/**
	 * Function for get id
	 */
	public function get_blog_id($slug	=	''){
		$result	=	Blog::where('slug',$slug)->select('id')->first();
		$id	=	$result['id'];
		return $id;
	}
	
	/**
	 * Function for get blog record
	 */
	public function get_blog_data($keywordData = null){
		$user_id	=	(!empty(Auth::user()->id)) ? Auth::user()->id : 0;
		$DB			=	Blog::query();
							$DB->where('is_active',1);
							$DB->where('is_deleted',0);
							if(!empty($keywordData)){
								if(!empty($keywordData["month"]) && !empty($keywordData["year"])){
									$DB->where('month', '=', $keywordData["month"]);
									$DB->where('year', '=', $keywordData["year"]);
								}
								if(!empty($keywordData["tags"])){
									$DB->whereRaw("MATCH(tags) AGAINST(? IN BOOLEAN MODE)", array($keywordData["tags"]));
								}
								if(!empty($keywordData["catId"])){
									$DB->where('category_id', '=', $keywordData["catId"]);
								}
							}
							$DB->select("blogs.*",DB::raw("(select count(*) from blog_notifications where user_id=$user_id and blog_id=blogs.id) as is_read"))->orderBy('created_at','DESC');
		$result		=	$DB->limit(BLOG_PAGE_LIMIT)->get();
		return $result;
	}
	
	public function blog_count($keywordData = null){
		
		$DB			=	Blog::query();
							$DB->where('is_active',1);
							$DB->where('is_deleted',0);
							if(!empty($keywordData)){
								if(!empty($keywordData["month"]) && !empty($keywordData["year"])){
									$DB->where('month', '=', $keywordData["month"]);
									$DB->where('year', '=', $keywordData["year"]);
								}
								if(!empty($keywordData["catId"])){
									$DB->where('category_id', '=', $keywordData["catId"]);
								}
								if(!empty($keywordData["tags"])){
									$DB->whereRaw("MATCH(tags) AGAINST(? IN BOOLEAN MODE)", array($keywordData["tags"]));
								}
							}
		$result		=	$DB->count();
		return $result;
	}
	
	/**
	 * Function for get load more blog record
	 */
	public function get_load_more_blog_data($limit=null,$offset=null,$keywordData = null){
		$user_id	=	(!empty(Auth::user()->id)) ? Auth::user()->id : 0;
		$DB			=	Blog::query();
							$DB->where('is_active',1);
							$DB->where('is_deleted',0);
							if(!empty($keywordData)){
								if(!empty($keywordData["month"]) && !empty($keywordData["year"])){
									$DB->where('month', '=', $keywordData["month"]);
									$DB->where('year', '=', $keywordData["year"]);
								}
								if(!empty($keywordData["catId"])){
									$DB->where('category_id', '=', $keywordData["catId"]);
								}
								if(!empty($keywordData["tags"])){
									$DB->whereRaw("MATCH(tags) AGAINST(? IN BOOLEAN MODE)", array($keywordData["tags"]));
								}
							}
							$DB->select("blogs.*",DB::raw("(select count(*) from blog_notifications where user_id=$user_id and blog_id=blogs.id) as is_read"))->orderBy('created_at','DESC');
							$DB->limit($limit);
							$DB->offset($offset);
		$result		=	$DB->get();
		return $result;
	}
	
	/**
	 * Function for get popular blog record
	 */
	public function get_recent_popular_blog(){
		$result	=	Blog::where('is_popular',1)
					->where('is_deleted',0)
					->where('is_active',1)
					->limit(3)
					->orderby('created_at','desc')
					->get();
		return $result;
	}
	
	/**
	 * Function for get latest blog record
	 */
	public function get_latest_blog(){
		$result	=	Blog::where('is_active',1)
					->where('is_deleted',0)
					->limit(3)
					->orderby('created_at','desc')
					->get();
		return $result;
	}
	
	/**
	 * Function for get blog category name
	 */
	public function get_blog_category_name(){
		$result	=	Blog::where('blogs.is_active',1)
					->where('blogs.is_deleted',0)
					->Leftjoin('dropdown_managers', 'category_id', '=', 'dropdown_managers.id')
					->select('dropdown_managers.name','dropdown_managers.id')
					->get();
		return $result;
	}
	
	/**
	 * Function for get blog archives
	 */
	public function get_blog_archive(){
		$result	=	Blog::where('blogs.is_active',1)
					->where('blogs.is_deleted',0)
					->select('blogs.month','blogs.year')
					->groupBy('year')
					->get()->toArray();
		
		//pr($result);
		$arr	=	array();
		if(!empty($result)){
			foreach($result as $key=>$data){
				$arr[$key]		=	$data;
				$month		=	$data['month'];//array(01,02,03,04,05,06,07,8,9,10,11,12); //$data['month'];
				$year			=	$data['year'];
				/* foreach($month as $key=>$record ){
					//pr($key);
					pr($record);
					$arr[$key]["data"]	=	Blog::where('blogs.is_active',1)
										->where('blogs.is_deleted',0)
										->where('blogs.year',$data['year'])
										->where('blogs.month','0'.$record)
										->selectRaw(" blogs.month , blogs.year ,(SELECT count(id) From blogs WHERE month = 0$record AND year = $year) as blog_count")
										->groupBy('month')
										->get()->toArray();
				}
				pr($arr);
				die; */
				$arr[$key]["data"]	=	Blog::where('blogs.is_active',1)
										->where('blogs.is_deleted',0)
										->where('blogs.year',$data['year'])
										->selectRaw(" blogs.month , blogs.year ,(SELECT count(id) From blogs WHERE month = $month AND year = $year) as blog_count")
										->groupBy('month')
										->get()->toArray();
			}
		}
		//pr($arr);die;
		
		return $arr;
	}
	
	/**
	 * Function for get popular blog detail
	 */
	public function get_popular_blog_detail($slug){
		$result	=	Blog::where('is_active',1)
					->where('slug',$slug)
					->where('is_deleted',0)
					->where('is_popular',1)
					->first();
		
		return $result;
	}
	
	/**
	 * Function for get  blog detail
	 */
	public function get_blog_detail($slug){
		$result	=	Blog::where('is_active',1)
					->where('slug',$slug)
					->where('is_deleted',0)
					->first();
		return $result;
	}
	/**
	 * Function for get  blog archive detail
	 */
	public function get_blog_archive_data($month	=	'',$year	=	''){
		//$year	=	date("Y",strtotime($date));
		//$month	=	date("m",strtotime($date));
		/* $result = Blog::where('is_active',1)
				->where('is_deleted',0)
				->whereYear('created_at', '=', $year)
				->whereMonth('created_at', '=', $month)
				->get(); */
		
		$result = Blog::where('is_active',1)
				->where('is_deleted',0)
				->where('year', '=', $year)
				->where('month', '=', $month)
				->get();
			  
		return $result;
	}
	/**
	 * Function for get  blog detail acording to category
	 */
	public function get_blog_category_data($category	=	''){
		$result = Blog::where('is_active',1)
				->where('is_deleted',0)
				->where('category_id',$category)
				->get();
		return $result;
	}
	
}
