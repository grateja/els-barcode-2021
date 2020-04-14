<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingMachineReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_machine_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('date_received')->nullable();
            $table->string('po_number')->nullable();
            $table->string('rr_number')->nullable();
            $table->string('dr_number')->nullable();
            $table->string('pi_number')->nullable();
            $table->string('billing_number')->nullable();
            $table->string('truck_number')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_machine_reports');
    }
}
