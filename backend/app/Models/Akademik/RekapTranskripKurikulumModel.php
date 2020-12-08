<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class RekapTranskripKurikulumModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_rekap_transkrip_kurikulum';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [                
        'user_id',
        'jumlah_matkul',
        'jumlah_sks',
        'jumlah_am', 
        'jumlah_m', 
        'ipk',   
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
}