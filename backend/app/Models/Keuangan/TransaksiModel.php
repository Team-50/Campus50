<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_transaksi';
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
        'user_id',    
        'no_transaksi',
        'no_faktur',
        'kjur',
        'ta',
        'idsmt',
        'idkelas',
        'no_formulir',
        'nim',
        'status',
        'total',
        'tanggal',
        'desc',
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

    public function detail ()
    {
        return $this->hasMany('\App\Models\Keuangan\TransaksiDetailModel','transaksi_id','id');
    }
}