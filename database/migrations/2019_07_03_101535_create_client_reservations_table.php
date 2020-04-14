<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_reservations', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('downpayment_date')->nullable();
            $table->string('cr_number')->nullable();

            $table->unsignedInteger('client_id')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_reservations');
    }
}
