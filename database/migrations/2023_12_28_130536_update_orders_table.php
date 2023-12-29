<?php

use App\Enums\OrderSatusEnum;
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
        Schema::table('orders', function(Blueprint $table){
            $table->string('status')->default(OrderSatusEnum::UNKNOWN->value)->change();
            $table->string('full_name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('remark')->nullable();
            $table->boolean('cash_desk_order');
            $table->string('customer_guid')->nullable();
            $table->boolean('paid');
            $table->string('foreign_status_id');
            $table->string('source');
            $table->decimal('vat', 10, 2);
            $table->decimal('to_pay', 10, 2);
            $table->string('currency_code');
            $table->decimal('with_vat', 10, 2);
            $table->decimal('without_vat', 10, 2);
            $table->decimal('exchange_rate', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('shipping')->nullable();
            $table->string('admin_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table){
            $table->integer('status')->default(OrderSatusEnum::NEW->value)->change();
            $table->dropColumn('full_name');
            $table->dropColumn('company');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('remark');
            $table->dropColumn('cash_desk_order');
            $table->dropColumn('customer_guid');
            $table->dropColumn('paid');
            $table->dropColumn('foreign_status_id');
            $table->dropColumn('source');
            $table->dropColumn('vat');
            $table->dropColumn('to_pay');
            $table->dropColumn('currency_code');
            $table->dropColumn('with_vat');
            $table->dropColumn('without_vat');
            $table->dropColumn('exchange_rate');
            $table->dropColumn('payment_method');
            $table->dropColumn('shipping');
            $table->dropColumn('admin_url');
        });
    }
};
