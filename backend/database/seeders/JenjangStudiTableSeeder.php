<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\System\ConfigurationModel;
class JenjangStudiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_jenjang_studi');
        
        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"A",
            'nama_jenjang'=>'S-3',
        ]);               
        
        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"B",
            'nama_jenjang'=>'S-2',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"C",
            'nama_jenjang'=>'S-1',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"D",
            'nama_jenjang'=>'D-IV',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"E",
            'nama_jenjang'=>'D-III',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"F",
            'nama_jenjang'=>'D-II',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"G",
            'nama_jenjang'=>'D-1',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"H",
            'nama_jenjang'=>'SP-1',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"I",
            'nama_jenjang'=>'SP-2',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"J",
            'nama_jenjang'=>'PROFESI',
        ]);               

        \DB::table('pe3_jenjang_studi')->insert([
            'kode_jenjang'=>"X",
            'nama_jenjang'=>'NON-AKADEMIK',
        ]);               
        
    }
}
