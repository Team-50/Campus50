<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesertaUjianPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_peserta_ujian_pmb', function (Blueprint $table) {
            $table->uuid('user_id')->primary();                                    
            $table->uuid('jadwal_ujian_id');                                                                                             
            $table->string('no_peserta',15);                                                
            $table->datetime('mulai_ujian')->nullable();            
            $table->datetime('selesai_ujian')->nullable();            
            $table->smallInteger('sisa_waktu')->default(0);            
            $table->boolean('isfinish')->default(0);                        
            $table->timestamps();

            $table->index('jadwal_ujian_id');            

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade') 
                    ->onUpdate('cascade');  

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
        Schema::dropIfExists('pe3_peserta_ujian_pmb');
    }
}
