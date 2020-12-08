<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoalPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);

        Schema::create('pe3_soal', function (Blueprint $table) {                                                                  
            $table->uuid('id')->primary();                        
            $table->text('soal');            
            $table->string('gambar',60)->nullable();            
            $table->unsignedInteger('prodi_id')->nullable();            
            $table->year('ta');
            $table->tinyInteger('semester');
            $table->boolean('active')->default(1);
            $table->timestamps();   
            
            $table->index('prodi_id');
            $table->foreign('prodi_id')
                ->references('id')
                ->on('pe3_prodi')
                ->onDelete('set null') 
                ->onUpdate('cascade'); 
                
        });        

        Schema::create('pe3_jawaban_soal', function (Blueprint $table) {                                                                  
            $table->uuid('id')->primary();                        
            $table->uuid('soal_id');                        
            $table->text('jawaban');            
            $table->boolean('status');            
            $table->string('options');            
            $table->timestamps();   
            
            $table->index('soal_id');
            $table->foreign('soal_id')
                ->references('id')
                ->on('pe3_soal')
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
        Schema::dropIfExists('pe3_jawaban_soal');
        Schema::dropIfExists('pe3_soal');
    }
}
