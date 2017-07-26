<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLinguoSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linguo_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('key_id', false, true);
            $table->string('source');
            $table->timestamps();
            $table->foreign('key_id')->references('id')->on('linguo_keys');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
