<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('queue_id');
            $table->string('code');

            $table->timestamps();

            $table->foreign('queue_id')->references('id')->on('queues')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_items');
    }
}
