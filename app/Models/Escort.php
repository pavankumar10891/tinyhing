<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\City;
use App\Models\Category;
use App\Models\EscortService;
use App\Models\EscortSexuality;
use App\Models\EscortFigure;
use App\Models\EscortType;
use App\Models\EscortPiercing;
use App\Models\EscortSubService;
class Escort extends Model
{
    const escort_images = null;
    use HasFactory;
   
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(EscortType::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origin()
    {
        return $this->belongsTo(EscortOrigin::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hairColor()
    {
        return $this->belongsTo(EscortHairColor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hairLength()
    {
        return $this->belongsTo(EscortHairLength::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intimateHair()
    {
        return $this->belongsTo(EscortIntimateHair::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bodyHair()
    {
        return $this->belongsTo(EscortBodyHair::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function piercing()
    {
        return $this->belongsTo(EscortPiercing::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function smokerType()
    {
        return $this->belongsTo(EscortSmokerType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tattoo()
    {
        return $this->belongsTo(EscortTattoo::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sexuality()
    {
        return $this->belongsTo(EscortSexuality::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servicesFor()
    {
        return $this->belongsToMany(EscortServiceFor::class, 'escort_escort_service_for')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function languages()
    {
        return $this->morphToMany(Language::class, 'languagable');
    }

        /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('gallery_order', 'asc');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function escortimages()
    {
        return $this->hasMany(Image::class, 'imageable_id', 'id');
    }

    public function escorSerice()
    {
        return $this->hasMany(EscortService::class, 'escort_id', 'id');
    }

    public function receivedcity()
    {
        return $this->belongsTo(City::class, 'current_city_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function figure()
    {
        return $this->belongsTo(EscortFigure::class, 'figure_id', 'id');
    }

    public function escorttype()
    {
        return $this->belongsTo(EscortType::class, 'type_id', 'id');
    }

    public function escortPiercing()
    {
        return $this->belongsTo(EscortPiercing::class, 'piercing_id', 'id');
    }
    public function subservice()
    {
       return $this->hasManyThrough(EscortSubService::class, EscortService::class);
    }

}
