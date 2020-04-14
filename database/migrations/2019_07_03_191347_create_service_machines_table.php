<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_machines', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('service_report_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable()->comment('The machine');

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('service_report_id')->references('id')->on('service_reports')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_machines');
    }
}
