<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('pe3_transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->uuid('user_id');    
            $table->string('no_transaksi',20)->unique(); 
            $table->string('no_faktur')->nullable(); 
            $table->unsignedInteger('kjur');
            $table->year('ta');
            $table->tinyInteger('idsmt');
            $table->char('idkelas',1);

            $table->string('no_formulir',11)->nullable();
            $table->string('nim')->nullable(); 
            $table->tinyInteger('status')->default(0);   
            $table->decimal('total',15,2)->default(0);
            $table->date('tanggal'); 
            $table->string('desc')->nullable();
            $table->timestamps();      

            $table->index('kjur');            
            $table->index('ta');
            $table->index('idsmt');
            $table->index('idkelas');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 
        });

        Schema::create('pe3_transaksi_detail', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->uuid('user_id');               
            $table->uuid('transaksi_id');    
            $table->string('no_transaksi',20);  
            $table->smallInteger('kombi_id'); 
            $table->string('nama_kombi');  
            $table->decimal('biaya',15,2);                                                            
            $table->string('promocode')->nullable();
            $table->string('promotype')->nullable();
            $table->decimal('promovalue',15,2)->default(0);
            $table->smallInteger('jumlah')->default(0);                                                            
            $table->tinyInteger('bulan')->nullable();                                                            
            $table->year('tahun')->nullable();                                                            
            $table->decimal('sub_total',15,2)->default(0);                                                            
            
            $table->timestamps();      

            $table->index('no_transaksi');
            $table->index('kombi_id');
            $table->index('user_id');
            $table->index('transaksi_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade') 
                ->onUpdate('cascade'); 

            $table->foreign('transaksi_id')
                ->references('id')
                ->on('pe3_transaksi')
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
        Schema::dropIfExists('pe3_transaksi_detail');
        Schema::dropIfExists('pe3_transaksi');
    }
}
