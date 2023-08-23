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
        Schema::table('images', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->string('hash')->primary()->change();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->id('id')->first();
        });

        // Drop the primary key constraint
        Schema::table('images', function (Blueprint $table) {
            $table->dropPrimary();
        });

        // Set the 'id' column as the primary key again
        Schema::table('images', function (Blueprint $table) {
            $table->primary('id')->change();
        });
    }
};
