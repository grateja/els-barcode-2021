<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservedFinishedGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved_finished_goods', function (Blueprint $table) {
            $table->uuid('finished_good_item_id');
            $table->uuid('reservation_id');

            $table->foreign('finished_good_item_id')->references('id')->on('finished_good_items')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('reserved_finished_goods');
    }
}
