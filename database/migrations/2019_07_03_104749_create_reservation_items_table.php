<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('client_reservation_id')->nullable();
            $table->unsignedInteger('parts_item_id')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('client_reservation_id')->references('id')->on('client_reservations')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('reservation_items');
    }
}
