<?php

namespace App\Models\Surat;

use Illuminate\Database\Eloquent\Model;

class SuratKeluarModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_surat_keluar';
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
        'user_id_created',                      
        'nama_user_created',    
        'user_id_ttd', 
        'nama_user_ttd', 
        'nomor_surat', 
        'no_urut',
        'bulan_surat',         
        'tahun_surat',
        'perihal',
        'kepada',
        'user_id_kepada',   
        'isi_surat', 
        'keterangan',
        'tanggal_surat', 
        'qr_code',
        'path_scanan', 
        'klasifikasi_surat',
        'ta'
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