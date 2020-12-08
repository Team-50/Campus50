<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapTranskripKurikulumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_rekap_transkrip_kurikulum', function (Blueprint $table) {

            $table->uuid('user_id');             

            $table->integer('jumlah_matkul')->default(0);               
            $table->integer('jumlah_sks')->default(0);               
            $table->integer('jumlah_am')->default(0);               
            $table->integer('jumlah_m')->default(0);                           
            $table->decimal('ipk',5,2)->default(0.00);                           
            $table->timestamps();  

            $table->primary('user_id');                

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
        Schema::dropIfExists('pe3_rekap_transkrip_kurikulum');
    }
}
