<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmallArea extends Model
{
    protected $fillable = [
        'name',
        'code',
        'region_id',
        'prefecture_id',
        'large_area_id',
    ];
}
