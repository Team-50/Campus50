<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class FakultasModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_fakultas';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'kode_fakultas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_fakultas', 'nama_fakultas'
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

    public function programstudi()
    {
        return $this->hasMany('App\Models\DMaster\ProgramStudiModel','kode_fakultas','kode_fakultas')
                    ->select(\DB::raw('id,kode_prodi,nama_prodi,CONCAT(nama_prodi,\' (\',nama_jenjang,\')\') AS nama_prodi2,kode_jenjang,nama_jenjang'));
    }
}