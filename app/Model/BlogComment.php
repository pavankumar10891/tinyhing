<?php
namespace App\Model; 

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Eloquent,DB;

class BlogComment extends Eloquent  {
	
	
	protected $table = 'blog_comments';

    /**
     * The database table used by the model.
     *
     * @var string
     */

	public function blogCommentData($blogId	=	0){
		
		$commentDetails										=	BlogComment::where('blog_id',$blogId)
																->where('parent_comment_id', '=', 0)
																->leftJoin('users',	'users.id', '=','blog_comments.user_id')
																->select('blog_comments.*','users.name as name')
																->get()
																->toArray();
		if(!empty($commentDetails)){
			foreach($commentDetails as $key=>$value){
				$commentDetails[$key]["subcomments"]		=	BlogComment::where('parent_comment_id',$value["id"])
																->where('parent_comment_id', '!=', 0)
																->leftJoin('users',	'users.id', '=','blog_comments.user_id')
																->select('blog_comments.*','users.name as name',DB::raw("(select profile_image from profile_picture where id=users.image ) as user_image"))
																->get()
																->toArray();
			}
		}
		
		return $commentDetails;
	} 
}
