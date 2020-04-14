<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingSPReportItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_s_p_report_items', function (Blueprint $table) {
            $table->uuid('incoming_report_id');
            $table->uuid('spare_part_item_id');

            $table->foreign('incoming_report_id')->references('id')->on('incoming_spare_part_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('spare_part_item_id')->references('id')->on('spare_part_items')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_spare_part_report_items');
    }
}
