<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Blog;
use App\Model\DropDown;
use App\Model\BlogComment;
use App\Model\Tag;
use App\Model\Lookup;
use App\Model\Blogtag;
use Illuminate\Http\Request;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

/**
* Blog Controller
*
* Add your methods in the class below
*
* This file will render views from views/admin/blog
*/
 
class BlogController extends BaseController {
/**
* Function for display list of all blogs
*
* @param null
*
* @return view page. 
*/
	public $model		=	'Blog';
	public $sectionName	=	'Blog';
	public $sectionNameSingular	=	'Blog';
	
	public function __construct(Request $request) {
		parent::__construct();
		View::share('modelName',$this->model);
		View::share('sectionName',$this->sectionName);
		View::share('sectionNameSingular',$this->sectionNameSingular);
		$this->request = $request;
	}
	public function listBlog(Request $request){
		$DB 					= 	Blog::query();
		$searchVariable			=	array(); 
		$inputGet				=	$request->all();
		/* seacrching on the basis of username and email */
		if ($request->all()) {
			$searchData			=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('blogs.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]); 											
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('blogs.created_at','>=' ,[$dateS." 00:00:00"]); 
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('blogs.created_at','<=' ,[$dateE." 00:00:00"]); 						
			}
			foreach($searchData as $fieldName => $fieldValue){
				  if($fieldName == "title"){
						$DB->where("blogs.title",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "category_id"){
						$DB->where("blogs.category_id",'like','%'.$fieldValue.'%');
					}
				if($fieldName == "posted_by"){
					$DB->where("blogs.posted_by",'like','%'.$fieldValue.'%');
				}
				
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	($request->get('sortBy')) ? $request->get('sortBy') : 'updated_at';
		$order  				= 	($request->get('order')) ? $request->get('order')   : 'DESC';
		$results 				= 	$DB->where('is_deleted',0)
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										//pr($result);die;
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($request->all())->render();

		//programs
		return  View::make('admin.blog.index', compact('results' ,'searchVariable','sortBy','order','query_string'));
	}// end listBlog()
/**
* Function for add blog
*
* @param null
*
* @return view page. 


*/	
	public function addBlog(){
		$blogCategory = Lookup::where('lookup_type',"blogcategory")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		return  View::make('admin.blog.add', compact('blogCategory'));
	}//end addBlog()
/**
* Function for save added blog
*
* @param null
*
* @return view page. 
*/	
	public function saveBlog(Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$formData						=	$request->all();
		$bannerImage					=	!empty($request->input('banner_image'))?$request->input('banner_image'):'';
			if(!empty($formData)){
				/*if($request->input('blog_type') == 'image'){
					$validator 						= 	Validator::make(
						$request->all(),
						array(
							'title'						=> 'required',
							//'category_id'				=> 'required',
							'posted_by'					=> 'required',
							'description'				=> 'required',
							//'featured'					=> 'required',
							'banner_image'				=> 'required|mimes:'.IMAGE_EXTENSION,
					),array(
						'title.required'					=> 'The title field is required',
						'description.required'				=>'The article field is required',

						//'category_id.required'				=>'The blog category field is required',
						//'featured.required'					=>'The blog featured field is required',
					)
					);
				}else{
					$regex  = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
					$validator 						= 	Validator::make(
						$request->all(),
						array(
								'title'						=> 'required',
								//'category_id'				=> 'required',
								'posted_by'					=> 'required',
								'description'				=> 'required',
								//'featured'					=> 'required',

								//'embedded_url' 				=>	'required|regex:'.$regex,
						),array(
							'title.required'					=> 'The title field is required',
							'description.required'				=>'The article field is required',
							//'featured.required'					=>'The blog featured field is required',
							//'embedded_url.regex'				=>'Please enter valid url. ',
							//'embedded_url.required'				=>'The video embedded url field is required. ',
							//'category_id.required'				=>'The blog category field is required',
						)
					);
				}*/
					$regex  = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
					$validator 						= 	Validator::make(
						$request->all(),
						array(
								'title'						=> 'required',
								'category_id'				=> 'required',
								'posted_by'					=> 'required',
								'description'				=> 'required',
								//'featured'					=> 'required',

								//'embedded_url' 				=>	'required|regex:'.$regex,
						),array(
							'title.required'					=> 'The title field is required',
							'description.required'				=>'The article field is required',
							'category_id.required'				=>'The blog category field is required',
							//'featured.required'					=>'The blog featured field is required',
							//'embedded_url.regex'				=>'Please enter valid url. ',
							//'embedded_url.required'				=>'The video embedded url field is required. ',
						)
					);
		
			if ($validator->fails()){
				 return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$obj 							= 	new Blog;
				$obj->title						= 	$request->input('title');
				$obj->category_id				= 	$request->input('category_id');
				$obj->posted_by					= 	$request->input('posted_by');
				$obj->description				= 	$request->input('description');
				//$obj->tags						= 	rtrim(strip_tags($this->arrayRemoveStrings($request->input('tags'))),",");
				$obj->is_active					=  	1;
				$obj->is_popular				=  	0;
				//$obj->featured					=   $request->input('featured');
				//$keyword						=	strip_tags($this->arrayRemoveStrings($request->input('description')));
				//$obj->keyword					=	$request->input('description');
				$obj->slug						=  	$this->getSlug($obj->title,'title',"Blog");
				$obj->month						=  	date('m');
				$obj->year						=  	date('Y');
				//$obj->blog_type					=	$request->input('blog_type');
				//$obj->metakey					=	strip_tags($request->input('metakey'));
				//$obj->metadescription			=  	strip_tags($request->input('metadescription'));
				//$obj->banner_video    			=	!empty($request->input('embedded_url')) ? $request->input('embedded_url') : '';
				/* if(input::hasFile('banner_video')){
					//$extension 					=	 Input::file('banner_video')->getClientOriginalExtension();
					$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;
					$folderPath					=	BLOG_IMAGE_ROOT_PATH.$newFolder; 
					//$fileName					=	time().'-banner-video.'.$extension;
					if(!File::exists($folderPath)){
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					/* if(Input::file('banner_video')->move($folderPath, $fileName)){
						//$obj->banner_video			=	$newFolder.$fileName;
					} 
					$extension 			=	Input::file('banner_video')->getClientOriginalExtension();
					$fileName			=	time().'-banner-video.'.$extension;
					$mp4_file 			= 	time().'-banner-video.mp4';
					$webm_file 			= 	time().'-banner-video.webm';
					$wmv_file 			= 	time().'-banner-video.wmv';
					$jpg_file 			= 	time().'-banner-video.jpg';
					if(Input::file('banner_video')->move($folderPath, $fileName)){
						$this->convertToMp4($folderPath . $fileName,$folderPath . $mp4_file ,1079, 559);
						$this->convertToWebm($folderPath . $fileName,$folderPath . $webm_file ,1079, 559);
						$this->generateThumbnail($folderPath .DS. $mp4_file,$folderPath .DS. $jpg_file ,1079, 559);
						$obj->banner_video    =  $newFolder.$fileName;
					}
					if(!input::hasFile('banner_image')){
						$obj->banner_image	=	$newFolder.$jpg_file;
					}
				} */
				if(!empty($request->input('banner_image')) && $request->input('blog_type') == 'image'){
					if($request->hasFile('banner_image')){
						$extension 				=	 $request->file('banner_image')->getClientOriginalExtension();
						$newFolder     			= 	strtoupper(date('M'). date('Y')).DS;
						$folderPath				=	BLOG_IMAGE_ROOT_PATH.$newFolder; 
						$fileName				=	time().'-banner-image.'.$extension;
						if(!File::exists($folderPath)){
							File::makeDirectory($folderPath, $mode = 0777,true);
						}
						if($request->file('banner_image')->move($folderPath, $fileName)){
							$obj->banner_image			=	$newFolder.$fileName;
						}
					}
				}
				$obj->save();
				$blog_id							=	$obj->id;			
				Session::flash('success',trans("Blog added successfully"));
				return Redirect::to('adminpnlx/blog');
			}
		}
	}// end saveBlog()
/**
* Function for display blog detail
*
* @param $blogId as id of blog
*
* @return view page. 
*/
	public function viewBlog($blogId = 0){
		$blogDetails			=	Blog::find($blogId);
		//$blogDetails	=	$blog->get_view_blog_details_by_id($blogId);
		if(empty($blogDetails)) {
			Session::flash("error","Wrong URL!");
			return Redirect::to('adminpnlx/blog');
		}
		return View::make('admin.blog.view', compact('blogDetails'));
	} // end viewBlog()
/**
* Function for display page for edit blog
*
* @param $blogId as id of blog
*
* @return view page. 
*/
	public function editBlog($blogId = 0){
		$model			=	Blog::find($blogId);
		$blogCategory = Lookup::where('lookup_type',"blogcategory")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		if(empty($model)) {
			return Redirect::to('adminpnlx/blog');
		}
		if($blogId){
			$model		=	Blog::find($blogId);
			

			return View::make('admin.blog.edit', compact('model', 'blogCategory'));
		}
	} // end editBlog()
/**
* Function for update blog detail
*
* @param $blogId as id of blog
*
* @return redirect page. 
*/
	public function updateBlog($blogId = 0, Request $request){
		$this->request->replace($this->arrayStripTags($request->all()));
		$thisData						=	$request->all();
		if(!empty($thisData)){
			/*if($request->input('blog_type') == 'image'){
				$validator 						= 	Validator::make(
					$request->all(),
					array(
						'title'						=> 'required',
						'category_id'				=> 'required',
						'posted_by'					=> 'required',
						'description'				=> 'required',
						'featured'					=> 'required',
						'banner_image'				=> 'mimes:'.IMAGE_EXTENSION,
				),array(
					'description.required'				=>'The article field is required',
					'category_id.required'				=>'The blog category field is required',
					'featured.required'					=>'The blog featured field is required',
				)
				);
			}else{
				$regex  = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
				$validator 						= 	Validator::make(
					$request->all(),
					array(
							'title'						=> 'required',
							'category_id'				=> 'required',	
							'posted_by'					=> 'required',
							'description'				=> 'required',
							'featured'					=> 'required',
							'embedded_url' 				=>	'required|regex:'.$regex,
					),array(
						'description.required'				=>'The article field is required',
						'embedded_url.regex'				=>'Please enter valid url.',
						'embedded_url.required'				=>'The video embedded url field is required. ',
						'category_id.required'				=>'The blog category field is required',
						'featured.required'					=>'The blog featured field is required',
						
					)
				);
			}*/
				$regex  = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
				$validator 						= 	Validator::make(
					$request->all(),
					array(
							'title'						=> 'required',
							'category_id'				=> 'required',	
							'posted_by'					=> 'required',
							'description'				=> 'required',
							'banner_image'				=> 'mimes:'.IMAGE_EXTENSION,
							//'featured'					=> 'required',

							//'embedded_url' 				=>	'required|regex:'.$regex,
					),array(
						'title.required'					=>'The title field is required',
						'category_id.required'			    =>'The blog category field is required',
						'description.required'				=>'The article field is required',
						//'embedded_url.regex'				=>'Please enter valid url.',
						//'embedded_url.required'				=>'The video embedded url field is required. ',
						//'featured.required'					=>'The blog featured field is required',
						
					)
				);
			if ($validator->fails()){	
				return Redirect::to('/adminpnlx/blog/edit-blog/'.$blogId)
					->withErrors($validator)->withInput();
			}else{
				$obj	 						=   Blog::find($blogId);
				$obj->category_id				= 	$request->input('category_id');
				$obj->title						= 	$request->input('title');
				$obj->posted_by					= 	$request->input('posted_by');
				$obj->description				= 	$request->input('description');
				//$obj->tags						= 	rtrim(strip_tags($this->arrayRemoveStrings($request->input('tags'))),",");
				//$obj->metakey					=	strip_tags($request->input('metakey'));
				//$obj->metadescription			=  	strip_tags($request->input('metadescription'));
				//$keyword						=	strip_tags($this->arrayRemoveStrings($request->input('description')));
				//$obj->keyword					=	$keyword;
				//$obj->featured					=   $request->input('featured');
				//$obj->is_popular				=  	0;
				//$obj->blog_type					=	$request->input('blog_type');
				//$obj->banner_video	   			=	!empty($request->input('embedded_url')) ? $request->input('embedded_url') : '';
			/* 	if(input::hasFile('banner_video')){
					//$extension 					=	 Input::file('banner_video')->getClientOriginalExtension();
					$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;
					$folderPath					=	BLOG_IMAGE_ROOT_PATH.$newFolder; 
					//$fileName					=	time().'-banner-video.'.$extension;
					 if(!File::exists($folderPath)){
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					
					
					$extension 			=	Input::file('banner_video')->getClientOriginalExtension();
					$fileName			=	time().'-banner-video.'.$extension;
					$mp4_file 			= 	time().'-banner-video.mp4';
					$webm_file 			= 	time().'-banner-video.webm';
					$wmv_file 			= 	time().'-banner-video.wmv';
					$jpg_file 			= 	time().'-banner-video.jpg';
					if(Input::file('banner_video')->move($folderPath, $fileName)){
						$this->convertToMp4($folderPath . $fileName,$folderPath . $mp4_file ,1079, 559);
						$this->convertToWebm($folderPath . $fileName,$folderPath . $webm_file ,1079, 559);
						$this->generateThumbnail($folderPath .DS. $mp4_file,$folderPath .DS. $jpg_file ,1079, 559);
						$obj->banner_video    =  $newFolder.$fileName;
					}
					
					if(!input::hasFile('banner_image')){
						$obj->banner_image	=	$newFolder.$jpg_file;
					}
				}  */
				if(!empty($request->input('banner_image')) && $request->input('blog_type') == 'image'){
					if($request->hasFile('banner_image')){
						$extension 					=	 $request->file('banner_image')->getClientOriginalExtension();
						$newFolder     				= 	strtoupper(date('M'). date('Y')).DS;
						$folderPath					=	BLOG_IMAGE_ROOT_PATH.$newFolder; 
						$fileName					=	time().'-banner-image.'.$extension;
						if(!File::exists($folderPath)){
							File::makeDirectory($folderPath, $mode = 0777,true);
						}
						if($request->file('banner_image')->move($folderPath, $fileName)){
							$obj->banner_image		=	$newFolder.$fileName;
						}
					}
				}
				
				$obj->save();
				$service_id					=	$obj->id;		
				if(!$service_id) {
					Session::flash('error', trans("Something went wrong.")); 
					return Redirect::back()->withInput();
				}
				return Redirect::to('adminpnlx/blog')->with('success',trans("Blog updated successfully"));
			}
		}
	}// end updateBlog()
/**
* Function for mark a blog as deleted 
*
* @param $updateBlog as id of blog
*
* @return redirect page. 
*/
	public function deleteBlog($updateBlog = 0){
		$userDetails	=	Blog::find($updateBlog); 
		if(empty($userDetails)) {
			return Redirect::to('adminpnlx/blog');
		}
		if($updateBlog){
        $userModel = Blog::find($updateBlog);
        $userModel->is_deleted = 1;
        $userModel->save();
		//$blogtag	=	DB::table('blog_tags')->where('blog_id',$updateBlog)->update(['is_deleted' => 1]);
			Session::flash('flash_notice',trans("Blog removed successfully")); 
		}
		return Redirect::to('adminpnlx/blog');
	} // end deleteBlog()
/**
* Function for comment
*
* @param $blogId as id of blog
*
* @return redirect page. 
*/
	public function commentBlog($blogId = 0){
		### Get Blog Comment Data ###
		$blogComment	=	new BlogComment;
		$commentDetails	=	$blogComment->blogCommentData($blogId);
		//pr($commentDetails);die;
		### Get Blog Data ###
		$blog			=	new Blog;
		$blogDetail		=	$blog->blogData($blogId);
		if(empty($blogDetail)) {
			return Redirect::to('adminpnlx/blog');
		}
		return View::make('admin.blog.comment', compact('commentDetails','blogDetail','blogId'));
	} // end commentBlog()
/**
* Function for save comment
*
* @param $blogId as id of blog
*
* @return redirect page. 
*/
	public function saveCommentBlog($blogId = 0){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all();
		if(!empty($thisData)){
			$validator 						= 	Validator::make(
				$request->all(),
				array(
					'comment'						=> 'required',
					
					)
				);
			if ($validator->fails()){	
				return Redirect::to('/adminpnlx/blog/comment-blog/'.$blogId)
					->withErrors($validator)->withInput();
			}else{
					$obj	 						=   new BlogComment();
					$obj->blog_id					= 	$blogId;
					$obj->user_id					= 	Auth::user()->id;
					$obj->user_role_id				= 	Auth::user()->user_role_id;
					$obj->name						= 	Auth::user()->full_name;
					$obj->email						= 	Auth::user()->email;
					$obj->parent_comment_id			= 	$request->input('parent_comment_id');
					$obj->comment					= 	$request->input('comment');
					$obj->slug						=  	$this->getSlug($obj->name,'slug',"BlogComment");
					$obj->save();
					return Redirect::to('adminpnlx/blog/comment-blog/'.$blogId)->with('success',trans("Comment added successfully."));
			}
		}
	} // end saveCommentBlog()
/**
 * Function for delete comment
 *
 * @param null
 *
 * @return redirect page. 
 */
	 public function deletetCommentBlog($id	=	0){
		$model = BlogComment::findorFail($id);
		$model->delete();
		Session::flash('flash_notice',trans("Comment deleted successfully.")); 
		return Redirect::back();
	} //end deleteComment
/**
 * Function for delete comment
 *
 * @param null
 *
 * @return redirect page. 
 */
	 public function editCommentBlog($blogId	=	0,$id	=	0){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		if(!empty($thisData)){
			$obj	 						=   BlogComment::find($id);
			$obj->comment					= 	$request->input('comment_edit');
			$obj->save();
			Session::flash('flash_notice',trans("Comment updated successfully.")); 
			return Redirect::back();
		}
	} //end editCommentBlog
/**
 * Function for save reply comment
 *
 * @param $blogId as id of comment
 *
 * @return redirect page.  
 */
	public function saveReplyCommentBlog($blogId = 0){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all();
		if(!empty($thisData)){
			$obj	 						=   new BlogComment();
			$obj->blog_id					= 	$blogId;
			$obj->user_id					= 	Auth::user()->id;
			$obj->user_role_id				= 	Auth::user()->user_role_id;
			$obj->name						= 	Auth::user()->full_name;
			$obj->email						= 	Auth::user()->email;
			$obj->parent_comment_id			= 	$request->input('parent_comment_id');
			$obj->comment					= 	$request->input('comment_reply');
			$obj->slug						=  	$this->getSlug($obj->name,'slug',"BlogComment");
			$obj->save();
		
			return Redirect::to('adminpnlx/blog/comment-blog/'.$blogId)->with('success',trans("Comment added successfully."));
		}
	} // end saveReplyCommentBlog()
/**
 * Function for update blog status
 *
 * @param $blogId as blog id
 *
 * @param $status as status of blog
 *
 * @return redirect page. 
 */
	public function updateBlogStatus($blogId = 0, $status = 0){
		if($status == 0	){
			$statusMessage	=	trans("Blog deactivated successfully");
		}else{
			$statusMessage	=	trans("Blog activated successfully");
		}
		$this->_update_all_status('blogs',$blogId,$status);
		
		Session::flash('flash_notice', $statusMessage);
		return Redirect::to('adminpnlx/blog/');
	} // end updateBlogStatus()
/**
 * Function for update blog status for popular/unpopular
 *
 * @param $blogId as blog id
 *
 * @param $status as status of blog
 *
 * @return redirect page. 
 */
/* 	public function changeBlogStatus($blogId = 0, $status = 0){
		if($status == 0	){
			$statusMessage	=	trans("Blog unpopulared successfully");
		}else{
			$statusMessage	=	trans("Blog populared successfully");
		}
		
		DB::table('blogs')->where("id",$blogId)->update(['is_popular'=>$status]);
		
		//$this->_update_all_status('blogs',$blogId,$status,'is_popular');
		
		Session::flash('flash_notice', $statusMessage);
		return Redirect::to('admin/blog/');
	} // end updateBlogStatus() */
}//end BlogController