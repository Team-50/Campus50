<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class TATableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_ta');
        $current_year=date('Y');

        \DB::table('pe3_ta')->insert([
            'tahun'=>$current_year,
            'tahun_akademik'=>$current_year.'/'.($current_year+1),            
            'awal_ganjil'=>"$current_year-09-01",            
            'akhir_ganjil'=>($current_year+1)."-02-28",            
            'awal_genap'=>($current_year+1)."-03-01",            
            'akhir_genap'=>($current_year+1)."-06-30",            
            'awal_pendek'=>($current_year+1)."-07-01",            
            'akhir_pendek'=>($current_year+1)."-08-30",            
        ]);        

    }
}