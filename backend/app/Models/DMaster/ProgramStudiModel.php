<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class ProgramStudiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_prodi';
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
        'id','kode_prodi', 'nama_prodi', 'nama_prodi_alias', 'kode_fakultas','kode_jenjang','nama_jenjang','config'
    ];
    /**
     * enable auto_increment.
     *
     * @var string
     */
    public $incrementing = true;
    /**
     * activated timestamps.
     *
     * @var string
     */
    public $timestamps = false;
}