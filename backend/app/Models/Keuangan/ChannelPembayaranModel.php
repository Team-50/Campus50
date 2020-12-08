<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class ChannelPembayaranModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_channel_pembayaran';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_channel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'id_channel',                      
        'nama_channel',            
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