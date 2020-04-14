<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->uuid('finished_good_item_id')->nullable();
            $table->uuid('spare_part_item_id')->nullable();
            $table->uuid('incoming_finished_good_report_item_id')->nullable();
            $table->uuid('incoming_spare_part_report_item_id')->nullable();
            $table->uuid('outgoing_finished_good_report_item_id')->nullable();
            $table->uuid('outgoing_spare_part_report_item_id')->nullable();

            $table->text('remarks');
            $table->string('action')->comment('DELETE, UPDATE, CREATE, ADD, REMOVE, MOVE, ISSUE');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('finished_good_item_id')->references('id')->on('finished_good_items')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('spare_part_item_id')->references('id')->on('spare_part_items')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('incoming_finished_good_report_item_id')->references('id')->on('incoming_finished_good_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('incoming_spare_part_report_item_id')->references('id')->on('incoming_spare_part_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('outgoing_finished_good_report_item_id')->references('id')->on('outgoing_finished_good_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('outgoing_spare_part_report_item_id')->references('id')->on('outgoing_spare_part_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
