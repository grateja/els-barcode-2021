<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinishedGoodItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finished_good_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('serial_number');

            $table->uuid('finished_good_id');

            $table->uuid('client_id')->nullable()->comment('CURRENT CLIENT (IF ANY)');
            $table->uuid('subdealer_id')->nullable()->comment('CURRENT SUBDEALER (IF ANY)');
            $table->string('warehouse')->nullable()->comment('GOLDLAND_TOWER, WH1, WH2, WH3, WH4, WH5');
            $table->string('status')->default('IN_INVENTORY')->comment('RESERVED, ISSUED, IN_INVENTORY');
            $table->string('current_location')->nullable()->comment('specific_location:BAY_1, BAY_2...');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('finished_good_id')->references('id')->on('finished_goods')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('finished_good_items');
    }
}
