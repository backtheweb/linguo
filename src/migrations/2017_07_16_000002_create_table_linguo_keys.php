<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLinguoKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linguo_keys', function (Blueprint $table) {

            $table->increments('id');
            $table->integer(   'catalog_id', false, true);
            $table->text(      'msgid');
            $table->text(      'msgid_plural');
            $table->timestamps();

            $table->foreign('catalog_id')->references('id')->on('linguo_catalogs');
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
