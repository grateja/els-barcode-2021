<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingFinishedGoodReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_finished_good_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->dateTime('received_date');
            $table->string('po_number')->nullable()->comment('PRODUCT ORDER NUMBER');
            $table->string('rr_number')->nullable()->comment('RECEIVING REPORT NUMBER');
            $table->string('pi_number')->nullable()->comment('PROFORMA INVOICE NUMBER');
            $table->string('truck_number')->nullable()->comment('PLATE NUMBER (IF ANY)');

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
        Schema::dropIfExists('incoming_finished_good_reports');
    }
}
