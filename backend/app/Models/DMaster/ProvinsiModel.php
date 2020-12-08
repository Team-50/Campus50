<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class ProvinsiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'wilayah_provinsi';
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
        'id', 'nama'
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

    public function kabupaten()
    {
        return $this->hasMany('App\Models\DMaster\KabupatenModel','provinsi_id','id');
    }
}