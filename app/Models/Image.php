<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Escort;

/**
 * Class EscortType
 * @package App
 */
class Image extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
    public function escort()
    {
       // return $this->belongsTo(Escort::class, 'imageable_id', 'id');
        return $this->belongsTo(Escort::class);
    }
}
