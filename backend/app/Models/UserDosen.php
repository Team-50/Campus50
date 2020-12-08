<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDosen extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_dosen';
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
        'nidn',
        'nipy',
        'nama_dosen',
        'gelar_depan',
        'gelar_belakang',
        
        'tempat_lahir', 
        'tanggal_lahir', 
        'jk', 

        'address1_desa_id', 
        'address1_kelurahan', 
        'address1_kecamatan_id',
        'address1_kecamatan', 
        'address1_kabupaten_id', 
        'address1_kabupaten', 
        'address1_provinsi_id',
        'address1_provinsi', 
        'alamat_rumah', 
        
        'nik',
        'address2_desa_id', 
        'address2_kelurahan', 
        'address2_kecamatan_id',
        'address2_kecamatan', 
        'address2_kabupaten_id', 
        'address2_kabupaten', 
        'address2_provinsi_id', 
        'address2_provinsi', 
        'alamat_ktp', 

        'is_dw',
        'desc',
        'active',

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