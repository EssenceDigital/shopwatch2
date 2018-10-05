<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('shop_rate', 13, 3)->default(0.000);
            $table->decimal('gst_rate', 13, 3)->default(0.000);;
            $table->decimal('shop_supply_rate', 13, 3)->default(0.000);;
            $table->string('city', 50);
            $table->string('province', 25);
            $table->string('address');
            $table->string('postal_code', 7);
            $table->string('phone', 14);
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
        Schema::dropIfExists('bus_settings');
    }
}
