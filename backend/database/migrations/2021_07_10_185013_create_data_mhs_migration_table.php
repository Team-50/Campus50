<?php
/**
 * tabel ini digunakan untuk menyimpan daftar mahasiswa yang telah dimigrasikan datanya
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataMhsMigrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_data_mhs_migration', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('nim');
            $table->string('nama_mhs');
            $table->string('aktivitas');
            $table->year('tahun');
            $table->tinyinteger('idsmt');
            $table->timestamps();              
            
            $table->index('user_id');             
            $table->index('nim');
            $table->index('tahun');

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
        Schema::dropIfExists('pe3_data_mhs_migration');
    }
}
