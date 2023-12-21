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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->integer('foreign_id');
            $table->string('name');
            $table->boolean('system');
            $table->integer('order');
            $table->boolean('mark_as_paid');
            $table->string('color')->nullable();
            $table->string('background_color')->nullable();
            $table->boolean('change_order_items');
            $table->boolean('stock_claim_resolved');
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
        Schema::dropIfExists('order_statuses');
    }
};
