<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KelasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::statement('DELETE FROM pe3_kelas'); 
        
        \DB::table('pe3_kelas')->insert([
            'idkelas'=>"A",
            'nkelas'=>'PAGI (S1)',
        ]);               

        \DB::table('pe3_kelas')->insert([
            'idkelas'=>"B",
            'nkelas'=>'SORE (S1)',
        ]);               

        \DB::table('pe3_kelas')->insert([
            'idkelas'=>"C",
            'nkelas'=>'MALAM (S1)',
        ]);               
    }
}
