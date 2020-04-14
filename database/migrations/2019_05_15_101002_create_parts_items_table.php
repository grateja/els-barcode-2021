<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('parts_info_id')->nullable();
            // $table->string('unique_code')->unique();
            $table->string('serial_number')->unique()->nullable();

            $table->string('status')->default('in_inventory')->comment('in_inventory, for_reservation, issued_to_client');
            $table->unsignedInteger('client_id')->nullable();

            $table->string('current_location')->nullable()->comment('current place');
            $table->string('specific_location')->nullable();
            // $table->string('specific_location')->nullable();

            // $table->unsignedInteger('incoming_report_id')->nullable();
            // $table->unsignedInteger('outgoing_report_id')->nullable();

            // $table->boolean('is_pulledout')->default(false);
            // $table->boolean('is_issued')->default(false);

            // $table->string('status')->nullable()->comment('in_inventory, issued_to_client, reserved');

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('parts_info_id')->references('id')->on('parts_infos')->onDelete('CASCADE')->onUpdate('CASCADE');
            // $table->foreign('incoming_report_id')->references('id')->on('incoming_reports')->onDelete('SET NULL');
            // $table->foreign('outgoing_report_id')->references('id')->on('outgoing_reports')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parts_items');
    }
}
