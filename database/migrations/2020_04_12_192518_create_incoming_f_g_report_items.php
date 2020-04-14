<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingFGReportItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_f_g_report_items', function (Blueprint $table) {
            $table->uuid('incoming_report_id');
            $table->uuid('finished_good_item_id');

            $table->foreign('incoming_report_id')->references('id')->on('incoming_finished_good_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('finished_good_item_id')->references('id')->on('finished_good_items')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_finished_good_report_items');
    }
}
