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
        Schema::create('client_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->foreignIdFor(\App\Models\Service::class, 'service_id');
            $table->string('oauth_access_token');
            $table->string('access_token')->nullable();
            $table->string('status');
            $table->index(['client_id', 'service_id', 'status']);
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
        Schema::dropIfExists('client_services');
    }
};
