<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;


class PenyelenggaraanMatakuliahModel extends Model 
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_penyelenggaraan';
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
        'matkul_id',
        'user_id',
        'kmatkul',
        'nmatkul',
        'sks',
        'semester',
        'ta_matkul',
        'idsmt',
        'tahun',         
        'kjur'
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

