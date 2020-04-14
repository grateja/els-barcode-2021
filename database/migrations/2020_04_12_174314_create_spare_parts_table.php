<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSparePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('part_number')->unique();
            $table->string('description')->nullable();
            $table->string('specs')->nullable();
            $table->string('supplier')->nullable()->comment('LG, ELECTROLUX, CSI, OTHER');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spare_parts');
    }
}
