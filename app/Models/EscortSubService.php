<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EscortService;

class EscortSubService extends Model
{
    public function subservice()
    {
        return $this->belongsTo(EscortService::class, 'escort_service_id', 'id');
    }
}
