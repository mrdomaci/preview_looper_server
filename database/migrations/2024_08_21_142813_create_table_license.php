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
        Schema::create('license', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(ClientService::class, 'client_service_id');
            $table->float('value');
            $table->string('currency');
            $table->date('valid_to');
            $table->boolean('is_active')->default(true);
            $table->string('foreign_id');
            $table->string('account_number');
            $table->string('bank_code');
            $table->string('variable_symbol')->nullable();
            $table->string('specific_symbol')->nullable();
            $table->string('constant_symbol')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('license');
    }
};
