<?php

namespace App\Models\SPMB;

use Illuminate\Database\Eloquent\Model;

class FormulirPendaftaranModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_formulir_pendaftaran';
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
        'no_formulir',    
        'nama_mhs', 
        'tempat_lahir', 
        'tanggal_lahir', 
        'jk',
        'idagama', 
        
        'idwarga',
        'nik',
        'status_pekerjaan',
        'alamat_kantor',             
        'telp_kantor', 

        'address1_desa_id',
        'address1_kelurahan', 
        'address1_kecamatan_id',
        'address1_kecamatan', 
        'address1_kabupaten_id',
        'address1_kabupaten', 
        'address1_provinsi_id',
        'address1_provinsi', 
        'alamat_rumah', 
        
        'telp_rumah', 
        'telp_hp', 

        'nama_ibu_kandung',
        'id_jenispekerjaan_ortu', 

        'pendidikan_terakhir',
        'jurusan', 
        'address2_kabupaten_id',
        'address2_kota', 
        'address2_provinsi_id',
        'address2_provinsi', 
        'tahun_pa',
        'jenis_slta',
        'asal_slta',
        'status_slta',
        'nomor_ijazah',

        'kjur1', 
        'kjur2', 
        'idkelas',
        'ta',
        'idsmt',
        'descr',
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