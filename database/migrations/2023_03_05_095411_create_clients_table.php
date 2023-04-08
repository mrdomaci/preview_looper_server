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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('oauth_access_token');
            $table->integer('eshop_id');
            $table->string('eshop_name')->nullable();
            $table->string('eshop_category')->nullable();
            $table->string('eshop_subtitle')->nullable();
            $table->string('constact_person')->nullable();
            $table->string('url');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('status');
            $table->dateTime('last_synced_at')->nullable();
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
        Schema::dropIfExists('client');
    }
};
