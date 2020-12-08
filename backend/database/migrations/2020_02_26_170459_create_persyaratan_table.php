<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersyaratanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);

        Schema::create('pe3_persyaratan', function (Blueprint $table) {                                                                  
            $table->uuid('id')->primary();                        
            $table->string('proses');                                  
            $table->string('nama_persyaratan');                 
            $table->unsignedInteger('prodi_id')->nullable();                                         
            $table->year('ta');
            $table->timestamps();   
            
            $table->foreign('prodi_id')
                    ->references('id')
                    ->on('pe3_prodi')
                    ->onDelete('set null') 
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
        Schema::dropIfExists('pe3_persyaratan');
    }
}
