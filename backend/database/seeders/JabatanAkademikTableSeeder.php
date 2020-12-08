<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JabatanAkademikTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_jabatan_akademik');

        \DB::table('pe3_jabatan_akademik')->insert([
            'id_jabatan'=>1,
            'nama_jabatan'=>'STAF PENGAJAR',
        ]);
        \DB::table('pe3_jabatan_akademik')->insert([
            'id_jabatan'=>2,
            'nama_jabatan'=>'ASISTEN AHLI',
        ]);
        \DB::table('pe3_jabatan_akademik')->insert([
            'id_jabatan'=>3,
            'nama_jabatan'=>'LEKTOR',
        ]);
        \DB::table('pe3_jabatan_akademik')->insert([
            'id_jabatan'=>4,
            'nama_jabatan'=>'LEKTOR KEPALA',
        ]);
        \DB::table('pe3_jabatan_akademik')->insert([
            'id_jabatan'=>5,
            'nama_jabatan'=>'GURU BESAR',
        ]);        
    }
}
