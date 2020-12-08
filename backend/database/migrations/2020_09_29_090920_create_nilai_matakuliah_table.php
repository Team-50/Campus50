<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiMatakuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_nilai_matakuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('krsmatkul_id')->unique();               
            $table->uuid('penyelenggaraan_id');
            $table->uuid('penyelenggaraan_dosen_id')->nullable();
            $table->uuid('kelas_mhs_id')->nullable();
            $table->uuid('user_id_mhs'); 
            $table->uuid('user_id_created'); 
            $table->uuid('user_id_updated'); 
            $table->uuid('krs_id');

            $table->decimal('persentase_absen',5,2)->default(0.00);               
            $table->decimal('persentase_quiz',5,2)->default(0.00);               
            $table->decimal('persentase_tugas_individu',5,2)->default(0.00);               
            $table->decimal('persentase_tugas_kelompok',5,2)->default(0.00);               
            $table->decimal('persentase_uts',5,2)->default(0.00);               
            $table->decimal('persentase_uas',5,2)->default(0.00);  

            $table->decimal('nilai_absen',5,2)->default(0.00);    
            $table->decimal('nilai_quiz',5,2)->default(0.00);    
            $table->decimal('nilai_tugas_individu',5,2)->default(0.00);    
            $table->decimal('nilai_tugas_kelompok',5,2)->default(0.00);    
            $table->decimal('nilai_uts',5,2)->default(0.00);    
            $table->decimal('nilai_uas',5,2)->default(0.00);    
            $table->decimal('n_kuan',5,2)->default(0.00);    
            $table->string('n_kual',3)->default(0.00);  
            $table->decimal('n_mutu',5,2)->default(0.00);    

            $table->boolean('telah_isi_kuesioner')->default(false);
            $table->datetime('tanggal_isi_kuesioner')->nullable();

            $table->timestamps();  

            $table->index('penyelenggaraan_id');
            $table->index('user_id_mhs');
            $table->index('user_id_created');
            $table->index('user_id_updated');
            $table->index('krs_id');
            $table->index('penyelenggaraan_dosen_id');
            $table->index('kelas_mhs_id');

            $table->foreign('krsmatkul_id')
                ->references('id')
                ->on('pe3_krsmatkul')
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
        Schema::dropIfExists('pe3_nilai_matakuliah');
    }
}
