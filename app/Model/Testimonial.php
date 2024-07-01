<?php 
namespace App\Model; 
use Eloquent,Session;

/**
 * Pattern Model
*/
 
class Testimonial extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'testimonials';
	
	
	
	public function getImageAttribute($value){
		if(!empty($value) && file_exists(TESTIMONIAL_IMAGE_ROOT_PATH.$value)){
			return TESTIMONIAL_IMAGE_URL.$value;
		}else{
			return "";
		}
	}

}// end VehicleType class
