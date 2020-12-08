<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiUjianPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_nilai_ujian_pmb', function (Blueprint $table) {
            $table->uuid('user_id')->primary();                                    
            $table->uuid('jadwal_ujian_id')->nullable();                                                                                             
            $table->smallInteger('jumlah_soal')->nullable();
            $table->smallInteger('jawaban_benar')->nullable();
            $table->smallInteger('jawaban_salah')->nullable();
            $table->smallInteger('soal_tidak_terjawab')->nullable();
            $table->smallInteger('passing_grade_1')->nullable();
            $table->smallInteger('passing_grade_2')->nullable();
            $table->decimal('nilai',5,2)->nullable();
            $table->boolean('ket_lulus')->default(0);
            $table->unsignedInteger('kjur');
            $table->string('desc')->nullable();

            $table->timestamps();

            $table->index('jadwal_ujian_id');            

            $table->foreign('user_id')
                    ->references('user_id')
                    ->on('pe3_formulir_pendaftaran')
                    ->onDelete('cascade') 
                    ->onUpdate('cascade');  

            $table->foreign('jadwal_ujian_id')
                    ->references('id')
                    ->on('pe3_jadwal_ujian_pmb')
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
        Schema::dropIfExists('pe3_nilai_ujian_pmb');
    }
}
