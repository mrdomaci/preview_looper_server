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
            $table->string('product_guid')->nullable();
            $table->string('category_guid')->nullable();
            $table->unique(['product_guid', 'category_guid', 'client_id'], 'pcr_product_client_category_unique');
            $table->string('product_id')->nullable()->change();
            $table->string('category_id')->nullable()->change();
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
            $table->dropUnique('pcr_product_client_category_unique');
            $table->dropColumn('product_guid');
            $table->dropColumn('category_guid');
            $table->string('product_id')->nullable(false)->change();
            $table->string('category_id')->nullable(false)->change();
        });
    }
};
