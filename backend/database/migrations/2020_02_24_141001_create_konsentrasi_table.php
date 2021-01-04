<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonsentrasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsentrasi', function (Blueprint $table) {
            $table->increments('id');                                                         
            $table->unsignedInteger('prodi_id');  
            $table->string('nama_konsentrasi',5);             
            $table->timestamps();
            
            $table->index('prodi_id');  

            $table->foreign('prodi_id')
                    ->references('id')
                    ->on('pe3_prodi')
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
        Schema::dropIfExists('konsentrasi');
    }
}