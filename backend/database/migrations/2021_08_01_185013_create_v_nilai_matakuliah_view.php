<?php
/**
 * tabel ini digunakan untuk menyimpan daftar mahasiswa yang telah dimigrasikan datanya
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVNilaiMatakuliahView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE VIEW v_nilai AS
            SELECT
                C.user_id,
                B.krs_id,
                A.krsmatkul_id,
                B.penyelenggaraan_id,
                D.matkul_id,
                E.user_id AS user_id_penyelenggaraan,
                E.nama_dosen AS nama_dosen_penyelenggaraan,
                J.user_id AS user_id_kelas,
                J.nama_dosen AS nama_dosen_kelas,
                C.nim,
                D.kmatkul,
                D.nmatkul,
                D.sks,
                D.semester,
                A.n_kuan,
                A.n_kual,
                A.n_mutu,
                A.telah_isi_kuesioner,	
                B.tahun,
                B.idsmt,
                A.created_at,
                A.updated_at
            FROM
                pe3_nilai_matakuliah A
            JOIN
                pe3_krsmatkul B ON A.krsmatkul_id=B.id
            JOIN
                pe3_krs C ON B.krs_id=C.id
            JOIN
                pe3_penyelenggaraan D ON A.penyelenggaraan_id=D.id
            LEFT JOIN 
                pe3_dosen E ON E.user_id=D.user_id                                        
            LEFT JOIN
                pe3_kelas_mhs_peserta F ON F.krsmatkul_id=A.krsmatkul_id
            LEFT JOIN
                pe3_kelas_mhs G ON G.id=F.kelas_mhs_id  
            LEFT JOIN
                pe3_kelas_mhs_penyelenggaraan H ON H.kelas_mhs_id=F.kelas_mhs_id
            LEFT JOIN 
                pe3_penyelenggaraan_dosen I ON I.id=H.penyelenggaraan_dosen_id
            LEFT JOIN
                pe3_dosen J ON J.user_id=I.user_id                                      
            WHERE
                B.batal=0
            ORDER BY
                D.semester ASC,
                D.kmatkul ASC
        ');	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v_nilai');
    }
}
