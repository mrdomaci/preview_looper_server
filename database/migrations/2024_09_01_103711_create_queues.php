<?php

use App\Models\ClientService;
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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('job_id');
            $table->string('status');
            $table->string('endpoint')->nullable();
            $table->string('reqsult_url')->nullable();
            $table->foreignIdFor(ClientService::class, 'client_service_id');
            $table->timestamps();

            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queues');
    }
};
