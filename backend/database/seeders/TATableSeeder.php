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
        
        \DB::table('pe3_ta')->insert([
            'tahun'=>date('Y'),
            'tahun_akademik'=>date('Y').'/'.(date('Y')+1),            
            'awal_semester'=>9,            
        ]);        

    }
}
