<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KecamatanModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'wilayah_kecamatan';
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
        'id', 'kabupaten_id','nama'
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

    public function desa ()
    {
        return $this->hasMany('App\Models\DMaster\DesaModel','kecamatan_id','id');
    }

}