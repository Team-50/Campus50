<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanUjianPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);

        Schema::create('pe3_jawaban_ujian', function (Blueprint $table) {                                                                  
            $table->uuid('id')->primary();                        
            $table->uuid('soal_id');            
            $table->uuid('jawaban_id');            
            $table->uuid('user_id');                        
            $table->timestamps();   
            
            $table->index('soal_id');
            $table->index('jawaban_id');
            $table->index('user_id');

            $table->foreign('soal_id')
                ->references('id')
                ->on('pe3_soal')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 

            $table->foreign('jawaban_id')
                ->references('id')
                ->on('pe3_jawaban_soal')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('pe3_jawaban_ujian');        
    }
}
