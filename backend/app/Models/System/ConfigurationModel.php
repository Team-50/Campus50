<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfigurationModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_configuration';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'config_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'config_group', 'config_group', 'config_key', 'config_value'
    ];
    /**
     * enable auto_increment.
     *
     * @var string
     */
    public $incrementing = false;
    /**
     * activated timestamps.
     *
     * @var string
     */
    public $timestamps = true;

    //digunakan untuk menyimpan ke cache
    public static function toCache()
    {
        $config = ConfigurationModel::all()->pluck('config_value','config_key'); 
        Cache::put('config', $config);
    }
    public static function getCache($idx=null)
    {
        if ($idx == null)
        {
            return Cache::get('config');
        }
        else
        {
            $config=Cache::get('config');
            return $config[$idx];
        }
    }
    //digunakan untuk menghapus cache
    public static function clear()
    {
        Cache::flush();
    }
}