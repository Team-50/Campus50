<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class KRSMatkulModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_krsmatkul';
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
        'krs_id',
        'nim',
        'penyelenggaraan_id',
        'batal',           
        'kjur',           
        'idsmt',           
        'tahun',           
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
    
    public function krs()
    {
        return $this->belongsTo('App\Models\Akademik\KRSModel','krs_id','id');
    }
}