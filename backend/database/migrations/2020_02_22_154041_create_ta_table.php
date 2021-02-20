<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_ta', function (Blueprint $table) {
            $table->year('tahun')->primary();            
            $table->string('tahun_akademik')->unique();  
            $table->date('awal_ganjil')->nullable();  
            $table->date('akhir_ganjil')->nullable();  
            $table->date('awal_genap')->nullable();  
            $table->date('akhir_genap')->nullable();  
            $table->date('awal_pendek')->nullable();  
            $table->date('akhir_pendek')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_ta');
    }
}
