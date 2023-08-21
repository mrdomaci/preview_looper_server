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
        // Remove the 'id' column
        Schema::table('images', function (Blueprint $table) {
            $table->string('hash')->nullable();
        });
    }

    public function down()
    {
        // Remove the composite primary key
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
};
