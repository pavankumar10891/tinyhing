<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortBodyHair
 * @package App
 */
class EscortBodyHair extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
