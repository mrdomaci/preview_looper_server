<?php

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
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
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('product_guid');
            $table->string('category_guid');
            $table->foreignIdFor(Client::class);
            //$table->dropForeignIdFor(Product::class);
            //$table->dropForeignIdFor(Category::class);
            $table->unique(['product_guid', 'category_guid', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('product_guid');
            $table->dropColumn('category_guid');
            $table->dropForeign(['client_id']);
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(Category::class);
            $table->dropUnique(['product_guid', 'category_guid', 'client_id']);
        });
    }
};
