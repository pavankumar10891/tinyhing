<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    public function getLogoAttribute($value = ""){
		if(!empty($value) && file_exists(GROUP_LOGO_IMAGE_ROOT_PATH.$value)){
			return GROUP_LOGO_IMAGE_URL.$value;
		}
	}
}
