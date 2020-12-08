<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiayaKomponenPeriodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_kombi_periode', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->smallInteger('kombi_id');
            $table->string('nama_kombi');
            $table->enum('periode',['insidental','persemester','perbulan','sekali']);
            $table->char('idkelas',1);
            $table->unsignedInteger('kjur')->nullable();
            $table->year('tahun');
            $table->decimal('biaya',15,2);
            $table->timestamps();

            $table->index('kombi_id');
            $table->index('kjur');

            $table->foreign('kombi_id')
                ->references('id')
                ->on('pe3_kombi')
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
        Schema::dropIfExists('pe3_kombi_periode');
    }
}
