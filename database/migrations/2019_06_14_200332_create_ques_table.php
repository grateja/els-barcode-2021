<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ques', function (Blueprint $table) {
            $table->increments('id');

            $table->dateTime('date_scaned')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('name')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('completed')->default(false);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ques');
    }
}
