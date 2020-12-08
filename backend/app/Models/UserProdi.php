<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProdi extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'usersprodi';
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
        'prodi_id',
        'kode_prodi',
        'nama_prodi',
        'nama_prodi_alias',
        'kode_jenjang',
        'nama_jenjang',    
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
}