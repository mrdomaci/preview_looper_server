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
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
            $table->unsignedBigInteger('id')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->primary(['client_id', 'code', 'guid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
