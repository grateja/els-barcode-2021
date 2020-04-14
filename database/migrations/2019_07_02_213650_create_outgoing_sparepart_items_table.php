<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutgoingSparepartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_sparepart_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('outgoing_sparepart_report_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('outgoing_sparepart_report_id')->references('id')->on('outgoing_sparepart_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('outgoing_sparepart_items');
    }
}
