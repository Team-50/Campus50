<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class DulangModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_dulang';
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
        'tahun', 
        'idsmt', 
        'tasmt', 
        'idkelas', 
        'status_sebelumnya',
        'k_status',              
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
    
    public function register_mahasiswa()
    {
        return $this->belongsTo('App\Models\Akademik\RegisterMahasiswaModel','user_id','user_id');
    }
}