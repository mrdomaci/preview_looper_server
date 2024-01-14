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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->foreignIdFor(\App\Models\Order::class, 'order_id');
            $table->string('order_guid');
            $table->foreignIdFor(\App\Models\Product::class, 'product_id')->nullable();
            $table->string('product_guid');
            $table->index('product_guid', 'product_guid_order_product_index');
            $table->index('order_guid', 'order_guid_order_product_index');
            $table->index('client_id', 'client_id_order_product_index');
            $table->index('order_id', 'order_id_order_product_index');
            $table->index('product_id', 'product_id_order_product_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
};
