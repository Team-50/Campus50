<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class DataMHSMigrationModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_data_mhs_migration';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'id',                      
        'user_id',    
        'nim', 
        'nama_mhs', 
        'aktivitas',         
        'tahun',         
        'idsmt',         
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

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}