<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class RegisterMahasiswaModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_register_mahasiswa';
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
        'dosen_id',
        'konsentrasi_id',
        'nim', 
        'nirm', 
        'tahun', 
        'idsmt', 
        'kjur',
        'k_status',
        'idkelas',        
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