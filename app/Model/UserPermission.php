<?php
namespace App\Model; 
use Eloquent;


class UserPermission extends Eloquent{
 
	protected $table = 'user_permissions';

	protected $fillable = ['user_id','admin_module_id','is_active'];
} // end Contact class
