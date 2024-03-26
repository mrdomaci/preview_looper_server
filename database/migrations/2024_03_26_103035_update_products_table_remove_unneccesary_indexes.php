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
            $table->dropIndex('products_is_negative_stock_allowed_index');
            $table->dropIndex('products_stock_index');
            $table->dropIndex('products_availability_foreign_id_index');
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
            $table->index('is_negative_stock_allowed');
            $table->index('stock');
            $table->index('availability_foreign_id');
        });
    }
};
