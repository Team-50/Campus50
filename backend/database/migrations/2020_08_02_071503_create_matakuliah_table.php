<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatakuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_matakuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();    
            $table->tinyInteger('id_group')->nullable();
            $table->string('nama_group')->nullable();
            $table->string('group_alias')->nullable();
            $table->string('kmatkul');
            $table->string('nmatkul');
            $table->tinyinteger('sks');
            $table->tinyinteger('idkonsentrasi')->nullable();
            $table->boolean('ispilihan')->default(false);
            $table->boolean('islintas_prodi')->default(false);
            $table->tinyinteger('semester');
            $table->tinyinteger('sks_tatap_muka');
            $table->tinyinteger('sks_praktikum')->nullable();
            $table->tinyinteger('sks_praktik_lapangan')->nullable();
            $table->string('minimal_nilai',10);
            $table->boolean('syarat_skripsi')->default(false);
            $table->boolean('status')->default(false);
            $table->year('ta');
            $table->tinyinteger('kjur');
            $table->timestamps();  
            
            $table->index('kmatkul');
            $table->index('nmatkul');
            $table->index('kjur');
            $table->index('ta');
            $table->index('id_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_matakuliah');        
    }
}
