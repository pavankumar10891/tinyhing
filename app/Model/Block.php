<?php 
namespace App\Model; 
use Eloquent,Session;
use App;
use DB;

/**
 * Block Model
*/
 
class Block extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'blocks';
	
	
	public function getImageAttribute($value = ""){
		if(!empty($value) && file_exists(BLOCK_IMAGE_ROOT_PATH.$value)){
			return BLOCK_IMAGE_URL.$value;
		}
	}
	
	public function get_all_blocks($page = null) {
		$lang			=	App::getLocale();
		$lang_id		=	DB::table("languages")->where("lang_code",$lang)->value("id");
		$result11 = Block::where('page_name_slug',$page)->orderBy('block_order','ASC')->pluck("id")->toArray();
		$result = DB::table("block_descriptions")->where('language_id',$lang_id)->whereIn("parent_id",$result11)->select("block_descriptions.name","block_descriptions.description",DB::raw("(select image from blocks where id=block_descriptions.parent_id) as image"),DB::raw("(select slug from blocks where id=block_descriptions.parent_id) as slug"))->get()->toArray();
		
		$blocks = array();
		if(!empty($result)){
			foreach($result as $block){
				if(!empty($block->image) && file_exists(BLOCK_IMAGE_ROOT_PATH.$block->image)){
					$block->image		 = BLOCK_IMAGE_URL.$block->image;
				}else{
					$block->image		 = "";
				}
			}
		}
		return $result;
    }
	
}// end Block class
