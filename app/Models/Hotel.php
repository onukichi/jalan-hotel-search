<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'hotel_code',
        'name',
        'name_kana',
        'post_code',
        'address',
        'region_id',
        'prefecture_id',
        'large_area_id',
        'small_area_id',
        'hotel_type_id',
        'catch_copy',
        'caption',
        'checkin_time',
        'checkout_time',
        'sample_rate_from',
        'last_update',
        'onsen_name',
        'hot_spring_id',
        'number_rate',
        'rate',
        'media_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\HotelImage', 'hotel_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms()
    {
        return $this->hasMany('App\Models\Room', 'hotel_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotelType()
    {
        return $this->belongsTo('App\Models\HotelType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prefecture()
    {
        return $this->belongsTo('App\Models\Prefecture');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function smallArea()
    {
        return $this->belongsTo('App\Models\SmallArea');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function largeArea()
    {
        return $this->belongsTo('App\Models\LargeArea');
    }
}
