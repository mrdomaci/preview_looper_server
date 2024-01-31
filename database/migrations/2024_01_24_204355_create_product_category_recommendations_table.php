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
        Schema::create('product_category_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor('App\Models\Product'::class, 'product_id');
            $table->foreignIdFor('App\Models\Category'::class, 'category_id');
            $table->foreignIdFor('App\Models\Client'::class, 'client_id');
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
        Schema::dropIfExists('product_category_recommendations');
    }
};
