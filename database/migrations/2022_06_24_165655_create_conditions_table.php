<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->bigIncrements('condition_id');
            $table->unsignedBigInteger('user_id');
            $table->date('condition_date');
            $table->string('condition_symptoms', 255)->nullable();
            $table->string('temperature', 255)->nullable();
            $table->string('oxygen_lvl', 255)->nullable();
            $table->string('condition_summary', 100);
            $table->timestamps();
        });

        Schema::table('conditions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditions');
    }
}
