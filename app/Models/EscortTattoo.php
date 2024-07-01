<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortTattoo
 * @package App
 */
class EscortTattoo extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
