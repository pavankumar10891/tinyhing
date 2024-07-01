<?php 
namespace App\Model; 
use Eloquent;

/**
 * Banner Model
 */
class Banner extends Eloquent   {
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	 
	protected $table = 'banners';
	
	
	public function getImageAttribute($value = ""){
		if(!empty($value) && file_exists(BANNER_IMAGE_ROOT_PATH.$value)){
			return BANNER_IMAGE_URL.$value;
		}
	}
}
