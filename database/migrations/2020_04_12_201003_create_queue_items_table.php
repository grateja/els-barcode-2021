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
            $table->uuid('id')->primary()->comment('series');

            $table->uuid('queue_id');

            $table->timestamps();
            $table->softDeletes();

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
