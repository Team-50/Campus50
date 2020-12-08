<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatusMahasiswaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_status_mahasiswa');

        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'A',
            'n_status'=>'AKTIF',
        ]);
        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'C',
            'n_status'=>'CUTI',
        ]);
        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'D',
            'n_status'=>'DROP-OUT / PUTUS',
        ]);
        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'K',
            'n_status'=>'KELUAR',
        ]);
        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'L',
            'n_status'=>'LULUS',
        ]);
        \DB::table('pe3_status_mahasiswa')->insert([
            'k_status'=>'N',
            'n_status'=>'NON-AKTIF',
        ]);
    }
}
