<?php 
namespace App\Model; 
use Eloquent,Session;

/**
 * DropDown Model
*/
 
class Lookup extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
 
	protected $table = 'lookups';
 
	public function  getActiveIdentityTypeList(){
		$result = Lookup::where('lookup_type',"identity_type")->where('is_active',1)->orderBy('id','ASC')->pluck("code","id")->toArray();
		return $result;
	}//end getActiveIdentityTypeList()
	
}// end DropDown class
