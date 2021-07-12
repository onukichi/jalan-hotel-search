<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $fillable = [
        'name',
        'code',
        'region_id',
    ];
}
