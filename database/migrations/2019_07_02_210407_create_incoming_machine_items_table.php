<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingMachineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_machine_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('incoming_machine_report_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('incoming_machine_report_id')->references('id')->on('incoming_machine_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('incoming_machine_items');
    }
}
