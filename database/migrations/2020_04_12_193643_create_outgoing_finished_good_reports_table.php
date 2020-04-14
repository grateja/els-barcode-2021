<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutgoingFinishedGoodReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_finished_good_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('client_id')->nullable();
            $table->uuid('subdealer_id')->nullable();

            $table->uuid('reservation_id')->nullable();
            $table->dateTime('date_delivered')->nullable();
            $table->dateTime('po_date')->nullable();
            $table->dateTime('downpayment_date')->nullable();
            $table->dateTime('invoice_date')->nullable();
            $table->string('quotation_number')->nullable();
            $table->string('sales_invoice')->nullable();
            $table->string('dr_number')->nullable();
            $table->string('warranty_number')->nullable();
            $table->string('truck')->nullable()->comment('L300, Foton, 3PL [/Plate number]');
            $table->string('driver')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('subdealer_id')->references('id')->on('subdealers')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outgoing_finished_good_reports');
    }
}
