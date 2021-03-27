<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProgramStudiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //kode pt 213452
        \DB::statement('DELETE FROM pe3_prodi');
        \DB::statement('ALTER TABLE pe3_prodi AUTO_INCREMENT = 1;');

        \DB::table('pe3_prodi')->insert([      
            'id'=>12,      
            'kode_forlap'=>'55201',
            'nama_prodi'=>'Teknik Informatika',
            'nama_prodi_alias'=>'IF',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);              

        \DB::table('pe3_prodi')->insert([   
            'id'=>32,         
            'kode_forlap'=>'57201', //sesuaikan dengan nama kode prodi di FORLAP
            'nama_prodi'=>'Sistem Informasi',
            'nama_prodi_alias'=>'SI',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]); 

        \DB::table('pe3_prodi')->insert([   
            'id'=>42,         
            'kode_forlap'=>'57201', //sesuaikan dengan nama kode prodi di FORLAP
            'nama_prodi'=>'Sistem Informasi',
            'nama_prodi_alias'=>'SI',
            'konsentrasi'=>'KOMPUTER AKUNTANSI',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);                      
    }
}
