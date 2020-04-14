<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateServiceTechniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_technicians', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('time_in')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('time_out')->nullable();
            $table->unsignedInteger('user_id')->nullable()->comment('Technician');
            $table->unsignedInteger('service_report_id')->nullable()->comment('Linked to SO');

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('service_technicians');
    }
}
