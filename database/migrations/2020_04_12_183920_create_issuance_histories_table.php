<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issuance_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('account_id');
            $table->uuid('fixed_asset_id');
            $table->text('remarks')->nullable();
            $table->dateTime('issued')->nullable();
            $table->dateTime('returned')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('issuance_histories');
    }
}
