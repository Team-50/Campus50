<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class JabatanAkademikModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_jabatan_akademik';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_jabatan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'id_jabatan',                      
        'nama_jabatan',            
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
    public $timestamps = false;
}