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
        Schema::table('queues', function (Blueprint $table) {
            $table->dropUnique(['job_id','client_service_id', 'type']);
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropPrimary();
            $table->unsignedBigInteger('id')->change();
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->primary(['job_id','client_service_id', 'type']);
        });
    }
};
