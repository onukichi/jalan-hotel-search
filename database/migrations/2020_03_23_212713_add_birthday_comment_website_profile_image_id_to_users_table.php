<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthdayCommentWebsiteProfileImageIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthday')->nullable()->after('password');
            $table->text('comment')->nullable()->after('birthday');
            $table->string('website')->nullable()->after('comment');
            $table->unsignedBigInteger('profile_image_id')->default(0)->after('website');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('comment');
            $table->dropColumn('website');
            $table->dropColumn('profile_image_id');
        });
    }
}
