<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class TAModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_ta';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'tahun';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tahun', 'tahun_akademik', 'awal_semester'
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