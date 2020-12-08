<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class PersyaratanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::statement('DELETE FROM pe3_persyaratan');

        \DB::table('pe3_persyaratan')->insert([
            'id'=>Uuid::uuid4()->toString(),
            'proses'=>'pmb',
            'nama_persyaratan'=>'Scan Pas Foto',
            'prodi_id'=>NULL,
            'ta'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);               
        \DB::table('pe3_persyaratan')->insert([
            'id'=>Uuid::uuid4()->toString(),
            'proses'=>'pmb',
            'nama_persyaratan'=>'Scan Ijazah Terakhir',
            'prodi_id'=>NULL,
            'ta'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);               
        \DB::table('pe3_persyaratan')->insert([
            'id'=>Uuid::uuid4()->toString(),
            'proses'=>'pmb',
            'nama_persyaratan'=>'Scan KTP',
            'prodi_id'=>NULL,
            'ta'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);               
        \DB::table('pe3_persyaratan')->insert([
            'id'=>Uuid::uuid4()->toString(),
            'proses'=>'pmb',
            'nama_persyaratan'=>'Scan Kartu Keluarga',
            'prodi_id'=>NULL,
            'ta'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);               

    }
}
