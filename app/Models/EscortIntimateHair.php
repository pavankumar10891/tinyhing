<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortIntimateHair
 * @package App
 */
class EscortIntimateHair extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
