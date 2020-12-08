<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class MatakuliahModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_matakuliah';
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
        'id_group',
        'nama_group',
        'group_alias',
        'kmatkul', 
        'nmatkul', 
        'sks', 
        'idkonsentrasi',
        'ispilihan',
        'islintas_prodi',
        'semester',
        'sks_tatap_muka',
        'sks_praktikum',
        'sks_praktik_lapangan',
        'minimal_nilai',
        'syarat_skripsi',
        'status',
        'ta',
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