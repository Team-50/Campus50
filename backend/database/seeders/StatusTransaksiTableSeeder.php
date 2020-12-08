<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class StatusTransaksiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_status_transaksi');

        \DB::table('pe3_status_transaksi')->insert([
            'id_status'=>0,
            'nama_status'=>'UNPAID',
            'style'=>'yellow darken-3',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_status_transaksi')->insert([
            'id_status'=>1,
            'nama_status'=>'PAID',
            'style'=>'light-green lighten-1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_status_transaksi')->insert([
            'id_status'=>2,
            'nama_status'=>'CANCELLED',
            'style'=>'red darken-2',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
    }
}
