<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class EscortReview
 * @package App
 *
 * @property int                                                         $id
 * @property int                                                         $reviewer_id
 * @property int                                                         $reviewed_id
 * @property int                                                         $rating
 * @property string                                                      $text
 * @property int                                                         $duration
 * @property int                                                         $visit_location_id
 *
 * Dates
 *
 * @property \Carbon\Carbon                                              $created_at
 * @property \Carbon\Carbon                                              $updated_at
 * @property \Carbon\Carbon                                              $date
 *
 * Relations
 *
 * @property \App\User                                                   $reviewer
 * @property \App\User                                                   $reviewed
 * @property \Illuminate\Database\Eloquent\Collection|\App\EscortReview[] $services
 *
 * Methods
 *
 * @method static Builder filter(Request $request)
 * @method static \Illuminate\Database\Eloquent\Model|$this create(array $attributes = [])
 */
class EscortReview extends Model
{
    const AVAILABLE_RATINGS = [1, 2, 3, 4, 5];

    /**
     * @var array
     */
    protected $fillable = [
        'reviewed_by',
        'reviewed_user_id',
        'rating_appearance',
        'rating_service',
        'text',
        'heading',
        'duration_id',
        'meeting_point_id',
        'date',
    ];

    protected $with = [
        'reviewed',
        'reviewer',
        'reviewed.user',
        'meetingPoint',
        'duration',
        'reviewer.member',
        'reviewer.member.user',
        'reviewer.member.images'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * @param $value
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewed()
    {
        return $this->belongsTo(Escort::class, 'reviewed_user_id');
    }


    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meetingPoint()
    {
        return $this->belongsTo(MeetingPoint::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duration()
    {
        return $this->belongsTo(Duration::class);
    }

    public function scopeFilter(Builder $query, Request $data)
    {
        if (isset($data['active'])) {
            $query->where('active', $data['active']);
        }
    }

    public function scopeApproved(Builder $query, $approved)
    {
        if (isset($approved)) {
            $query->where('active', $approved);
        }
    }
}
