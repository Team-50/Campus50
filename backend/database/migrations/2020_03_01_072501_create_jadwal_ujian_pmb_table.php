<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalUjianPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_jadwal_ujian_pmb', function (Blueprint $table) {
            $table->uuid('id')->primary();                                    
            $table->string('nama_kegiatan');
            $table->smallInteger('jumlah_soal')->default(0);
            $table->date('tanggal_ujian');
            $table->string('jam_mulai_ujian',5);
            $table->string('jam_selesai_ujian',5);
            $table->date('tanggal_akhir_daftar');
            $table->smallInteger('durasi_ujian')->default(0);
            $table->uuid('ruangkelas_id');
            $table->year('ta');
            $table->tinyInteger('idsmt')->default(1);
            //status pendaftaran; 0 buka 1 tutup
            $table->boolean('status_pendaftaran')->default(0);
            //status ujian; 0 stop, 1 selesai
            $table->boolean('status_ujian')->default(0);

            $table->timestamps();
            
            $table->index('ruangkelas_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_jadwal_ujian_pmb');
    }
}
