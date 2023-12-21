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
        Schema::table('client_settings_service_options', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\SettingsServiceOption::class, 'settings_service_option_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_settings_service_options', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\SettingsServiceOption::class, 'settings_service_option_id')->change();
        });
    }
};
