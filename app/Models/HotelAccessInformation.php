<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelAccessInformation extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'description',
    ];
}
