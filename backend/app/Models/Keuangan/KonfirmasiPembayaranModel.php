<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class KonfirmasiPembayaranModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_konfirmasi_pembayaran';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'transaksi_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'transaksi_id',                 
        'user_id',            
        'no_transaksi',        
        'id_channel',        
        'total_bayar',
        'nomor_rekening_pengirim',
        'nama_rekening_pengirim',
        'nama_bank_pengirim',
        'desc',
        'tanggal_bayar',
        'bukti_bayar',        
        'verified',        
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

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    public function formulir()
    {
        return $this->belongsTo('App\Models\SPMB\FormulirPendaftaranModel','user_id','user_id');
    }
    public function transaksi()
    {
        return $this->belongsTo('App\Models\Keuangan\TransaksiModel','transaksi_id','id');
    }
}