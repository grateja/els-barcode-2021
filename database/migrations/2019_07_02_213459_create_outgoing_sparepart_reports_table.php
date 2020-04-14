<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutgoingSparepartReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_sparepart_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->date('date_delivered')->nullable();
            $table->string('quotation_number')->nullable();
            $table->string('sales_invoice')->nullable();
            $table->string('dr_number')->nullable();
            $table->string('warranty_number')->nullable();
            $table->string('truck')->nullable();
            $table->string('driver')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('sub_dealer_id')->nullable();

            $table->text('technicians')->nullable()->comment('List of technicians separated by comma');

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
        Schema::dropIfExists('outgoing_sparepart_reports');
    }
}
