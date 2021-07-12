<?php

use Illuminate\Database\Seeder;

class HotelTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('hotel_types')->insert(
            [
                ['id' => 1, 'name' => 'ホテル'],
                ['id' => 2, 'name' => '旅館'],
                ['id' => 3, 'name' => 'ペンション'],
                ['id' => 4, 'name' => '貸別荘'],
                ['id' => 5, 'name' => 'ロッジ'],
                ['id' => 6, 'name' => 'ユースホステル'],
                ['id' => 7, 'name' => 'コンドミニアム'],
                ['id' => 8, 'name' => '民宿'],
                ['id' => 9, 'name' => 'トレーラハウス'],
            ]
        );
    }
}
