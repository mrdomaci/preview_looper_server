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
        Schema::table('client_service_queues', function (Blueprint $table) {
            $table->timestamp('queued_at')->nullable()->after('client_service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_service_queues', function (Blueprint $table) {
            $table->dropColumn('queued_at');
        });
    }
};
