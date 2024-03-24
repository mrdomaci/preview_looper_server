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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->string('foreign_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_system');
            $table->boolean('is_on_stock')->default(false);
            $table->boolean('is_sold_out_negative_stock_allowed')->default(false);
            $table->boolean('is_sold_out_negative_stock_forbidden')->default(false);
            $table->string('on_stock_in_hours')->nullable();
            $table->string('delivery_in_hours')->nullable();
            $table->string('google_availability_id')->nullable();
            $table->string('google_availability_name')->nullable();
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
        Schema::dropIfExists('availabilies');
    }
};
