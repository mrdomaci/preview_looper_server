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
        Schema::table('product_category_recommendations', function (Blueprint $table) {
            $table->foreignIdFor('App\Models\Category'::class, 'category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_category_recommendations', function (Blueprint $table) {
            $table->foreignIdFor('App\Models\Category'::class, 'category_id')->nullable(false)->change();
        });
    }
};
