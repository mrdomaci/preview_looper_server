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
        Schema::dropIfExists('client_service_queues');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('client_service_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientService::class);
            $table->string('status')->default('NEW');
            $table->timestamp('queued_at')->nullable();
            $table->timestamps();
        });
    }
};
