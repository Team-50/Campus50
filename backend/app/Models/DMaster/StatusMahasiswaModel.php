<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class StatusMahasiswaModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_status_mahasiswa';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'k_status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'k_status',                      
        'n_status',            
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