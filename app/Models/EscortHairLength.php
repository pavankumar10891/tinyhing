<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortHairLength
 * @package App
 */
class EscortHairLength extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
