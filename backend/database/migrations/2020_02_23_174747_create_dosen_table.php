<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_dosen', function (Blueprint $table) {
            $table->uuid('user_id')->primary();            
            $table->string('nidn',15)->nullable();
            $table->string('nipy')->nullable();
            $table->string('nama_dosen');
            $table->string('gelar_depan',20)->nullable();
            $table->string('gelar_belakang',20)->nullable();         
            
            $table->string('tempat_lahir')->nullable(); 
            $table->date('tanggal_lahir')->nullable(); 
            $table->enum('jk',['L','P'])->default('L');
            
            $table->string('address1_desa_id',10)->nullable(); 
            $table->string('address1_kelurahan')->nullable(); 
            $table->string('address1_kecamatan_id',7)->nullable(); 
            $table->string('address1_kecamatan')->nullable(); 
            $table->string('address1_kabupaten_id',4)->nullable(); 
            $table->string('address1_kabupaten')->nullable(); 
            $table->string('address1_provinsi_id',2)->nullable(); 
            $table->string('address1_provinsi')->nullable(); 
            $table->string('alamat_rumah')->nullable(); 
            
            $table->string('nik',60)->nullable(); 
            $table->string('address2_desa_id',10)->nullable(); 
            $table->string('address2_kelurahan')->nullable(); 
            $table->string('address2_kecamatan_id',7)->nullable(); 
            $table->string('address2_kecamatan')->nullable(); 
            $table->string('address2_kabupaten_id',4)->nullable(); 
            $table->string('address2_kabupaten')->nullable(); 
            $table->string('address2_provinsi_id',2)->nullable(); 
            $table->string('address2_provinsi')->nullable(); 
            $table->string('alamat_ktp')->nullable(); 

            $table->boolean('is_dw')->default(0);
            
            $table->string('desc')->nullable();
            $table->boolean('active')->default(1);
            
            $table->timestamps();

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
        Schema::dropIfExists('pe3_dosen');
    }
}
