<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutgoingMachineReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_machine_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('downpayment_date')->nullable();
            $table->dateTime('po_date')->nullable();
            $table->dateTime('invoice_date')->nullable();

            $table->string('quotation_number')->nullable();
            $table->string('sales_invoice')->nullable();
            $table->string('dr_number')->nullable();
            $table->string('warranty_number')->nullable();
            $table->string('truck')->nullable();
            $table->string('driver')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('sub_dealer_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outgoing_machine_reports');
    }
}
