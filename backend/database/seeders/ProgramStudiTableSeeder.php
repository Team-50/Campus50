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
            'kode_prodi'=>'60202',
            'nama_prodi'=>'Ekonomi Syariah',
            'nama_prodi_alias'=>'ESY',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);              

        \DB::table('pe3_prodi')->insert([            
            'kode_prodi'=>'86208',
            'nama_prodi'=>'Pendidikan Agama Islam',
            'nama_prodi_alias'=>'PAI',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);              

        \DB::table('pe3_prodi')->insert([            
            'kode_prodi'=>'86232',
            'nama_prodi'=>'Pendidikan Guru Madrasah Ibtidaiyah',
            'nama_prodi_alias'=>'PGMI',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);      

        \DB::table('pe3_prodi')->insert([            
            'kode_prodi'=>'88203',
            'nama_prodi'=>'Tadris Bahasa Inggris',
            'nama_prodi_alias'=>'TBI',
            'kode_jenjang'=>'C',
            'nama_jenjang'=>'S-1',
        ]);              
        
        
    }
}
