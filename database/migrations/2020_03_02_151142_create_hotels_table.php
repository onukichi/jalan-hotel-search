<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hotel_code')->unique()->index();
            $table->string('name');
            $table->string('name_kana')->nullable();
            $table->string('post_code');
            $table->string('address');
            $table->unsignedBigInteger('region_id')->index();
            $table->unsignedBigInteger('prefecture_id')->index();
            $table->unsignedBigInteger('large_area_id')->index();
            $table->unsignedBigInteger('small_area_id')->index();
            $table->unsignedInteger('hotel_type_id')->index();
            $table->string('catch_copy');
            $table->text('caption');
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->unsignedInteger('sample_rate_from')->nullable();
            $table->date('last_update')->nullable();
            $table->string('onsen_name')->nullable();
            $table->string('number_of_ratings')->nullable();
            $table->string('rating')->nullable();
            $table->enum('media_type', ['stayfan', 'jalan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}
