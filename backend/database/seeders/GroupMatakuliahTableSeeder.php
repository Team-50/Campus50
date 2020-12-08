<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GroupMatakuliahTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_group_matakuliah');
        
        \DB::table('pe3_group_matakuliah')->insert([
            'id_group'=>1,
            'nama_group'=>'MATAKULIAH PENGEMBANGAN KEAHLIAN',
            'group_alias'=>'MPK',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);  

        \DB::table('pe3_group_matakuliah')->insert([
            'id_group'=>2,
            'nama_group'=>'MATAKULIAH KEAHLIAN KHUSUS',
            'group_alias'=>'MKK',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);  
        
        \DB::table('pe3_group_matakuliah')->insert([
            'id_group'=>3,
            'nama_group'=>'MATAKULIAH KEAHLIAN BERKARYA',
            'group_alias'=>'MKB',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);  

        \DB::table('pe3_group_matakuliah')->insert([
            'id_group'=>4,
            'nama_group'=>'MATAKULIAH PERILAKU BERAGAM',
            'group_alias'=>'MPB',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);  

        \DB::table('pe3_group_matakuliah')->insert([
            'id_group'=>5,
            'nama_group'=>'MATAKULIAH BERKEHIDUPAN BERMASYARAKATA',
            'group_alias'=>'MBB',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);  
        
    }
}
