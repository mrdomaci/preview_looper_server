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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('settings_infinite_repeat')->default(false);
            $table->boolean('settings_return_to_default')->default(true);
            $table->integer('settings_show_time')->default(1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('settings_infinite_repeat');
            $table->dropColumn('settings_return_to_default');
            $table->dropColumn('settings_show_time');
        });
    }
};
