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
class BlogController extends BaseController {
	
/** 
* Function to redirect website on main page
*
* @param null
* 
* @return
*/
	public function blog(Request $request)
	{
	
		$Item = $request->search;
		$category_id = !empty($request->category_id) ? base64_decode($request->category_id)  : '' ;
	
		if(isset($Item) || isset($category_id) ){
		 $query = Blog::query();
		 $query->where(['is_active' => 1, 'is_deleted' => 0])->where(function ($query) use($Item){
							$query->orwhere("title","LIKE","%".$Item."%");
							$query->orwhere("description","LIKE","%".$Item."%");
						}) ; 
		if($category_id!=''){
			$query->where('category_id' , $category_id );
		}				
		$blogs = $query->orderBy('id', 'desc')->limit(6)->get();
		
		}else{
			$blogs = Blog::where('is_active', 1)->where('is_deleted', 0)->orderBy('created_at', 'desc')->limit(6)->get();
			
		}
	
	 	return View::make('front.blog.blog', compact('blogs'));
	}


	public function blogDetail($slug)
	{
		$blog = Blog::where('is_active', 1)->where('slug', $slug)->where('is_deleted', 0)->first();
		if(!empty($blog)){
			return View::make('front.blog.blog_details', compact('blog'));
		}else{
			Session::flash('error',trans("something went to wrong"));
			return redirect()->back();
		}

	}

	public function loadmoreBlog(Request $request)
	{
		$search      = $request->search ; 
		$category_id = $request->category_id ; 
		$offset = $request->offset + 6;
		$offsetdb = $request->offset;
		$DB		=	Blog::query();
		$DB->where(['is_active' => 1,  'is_deleted' => 0 ]);
		if($search !=''){
			$DB->where(function ($query) use($search){
				$query->orwhere("title","LIKE","%".$search."%");
				$query->orwhere("description","LIKE","%".$search."%");
			}) ; 

		}
		if($category_id !='') {
			$DB->where('category_id' , $category_id );
		}

		$blogs = $DB->offset($offsetdb)->limit(6)->orderBy('created_at', 'desc')->limit(6)->get();
		$list_count   = count($blogs) ;
		 $output = '';
		if(count($blogs) > 0){
			
			foreach($blogs as $blogk=>$blogv){
				$image = !empty($blogv->banner_image) ? BLOG_IMAGE_URL.$blogv->banner_image:WEBSITE_IMG_URL.'listing-img.jpg';
				$output.='<div class="col-12">
                                <div class=" mr-md-auto">
                                    <div class="row align-items-center no-gutters bg-light-white">
                                        <div class="col-md-4">
                                     <div class="img-wall no-round" style="background-image: url('.$image.')">
                                        <img src="'.$image.'" class="w-100" alt="">
                                    </div>
                                </div>
                                <div class="col-md-8 pl-4">
                                    <div class="text-block">
                                            <h3>'.$blogv->title.'</h3>
                                       
                                        <div class="">
                                            <p>'.$blogv->description.'</p>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-block mt-1 ">
                                        <a href="'.route('user.blogdetail', $blogv->slug).'" 
                                            class="btn-theme ">
                                            Read More
                                        </a>
                                    </div>
                                </div>  </div>    </div>
                            </div>';
				
							}
						}
		return response()->json(['success' => true, 'data' => $output , 'offset'=>$offset, 'list_count'=>$list_count]);
	}


	
	
}// end BlogController class
