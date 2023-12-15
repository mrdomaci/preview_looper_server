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
            $table->string("name")->nullable();
            $table->string("perex")->nullable();
            $table->string("producer")->nullable();
            $table->string("category")->nullable();
            $table->string("subcategory")->nullable();
            $table->string("price")->nullable();
            $table->string('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint  $table) {
            $table->dropColumn('name');
            $table->dropColumn('perex');
            $table->dropColumn('producer');
            $table->dropColumn('category');
            $table->dropColumn('subcategory');
            $table->dropColumn('price');
            $table->dropColumn('url');
        });
    }
};
