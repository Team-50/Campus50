<?php

namespace App\Models\Plugins\H2H\ZoomAPI;

use Illuminate\Database\Eloquent\Model;

class ZoomModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'plugins_h2h_zoom';
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
        'email',
        'zoom_id',
        'api_key',
        'api_secret',
        'im_token',
        'jwt_token',

        'status',
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

}