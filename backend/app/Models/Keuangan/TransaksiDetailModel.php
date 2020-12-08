<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetailModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_transaksi_detail';
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
        'transaksi_id',    
        'no_transaksi',    
        'kombi_id',
        'nama_kombi',
        'biaya',
        'promocode',
        'promotype',
        'promovalue',
        'jumlah',
        // dimanfaatkan untuk pembayaran per bulan
        'bulan',
        'sub_total',        
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

    public function transaksi ()
    {
        return $this->belongsTo('\App\Models\Keuangan\TransaksiModel','transaksi_id','id');
    }
}