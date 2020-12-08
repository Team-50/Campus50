<?php

namespace App\Models\SPMB;

use Illuminate\Database\Eloquent\Model;

class PesertaUjianPMBModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_peserta_ujian_pmb';
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
        'no_peserta',                   
        'jadwal_ujian_id',            
        'mulai_ujian', 
        'selesai_ujian',         
        'sisa_waktu',         
        'isfinish',                 
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

    public function jadwalujian ()
    {
        return $this->belongsTo('App\Models\SPMB\JadwalUjianPMBModel','jadwal_ujian_id','id');
    }
}