<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LargeArea extends Model
{
    protected $fillable = [
        'name',
        'code',
        'region_id',
        'prefecture_id',
    ];
}
