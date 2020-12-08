<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterMahasiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_register_mahasiswa', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->uuid('dosen_id')->nullable();
            $table->uuid('konsentrasi_id')->nullable(); 

            $table->string('nim')->unique();
            $table->string('nirm')->unique();
            $table->year('tahun');
            $table->tinyinteger('idsmt');
            $table->unsignedInteger('kjur');   
            $table->char('k_status',1);          
            $table->char('idkelas',1);            

            $table->timestamps();  

            $table->index('user_id');
            $table->index('dosen_id');
            $table->index('konsentrasi_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('dosen_id')
                ->references('user_id')
                ->on('pe3_dosen')
                ->onDelete('set null')
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
        Schema::dropIfExists('pe3_register_mahasiswa');
    }
}
