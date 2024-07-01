<?php
namespace App\Model; 
use Eloquent,DB;

class AclAdminAction extends Eloquent
{
	protected $table = 'admin_module_actions';
	
	protected $fillable = ['admin_module_id','name','function_name'];
	
}