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
            $table->string('code', 255)->nullable();
            $table->foreignIdFor(\App\Models\Product::class, 'parent_product_id')->nullable();
            $table->index('parent_product_id', 'parent_product_id_product_index');
            $table->index('code', 'code_product_index');
            $table->string('availability', 255)->nullable();
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
                $table->dropIndex(['parent_product_id_product_index']);
                $table->dropIndex(['code_product_index']);
                $table->dropColumn(['code', 'parent_product_id', 'availability']);
        });
    }
};
