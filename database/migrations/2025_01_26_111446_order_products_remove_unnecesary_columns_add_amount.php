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
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('order_id');
            $table->dropColumn('product_id');
            $table->integer('amount')->default(1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Order::class, 'order_id');
            $table->foreignIdFor(\App\Models\Product::class, 'product_id');
            $table->dropColumn('amount');
        });
    }
};
