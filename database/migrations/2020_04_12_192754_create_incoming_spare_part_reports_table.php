<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingSparePartReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_spare_part_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->dateTime('received_date');
            $table->string('tracking_number')->nullable();
            $table->string('rr_number')->nullable()->comment('RECEIVING REPORT NUMBER');
            $table->string('pi_number')->nullable()->comment('PROFORMA INVOICE NUMBER');
            $table->string('order_number')->nullable();

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
        Schema::dropIfExists('incoming_spare_part_reports');
    }
}
