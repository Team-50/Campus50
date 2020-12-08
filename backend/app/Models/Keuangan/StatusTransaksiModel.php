<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class StatusTransaksiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_status_transaksi';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'id_status',                      
        'nama_status',    
        'style',
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