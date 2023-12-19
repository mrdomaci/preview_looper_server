<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Advert::class,'product_id')->nullable();
            $table->string('primary_text');
            $table->string('secondary_text')->nullable();
            $table->string('link');
            $table->string('image_url')->nullable();
            $table->string('template');
            $table->string('producer');
            $table->dateTime('date_show_from');
            $table->dateTime('date_show_to');
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
        Schema::dropIfExists('adverts');
    }
};
