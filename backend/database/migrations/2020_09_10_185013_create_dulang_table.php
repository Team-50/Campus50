<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDulangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_dulang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('nim');
            $table->year('tahun');
            $table->tinyinteger('idsmt');
            $table->smallinteger('tasmt');
            $table->char('idkelas',1); 
            $table->char('status_sebelumnya',1); 
            $table->char('k_status',1); 

            $table->timestamps();  
            
            $table->index('user_id');   
            $table->index('nim');
            $table->index('tahun');
            $table->index('idsmt');
            $table->index('idkelas');
            $table->index('k_status');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('pe3_register_mahasiswa')
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
        Schema::dropIfExists('pe3_dulang');
    }
}
