<?php

use App\Connector\Shoptet\Product;
use App\Models\Category;
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
            $table->dropColumn('product_id');
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
        Schema::table('product_category_recommendations', function (Blueprint $table) {
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->index('product_id');
            $table->index('category_id');
        });
    }
};
