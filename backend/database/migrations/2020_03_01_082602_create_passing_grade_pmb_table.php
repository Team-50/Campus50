<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassingGradePmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_passing_grade_pmb', function (Blueprint $table) {
            $table->uuid('id')->primary();            
            $table->uuid('jadwal_ujian_id');                              
            $table->unsignedInteger('kjur'); 
            $table->smallInteger('nilai'); 

            $table->timestamps();
            
            $table->index('jadwal_ujian_id');

            $table->foreign('jadwal_ujian_id')
                    ->references('id')
                    ->on('pe3_jadwal_ujian_pmb')
                    ->onDelete('cascade') 
                    ->onUpdate('cascade');  
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_passing_grade_pmb');
    }
}
