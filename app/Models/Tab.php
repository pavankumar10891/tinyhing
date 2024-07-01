<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainTab;
class Tab extends Model
{
    use HasFactory;

    public function maintabs()
    {
        return $this->hasMany(MainTab::class, 'tabs_id', 'id');
    }
}
