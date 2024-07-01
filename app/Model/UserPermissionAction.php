<?php
namespace App\Model; 
use Eloquent;


class UserPermissionAction extends Eloquent{
 
	protected $table = 'user_permission_actions';
	
	protected $fillable = ['user_id','user_permission_id','admin_module_id','admin_sub_module_id','admin_module_action_id','is_active'];

} // end Contact class
