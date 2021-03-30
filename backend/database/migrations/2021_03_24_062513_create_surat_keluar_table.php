<?php
/**
 * tabel ini digunakan untuk menyimpan surat keluar
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratKeluarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_surat_keluar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id_created')->nullable();
            $table->uuid('nama_user_created')->nullable();
            $table->uuid('user_id_ttd')->nullable();
            $table->uuid('nama_user_ttd')->nullable();
            $table->string('nomor_surat');
            $table->integer('no_urut');
            $table->tinyInteger('bulan_surat');
            $table->year('tahun_surat');
            $table->string('perihal');
            $table->string('kepada');            
            $table->string('user_id_kepada')->nullable();            
            $table->text('isi_surat');
            $table->string('keterangan');
            $table->date('tanggal_surat');
            $table->string('qr_code')->nullable();            
            $table->string('path_scanan')->nullable(); 
            $table->string('klasifikasi_surat',20);           
            $table->year('ta');           
            $table->timestamps();              
            
            $table->index('user_id_created');             
            $table->index('user_id_ttd');           
            $table->index('nomor_surat');           
            $table->index('tahun_surat');           
            $table->index('no_urut');           
            $table->index('bulan_surat');           
            $table->index('ta');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_surat_keluar');
    }
}
