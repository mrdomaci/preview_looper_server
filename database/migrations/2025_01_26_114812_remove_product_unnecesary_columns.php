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
        Schema::table('products', function(Blueprint $table) {
            $table->dropColumn('perex');
            $table->dropColumn('producer');
            $table->dropColumn('category');
            $table->dropColumn('subcategory');
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function(Blueprint $table) {
            $table->string('perex')->nullable();
            $table->string('producer')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->foreignIdFor(\App\Models\Category::class, 'category_id')->nullable();
        });
    }
};
