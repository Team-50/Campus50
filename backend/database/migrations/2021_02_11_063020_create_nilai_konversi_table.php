<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiKonversiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_nilai_konversi1', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();   
            $table->string('nim')->nullable();

            $table->string('nama_mhs');               
            $table->string('alamat');
            $table->string('no_telp');
            $table->string('email');
            $table->string('nim_asal');
            $table->string('kode_jenjang',1);
            $table->string('kode_pt_asal',6); 
            $table->string('nama_pt_asal'); 
            $table->string('kode_ps_asal',6);
            $table->string('nama_ps_asal'); 
            $table->year('tahun'); 
            $table->unsignedInteger('kjur'); 
            $table->boolean('perpanjangan')->default(false);            

            $table->timestamps();  

            $table->index('nama_mhs');        
            $table->index('user_id');        
            $table->index('nim');        
            
            $table->foreign('user_id')
                ->references('user_id')
                ->on('pe3_register_mahasiswa')
                ->onDelete('set null')
                ->onUpdate('cascade');            

            $table->foreign('kode_jenjang')
                ->references('kode_jenjang')
                ->on('pe3_jenjang_studi')
                ->onDelete('cascade') 
                ->onUpdate('cascade');  
        });

        Schema::create('pe3_nilai_konversi2', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nilai_konversi_id');   
            $table->uuid('matkul_id');

            $table->string('kmatkul_asal');               
            $table->string('matkul_asal');
            $table->string('sks_asal');
            $table->string('n_kual',3);            
            $table->string('keterangan')->nullable();            

            $table->timestamps();  

            $table->index('nilai_konversi_id');        
            $table->index('matkul_id');        
            
            $table->foreign('nilai_konversi_id')
                ->references('id')
                ->on('pe3_nilai_konversi1')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('matkul_id')
                ->references('id')
                ->on('pe3_matakuliah')
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
        Schema::dropIfExists('pe3_nilai_konversi2');
        Schema::dropIfExists('pe3_nilai_konversi1');
    }
}
