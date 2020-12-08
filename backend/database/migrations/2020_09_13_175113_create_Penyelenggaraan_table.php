<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenyelenggaraanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_penyelenggaraan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('matkul_id'); 
            $table->uuid('user_id')->nullable();                       
            $table->string('kmatkul');
            $table->string('nmatkul');
            $table->string('sks');
            $table->tinyinteger('semester');
            $table->year('ta_matkul');
            $table->tinyinteger('idsmt');
            $table->year('tahun');            
            $table->tinyinteger('kjur');                
            $table->timestamps();  
                    
            $table->index('matkul_id');
            $table->index('user_id');
            $table->index('tahun');
            $table->index('idsmt');            
            $table->index('kjur');            
            
            $table->foreign('matkul_id')
                ->references('id')
                ->on('pe3_matakuliah')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('pe3_dosen')
                ->onDelete('set null')
                ->onUpdate('set null');
                
        });

        Schema::create('pe3_penyelenggaraan_dosen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penyelenggaraan_id');            
            $table->uuid('user_id');            
            $table->boolean('is_ketua')->default(0);            
            
            $table->timestamps();  
            
            $table->index('penyelenggaraan_id');                           
            $table->index('user_id');
           
            
            $table->foreign('penyelenggaraan_id')
                ->references('id')
                ->on('pe3_penyelenggaraan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('pe3_dosen')
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
        Schema::dropIfExists('pe3_penyelenggaraan');
    }
}
