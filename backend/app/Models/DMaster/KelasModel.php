<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KelasModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_kelas';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'idkelas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idkelas', 'nkelas'
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