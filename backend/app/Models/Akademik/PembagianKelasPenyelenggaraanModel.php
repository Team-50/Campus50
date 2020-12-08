<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;


class PembagianKelasPenyelenggaraanModel extends Model 
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_kelas_mhs_penyelenggaraan';
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
        'penyelenggaraan_dosen_id',
        'kelas_mhs_id',        
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

