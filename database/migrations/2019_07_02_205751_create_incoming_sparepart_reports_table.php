<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingSparepartReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_sparepart_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('date_received')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('order_number')->nullable();
            $table->string('pi_number')->nullable();
            $table->string('rr_number')->nullable();

            $table->softDeletes();

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
        Schema::dropIfExists('incoming_sparepart_reports');
    }
}
