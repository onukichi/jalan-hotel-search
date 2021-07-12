<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotSpringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hot_springs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('onsen_name');
            $table->string('onsen_address');
            $table->unsignedBigInteger('region_id')->index();
            $table->unsignedBigInteger('prefecture_id')->index();
            $table->unsignedBigInteger('large_area_id')->index();
            $table->unsignedBigInteger('small_area_id')->index();
            $table->string('nature_of_onsen');
            $table->string('onsen_area_name');
            $table->string('onsen_area_caption');
            $table->unsignedInteger('hotel_count')->default(0);
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
        Schema::dropIfExists('hot_springs');
    }
}
