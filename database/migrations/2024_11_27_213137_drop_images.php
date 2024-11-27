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
        Schema::dropIfExists('images');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->foreignIdFor(\App\Models\Product::class, 'product_id');
            $table->string('name');
            $table->string('hash');
            $table->integer('priority')->nullable();
        });
    }
};
