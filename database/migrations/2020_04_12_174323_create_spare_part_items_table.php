<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSparePartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spare_part_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('serial_number');

            $table->uuid('spare_part_id');

            $table->uuid('client_id')->nullable()->comment('CURRENT CLIENT (IF ANY)');
            $table->uuid('subdealer_id')->nullable()->comment('CURRENT SUBDEALER (IF ANY)');
            $table->string('warehouse')->comment('GOLDLAND_TOWER, WH1, WH2, WH3, WH4, WH5');
            $table->string('status')->comment('RESERVED, ISSUED, IN_INVENTORY');
            $table->string('current_location')->nullable()->comment('specific_location:BAY_1, BAY_2...');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('spare_part_id')->references('id')->on('spare_parts')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('subdealer_id')->references('id')->on('subdealers')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spare_part_items');
    }
}
