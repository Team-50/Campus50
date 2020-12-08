<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersyaratanPmbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);

        Schema::create('pe3_pmb_persyaratan', function (Blueprint $table) {                                                                  
            $table->uuid('id')->primary();                        
            $table->uuid('persyaratan_id')->index();                        
            $table->uuid('user_id')->index();                                  
            $table->string('nama_persyaratan');                                
            $table->string('path');                                                        
            $table->boolean('verified')->default(0);                                                        
            $table->string('descr')->nullable();                                                        
            $table->timestamps();   
            
            $table->foreign('persyaratan_id')
                    ->references('id')
                    ->on('pe3_persyaratan')
                    ->onDelete('cascade') 
                    ->onUpdate('cascade'); 

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
        Schema::dropIfExists('pe3_pmb_persyaratan');
    }
}
