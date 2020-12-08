<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ConfigurationTableSeeder::class);
        $this->call(TATableSeeder::class);
        $this->call(StatusMahasiswaTableSeeder::class);
        $this->call(JabatanAkademikTableSeeder::class);
        $this->call(RuangKelasTableSeeder::class);
        $this->call(KelasTableSeeder::class);
        $this->call(JenjangStudiTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PersyaratanTableSeeder::class);

        $this->call(StatusTransaksiTableSeeder::class);
        $this->call(ChannelPembayaranTableSeeder::class);
        $this->call(KomponenBiayaTableSeeder::class);
        $this->call(ProgramStudiTableSeeder::class); 
        
        $this->call(GroupMatakuliahTableSeeder::class); 
    }
}
