<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KomponenBiayaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::statement('DELETE FROM pe3_kombi'); 
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"101",
            'nama'=>'BIAYA PENDAFTARAN + BIAYA FORMULIR',
            'periode'=>'sekali',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          

        \DB::table('pe3_kombi')->insert([
            'id'=>"102",
            'nama'=>'BIAYA DAFTAR ULANG MHS. BARU',
            'periode'=>'sekali',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"201",
            'nama'=>'SPP',
            'periode'=>'perbulan',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);       

        \DB::table('pe3_kombi')->insert([
            'id'=>"202",
            'nama'=>'REGISTRASI KRS',
            'periode'=>'perbulan',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          

        \DB::table('pe3_kombi')->insert([
            'id'=>"301",
            'nama'=>'PROGRAM PENGALAMAN LAPANGAN / PROGRAM KERJA LAPANGAN',
            'periode'=>'insidental',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"401",
            'nama'=>'KULIAH KERJA NYATA',
            'periode'=>'insidental',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"501",
            'nama'=>'SEMINAR',
            'periode'=>'insidental',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"601",
            'nama'=>'UJIAN MUNAQASAH',
            'periode'=>'insidental',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
        
        \DB::table('pe3_kombi')->insert([
            'id'=>"701",
            'nama'=>'WISUDA',
            'periode'=>'sekali',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);          
    }
}
