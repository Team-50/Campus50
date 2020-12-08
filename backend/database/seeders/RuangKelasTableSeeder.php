<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class RuangKelasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::statement('DELETE FROM pe3_ruangkelas');

        \DB::table('pe3_ruangkelas')->insert([
            'id'=>Uuid::uuid4()->toString(),
            'namaruang'=>'ujianonline',
            'kapasitas'=>0,            
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);   
    }
}
