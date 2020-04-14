<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingSparepartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_sparepart_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('incoming_sparepart_report_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('incoming_sparepart_report_id')->references('id')->on('incoming_sparepart_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('parts_item_id')->references('id')->on('parts_items')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_sparepart_items');
    }
}
