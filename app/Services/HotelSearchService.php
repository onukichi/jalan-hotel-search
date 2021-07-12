<?php
namespace App\Services;

use App\Models\Hotel;
use App\Models\Prefecture;

/**
 * Class HotelSearchService.
 */
class HotelSearchService
{
    public function search($searchConditions)
    {
        $hotelQuery = Hotel::select('hotels.*');

        if (isset($searchConditions['prefecture'])) {
            $hotelQuery = $hotelQuery->where('prefecture_id', $searchConditions['prefecture']);
            $searchConditions['prefecture_name'] = Prefecture::find($searchConditions['prefecture'], ['name']);
        } else {
            $searchConditions['prefecture'] = null;
            $searchConditions['prefecture_name'] = null;
        }

        if (isset($searchConditions['hotel_type'])) {
            $hotelQuery = $hotelQuery->where('hotel_type_id', $searchConditions['hotel_type']);
        } else {
            $searchConditions['hotel_type'] = null;
        }

        if (isset($searchConditions['hot_spring'])) {
            $hotelQuery = $hotelQuery->where('hot_spring_id', $searchConditions['hot_spring']);
        } else {
            $searchConditions['hot_spring'] = null;
        }

        if (isset($searchConditions['keyword'])) {
            $formattedKeyword  = str_replace('　', ' ', $searchConditions['keyword']);
            $keywords          = explode(' ', $formattedKeyword);

            foreach ($keywords as $keyword) {
                $hotelQuery = $hotelQuery->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orwhere('address', 'like', '%'.$keyword.'%')
                        ->orwhere('caption', 'like', '%'.$keyword.'%')
                        ->orwhere('onsen_name', 'like', '%'.$keyword.'%');
                });
            }
        } else {
            $searchConditions['keyword'] = null;
        }

        if (isset($searchConditions['sort_direction'])) {
            if ($searchConditions['sort_direction'] === 'price_desc') {
                $hotelQuery->orderBy('sample_rate_from', 'desc');
            } elseif ($searchConditions['sort_direction'] === 'price_asc') {
                $hotelQuery->orderBy('sample_rate_from', 'asc');
            } elseif ($searchConditions['sort_direction'] === 'checkin_asc') {
                $hotelQuery->orderBy('checkin_time', 'asc');
            } elseif ($searchConditions['sort_direction'] === 'checkout_desc') {
                $hotelQuery->orderBy('checkout_time', 'desc');
            }
        } else {
            $searchConditions['sort_direction'] = null;
            $hotelQuery                         = $hotelQuery->orderBy('number_rate', 'desc')->orderBy('rate', 'desc');
        }

        $total  = $hotelQuery->count();
        $hotels = $hotelQuery->simplePaginate(24)->withPath(route('hotels.index', null, false));

        $hotels->load('images');

        $searchData = [
            'hotels'           => $hotels,
            'searchConditions' => $searchConditions,
            'total'            => $total,
        ];

        return $searchData;
    }
}
