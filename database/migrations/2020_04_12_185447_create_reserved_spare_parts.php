<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservedSpareParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved_spare_parts', function (Blueprint $table) {
            $table->uuid('spare_part_item_id');
            $table->uuid('reservation_id');

            $table->foreign('spare_part_item_id')->references('id')->on('spare_part_items')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserved_spare_parts');
    }
}
