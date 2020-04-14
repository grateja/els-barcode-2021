<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_asset_tags', function (Blueprint $table) {
            $table->uuid('fixed_asset_id');
            $table->uuid('tag_id');

            $table->foreign('fixed_asset_id')->references('id')->on('fixed_assets')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_tags');
    }
}
