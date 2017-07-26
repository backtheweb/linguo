<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLinguoCatalogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $locale = app()->getLocale();

        Schema::create('linguo_catalogs', function (Blueprint $table) use ($locale) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('locale', 5)->default($locale);
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

    }
}
