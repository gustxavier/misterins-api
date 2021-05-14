<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerVideoVDISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_video_v_d_i_s', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('path');
            $table->string('file_name');
            $table->string('file_extension');
            $table->enum('type',array('feed','story'));
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
        Schema::dropIfExists('partner_video_v_d_i_s');
    }
}
