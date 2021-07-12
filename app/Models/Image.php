<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'url',
        'title',
        's3_path',
        'entity_type',
        's3_key',
        's3_bucket',
        's3_region',
    ];

    public function uploadImage($image, $folder = '')
    {
        $path = Storage::disk('s3')->putFileAs($folder, $image, $image->hashName(), 'public');
        $url  = Storage::disk('s3')->url($path);

        return $url;
    }
}
