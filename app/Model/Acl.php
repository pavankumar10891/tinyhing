<?php
namespace App\Model; 
use Eloquent,DB;

class Acl extends Eloquent
{
	protected $table = 'admin_modules';
	
	protected $fillable = ['parent_id','type','title','path','icon','module_order','is_active'];

	public function get_admin_module_action(){
		return $this->hasMany('App\Model\AclAdminAction','admin_module_id','id');
	}
}