<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class ChannelPembayaranTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_channel_pembayaran');
        
        \DB::table('pe3_channel_pembayaran')->insert([
            'id_channel'=>1,
            'nama_channel'=>'TELLER BANK',             
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()         
        ]);               

        \DB::table('pe3_channel_pembayaran')->insert([
            'id_channel'=>2,
            'nama_channel'=>'TRANSFER BANK',             
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()         
        ]);               

        \DB::table('pe3_channel_pembayaran')->insert([
            'id_channel'=>3,
            'nama_channel'=>'TRANSFER ATM',             
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()         
        ]);               

        \DB::table('pe3_channel_pembayaran')->insert([
            'id_channel'=>4,
            'nama_channel'=>'H2H',             
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()         
        ]);               
    }
}
