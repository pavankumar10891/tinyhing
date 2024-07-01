<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscortMeta extends Model
{
    protected $fillable = [
        'escort_id',
        'lang_id',
        'meta_description',
        'meta_title',
        'meta_keywords',
    ];
}
