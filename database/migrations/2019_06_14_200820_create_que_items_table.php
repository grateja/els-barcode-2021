<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('que_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('que_id')->nullable();
            $table->string('code')->nullable();
            $table->boolean('done')->default(false);

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('que_id')->references('id')->on('ques')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('que_items');
    }
}
