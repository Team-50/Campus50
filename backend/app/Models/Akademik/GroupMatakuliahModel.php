<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class GroupMatakuliahModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_group_matakuliah';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_group';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_group',
        'nama_group',        
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