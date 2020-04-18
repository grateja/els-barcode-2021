<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_asset_remarks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('fixed_asset_id');
            $table->text('content');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fixed_asset_id')->references('id')->on('fixed_assets')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_remarks');
    }
}
