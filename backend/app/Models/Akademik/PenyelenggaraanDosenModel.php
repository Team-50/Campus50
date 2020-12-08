<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;


class PenyelenggaraanDosenModel extends Model 
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_penyelenggaraan_dosen';
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
        'penyelenggaraan_id',
        'user_id',
        'is_ketua',        
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
    
    public function penyelenggaraan()
    {
        return $this->belongsTo('App\Models\Akademik\PenyelenggaraanMatakuliahModel','penyelenggaraan_id','id');
    }
}

