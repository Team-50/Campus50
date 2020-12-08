<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class BiayaKomponenPeriodeModel extends Model {
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_kombi_periode';
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
        'kombi_id',
        'nama_kombi',
        'idkelas',
        'kjur',
        'tahun',
        'biaya',
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
}
