<?php 
namespace App\Model; 
use Eloquent,Auth,DB;


/**
* Blogtag Model
*/
 
class Blogtag extends Eloquent  {
	
	/**
	* The database table used by the model.
	*
	* @var string
	*/
 
	protected $table = 'blog_tags';
}// end Blogtag class
