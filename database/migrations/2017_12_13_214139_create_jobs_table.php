<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_order_id')->unsigned();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            $table->string('tech', 50);
            $table->string('title', 100);
            $table->string('description')->nullable();
            $table->decimal('hours', 13, 1)->default(0.0);
            $table->decimal('shop_rate', 13, 3)->default(0.000);
            $table->decimal('tech_hourly_rate', 13, 3)->default(0.000);
            $table->boolean('is_complete')->default(false);
            $table->decimal('tech_pay_total', 13, 3)->default(0.000);
            $table->decimal('job_labour_total', 13, 3)->default(0.000);
            $table->decimal('parts_total_cost', 13, 3)->default(0.000);
            $table->decimal('parts_total_billed', 13, 3)->default(0.000);
            $table->decimal('job_grand_total', 13, 3)->default(0.000);            
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
        Schema::dropIfExists('jobs');
    }
}
