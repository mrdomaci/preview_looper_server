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
            $table->index('product_id');
            $table->index('category_id');
            $table->index('client_id');
            $table->unique(['product_id', 'category_id', 'client_id'], 'product_category_client_unique');
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
            $table->dropIndex('product_id');
            $table->dropIndex('category_id');
            $table->dropIndex('client_id');
            $table->dropUnique('product_category_client_unique');
        });
    }
};
