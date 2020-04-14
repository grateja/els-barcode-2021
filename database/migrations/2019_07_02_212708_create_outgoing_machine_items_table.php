<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutgoingMachineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_machine_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('outgoing_machine_report_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('outgoing_machine_report_id')->references('id')->on('outgoing_machine_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('outgoing_machine_items');
    }
}
