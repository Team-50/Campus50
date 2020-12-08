<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class KRSModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_krs';
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
        'dulang_id',
        'nim',

        'jumlah_matkul_1',               
        'jumlah_sks_1',               
        'jumlah_am_1',               
        'jumlah_m_1',                           
        
        'jumlah_matkul_2',               
        'jumlah_sks_2',               
        'jumlah_am_2',               
        'jumlah_m_2',                           

        'ipk',
        'ips',
        
        'kjur', 
        'idsmt', 
        'tahun', 
        'tasmt',         
        'sah',        
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
    
    public function dulang()
    {
        return $this->belongsTo('App\Models\Akademik\DulangModel','dulang_id','id');
    }
    public function mahasiswa()
    {
        return $this->belongsTo('App\Models\SPMB\FormulirPendaftaranModel','user_id','user_id');
    }
}