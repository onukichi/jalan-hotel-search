<?php
namespace App\Helpers;

use App\Models\HotelImage;

class ImageHelper
{
    public static function getMainImage($hotelId)
    {
        $hotelMainImage = HotelImage::where('hotel_id', $hotelId)->where('type', 'main')->first();

        if (isset($hotelMainImage->image_url)) {
            return $hotelMainImage->image_url;
        }

        $hotelSubImage = HotelImage::where('hotel_id', $hotelId)->where('type', 'sub')->first();

        if (isset($hotelSubImage->image_url)) {
            return $hotelSubImage->image_url;
        }

        return 'https://trip-sns.s3-ap-northeast-1.amazonaws.com/assets/default_image.png';
    }

    public static function setUserProfileImageUrl($image)
    {
        if (empty($image)) {
            return 'https://res.cloudinary.com/frip/image/upload/c_scale,h_176,q_100/v1585315708/stayfan-prod/f_f_object_174_s512_f_object_174_0bg_yxoidp.png';
        }

        return $image->url;
    }
}
