<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EscortServiceFor
 * @package App
 */
class EscortServiceFor extends Model
{
    use SoftDeletes;
    /**
     * @var string
     */
    protected $table = 'escort_services_for';

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function escorts(){
        return $this->belongsToMany(Escort::class, 'escort_escort_service_for')->withTimestamps();
    }
}
