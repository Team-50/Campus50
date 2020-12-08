<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_prodi', function (Blueprint $table) {
            $table->increments('id');                        
            $table->string('kode_prodi',5)->unique();                        
            $table->string('kode_fakultas',10)->nullable();                        
            $table->string('nama_prodi',50);
            $table->string('nama_prodi_alias',50);
            $table->string('kode_jenjang',1);
            $table->string('nama_jenjang',15);
            $table->string('config')->nullable();
            
            $table->index('kode_fakultas'); 
            $table->index('kode_jenjang'); 
            
            $table->foreign('kode_fakultas')
                ->references('kode_fakultas')
                ->on('pe3_fakultas')
                ->onDelete('cascade') 
                ->onUpdate('cascade');  

            $table->foreign('kode_jenjang')
                ->references('kode_jenjang')
                ->on('pe3_jenjang_studi')
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
        Schema::dropIfExists('pe3_prodi');
    }
}
