<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_order_id')->unsigned();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');  
            $table->integer('vehicle_id')->unsigned();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');                      
            $table->string('created_by', 50);
            $table->date('date')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method', 10)->nullable();
            $table->date('paid_on')->nullable();
            $table->decimal('gst_rate', 13, 3)->default(0.000);
            $table->decimal('shop_supply_rate', 13, 3)->default(0.000);
            $table->decimal('total_labour', 13, 3)->default(0.000);
            $table->decimal('total_labour_cost', 13, 3)->default(0.000);
            $table->decimal('total_parts', 13, 3)->default(0.000);
            $table->decimal('total_parts_cost', 13, 3)->default(0.000);
            $table->decimal('sub_total', 13, 3)->default(0.000);
            $table->decimal('gst_total', 13, 3)->default(0.000);
            $table->decimal('grand_total', 13, 3)->default(0.000);                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
