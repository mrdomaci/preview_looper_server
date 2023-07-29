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
        Schema::create('client_settings_service_options', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SettingsService::class, 'settings_service_id');
            $table->foreignIdFor(\App\Models\Client::class, 'client_id');
            $table->foreignIdFor(\App\Models\SettingsServiceOption::class, 'settings_service_option_id');
            $table->string('value')->nullable();
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
        Schema::dropIfExists('client_settings_service_options');
    }
};
