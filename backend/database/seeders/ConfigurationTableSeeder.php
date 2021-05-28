<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;
use App\Models\System\ConfigurationModel;
class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM pe3_configuration');
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"101",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_PT',
            'config_value'=>'NAMA PT',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);        

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"102",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_PT_ALIAS',
            'config_value'=>'NAMA PT ALIAS',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"103",
            'config_group'=>'identitas',
            'config_key'=>'BENTUK_PT',
            'config_value'=>'sekolahtinggi',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"104",
            'config_group'=>'identitas',
            'config_key'=>'KODE_PT',
            'config_value'=>'0',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"105",
            'config_group'=>'identitas',
            'config_key'=>'LOGO',
            'config_value'=>'{realpath:"",relativepath:""}',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"201",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_TA',
            'config_value'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"202",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_SEMESTER',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"203",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_TAHUN_PENDAFTARAN',
            'config_value'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"204",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_SEMESTER_PENDAFTARAN',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"205",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_FAKULTAS',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"206",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_PRODI',
            'config_value'=>12,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
			'config_id'=>"207",
			'config_group'=>'basic',
			'config_key'=>'DEFAULT_TTD_SK_KELULUSAN',
			'config_value'=>'{"nama":"Tim Campus50","nidn":"XX","nipy":"XX"}',
			'created_at'=>Carbon::now(),
			'updated_at'=>Carbon::now()
		]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"701",
            'config_group'=>'report',
            'config_key'=>'HEADER_1',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"702",
            'config_group'=>'report',
            'config_key'=>'HEADER_2',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"703",
            'config_group'=>'report',
            'config_key'=>'HEADER_3',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"704",
            'config_group'=>'report',
            'config_key'=>'HEADER_4',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"705",
            'config_group'=>'report',
            'config_key'=>'HEADER_ADDRESS',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        // theme
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"801",
            'config_group'=>'theme',
            'config_key'=>'V_SYSTEM_BAR_CSS_CLASS',
            'config_value'=>'green lighten-2 white--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"802",
            'config_group'=>'theme',
            'config_key'=>'V_APP_BAR_NAV_ICON_CSS_CLASS',
            'config_value'=>'grey--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"803",
            'config_group'=>'theme',
            'config_key'=>'V_NAVIGATION_DRAWER_CSS_CLASS',
            'config_value'=>'accent-2',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"808",
            'config_group'=>'theme',
            'config_key'=>'V_NAVIGATION_DRAWER_COLOR',
            'config_value'=>'#1A237E',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"804",
            'config_group'=>'theme',
            'config_key'=>'V_LIST_ITEM_BOARD_CSS_CLASS',
            'config_value'=>'warning',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"805",
            'config_group'=>'theme',
            'config_key'=>'V_LIST_ITEM_BOARD_COLOR',
            'config_value'=>'white',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"806",
            'config_group'=>'theme',
            'config_key'=>'V_LIST_ITEM_ACTIVE_CSS_CLASS',
            'config_value'=>'green darken-1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"807",
            'config_group'=>'theme',
            'config_key'=>'COLOR_DASHBOARD',
            'config_value'=>json_encode([
                'dmaster'=>'#2ed573',
                'perencanaan'=>'#2ed573',
                'spmb'=>'#1e90ff',
                'keuangan'=>'#3742fa',
                'akademik'=>'#a4b0be',
                'kemahasiswaan'=>'#ff7f50',
                'elearning'=>'#ffa502',
                'user_sistem'=>'#ff6348',
                'konfigurasi_sistem'=>'#ff4757',
                'migrasi_sistem'=>'#747d8c'
            ]),
            
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        //server
        \DB::table('pe3_configuration')->insert([
            'config_id'=>"910",
            'config_group'=>'server',
            'config_key'=>'EMAIL_MHS_ISVALID',
            'config_value'=>'1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"901",
            'config_group'=>'server',
            'config_key'=>'CAPTCHA_SITE_KEY',
            'config_value'=>'$',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('pe3_configuration')->insert([
            'config_id'=>"902",
            'config_group'=>'server',
            'config_key'=>'CAPTCHA_PRIVATE_KEY',
            'config_value'=>'$',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        ConfigurationModel::toCache();
    }
}
