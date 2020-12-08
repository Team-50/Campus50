<?php

namespace App\Models\SPMB;

use Illuminate\Database\Eloquent\Model;

class JadwalUjianPMBModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_jadwal_ujian_pmb';
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
        'nama_kegiatan',    
        'jumlah_soal',    
        'tanggal_ujian', 
        'jam_mulai_ujian', 
        'jam_selesai_ujian', 
        'tanggal_akhir_daftar',         
        'durasi_ujian',         
        'ruangkelas_id',                         
        'ta',         
        'idsmt',         
        'status_pendaftaran',         
        'status_ujian',         
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