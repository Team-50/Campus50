<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelasMhsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('pe3_kelas_mhs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('zoom_id')->nullable();
            $table->string('kmatkul');
            $table->string('nmatkul');
            $table->tinyinteger('sks');
            $table->char('idkelas',1);
            $table->tinyinteger('hari');
            $table->string('jam_masuk',5);
            $table->string('jam_keluar',5);
            $table->uuid('ruang_kelas_id');

            $table->tinyinteger('persen_quiz')->default(0);
            $table->tinyinteger('persen_tugas_individu')->default(0);
            $table->tinyinteger('persen_tugas_kelompok')->default(0);
            $table->tinyinteger('persen_uts')->default(0);
            $table->tinyinteger('persen_uas')->default(0);
            $table->tinyinteger('persen_absen')->default(0);
            $table->boolean('isi_nilai')->default(false);

            $table->tinyinteger('idsmt');
            $table->year('tahun');

            $table->timestamps();

            $table->index('ruang_kelas_id');
            $table->index('idkelas');
            $table->index('user_id');
            $table->index('zoom_id');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('pe3_dosen')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('plugins_h2h_zoom')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

        });

        Schema::create('pe3_kelas_mhs_penyelenggaraan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penyelenggaraan_dosen_id');
            $table->uuid('kelas_mhs_id');
            $table->timestamps();

            $table->index('kelas_mhs_id');
            $table->index('penyelenggaraan_dosen_id');

            $table->foreign('kelas_mhs_id')
                ->references('id')
                ->on('pe3_kelas_mhs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('penyelenggaraan_dosen_id')
                ->references('id')
                ->on('pe3_penyelenggaraan_dosen')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });

        Schema::create('pe3_kelas_mhs_peserta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kelas_mhs_id');
            $table->uuid('krsmatkul_id');
            $table->timestamps();

            $table->index('kelas_mhs_id');
            $table->index('krsmatkul_id');

            $table->foreign('kelas_mhs_id')
                ->references('id')
                ->on('pe3_kelas_mhs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('krsmatkul_id')
                ->references('id')
                ->on('pe3_krsmatkul')
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
        Schema::dropIfExists('pe3_kelas_mhs_peserta');
        Schema::dropIfExists('pe3_kelas_mhs_penyelenggaraan');
        Schema::dropIfExists('pe3_kelas_mhs');
    }
}
