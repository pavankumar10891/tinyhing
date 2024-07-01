<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortSmokerType
 * @package App
 */
class EscortSmokerType extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
