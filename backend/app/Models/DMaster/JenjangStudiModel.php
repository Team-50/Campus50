<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class JenjangStudiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_jenjang_studi';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'kode_jenjang';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_jenjang', 'nama_jenjang'
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