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
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->boolean('mark_as_paid')->default(0)->change();
            $table->boolean('change_order_items')->default(0)->change();
            $table->boolean('stock_claim_resolved')->default(0)->change();
            $table->boolean('system')->default(0)->change();
            $table->string('color')->nullable()->change();
            $table->string('background_color')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_statuses', function (Blueprint $table) {
                $table->boolean('mark_as_paid')->change();
                $table->boolean('change_order_items')->change();
                $table->boolean('stock_claim_resolved')->change();
                $table->boolean('system')->change();
                $table->string('color')->change();
                $table->string('background_color')->change();
        });
    }
};
