<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class TransferBankModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_transfer_bank';
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
        'nama_bank',    
        'nama_cabang',
        'nomor_rekening',
        'pemilik_rekening',
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