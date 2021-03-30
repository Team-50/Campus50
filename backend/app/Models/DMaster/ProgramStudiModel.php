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
        'id', 
        'kode_forlap', 
        'nama_prodi', 
        'nama_prodi_alias', 
        'konsentrasi',
        'kode_fakultas',
        'kode_jenjang',
        'nama_jenjang',
        'config'
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
    public $timestamps = true;

    public function getKAProdi($prodi_id)
    {
        $prodi=ProgramStudiModel::find($prodi_id);
        if (is_null($prodi))
        {
            return null;
        }
        else
        {
            $config=json_decode($prodi->config);            
            return $config->kaprodi;
        }        
    }
}