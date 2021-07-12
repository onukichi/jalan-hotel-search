<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotSpring extends Model
{
    protected $fillable = [
        'onsen_name',
        'onsen_address',
        'region_id',
        'prefecture_id',
        'large_area_id',
        'small_area_id',
        'nature_of_onsen',
        'onsen_area_name',
        'onsen_area_caption',
        'hotel_count',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prefecture()
    {
        return $this->belongsTo('App\Models\Prefecture');
    }
}
