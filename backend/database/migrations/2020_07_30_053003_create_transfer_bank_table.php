<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_transfer_bank', function (Blueprint $table) {
            $table->uuid('id')->primary();      
            $table->string('nama_bank');            
            $table->string('nama_cabang');            
            $table->string('nomor_rekening');            
            $table->string('pemilik_rekening');            
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pe3_transfer_bank');
    }
}
