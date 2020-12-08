<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersprodiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usersprodi', function (Blueprint $table) {
            $table->increments('id');            
            $table->uuid('user_id');                                  
            $table->unsignedInteger('prodi_id');  
            $table->string('kode_prodi',5); 
            $table->string('nama_prodi',50);
            $table->string('nama_prodi_alias',50);
            $table->string('kode_jenjang',1);
            $table->string('nama_jenjang',15);
            $table->boolean('locked')->default(0); 
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('prodi_id');  

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->foreign('prodi_id')
                    ->references('id')
                    ->on('pe3_prodi')
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
        Schema::dropIfExists('usersprodi');
    }
}