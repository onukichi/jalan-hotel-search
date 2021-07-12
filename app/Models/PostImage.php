<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = [
        'post_id',
        'image_id',
    ];

    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id', 'id');
    }
}
