<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts_infos', function (Blueprint $table) {
            $table->increments('id');

            $table->string('code')->unique();
            $table->string('description');
            $table->text('specs')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->string('item_type')->nullable(); // spare parts / machine

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parts_infos');
    }
}
