<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;


/**
 * Class ActivityLog
 *
 * @author Danyal <mig@danyal.dk>
 * @package DANYALDK\ActivityLog
 */
class ActivityLog extends Model
{

    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'ip_address', 'method', 'endpoint', 'status_code', 'message', 'object', 'object_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * Save log
     *
     * @param Request $request Request.
     * @param array   $data    Data to save.
     *
     * @return void
     */
    public static function log(Request $request, array $data = [], $status_code=0)
    {
        $user_id = 0;
        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $message = isset($data['message']) ? $data['message'] : 'Some action is performed';

        self::create([
            'user_id' => $user_id,
            'object' => get_class($data['object']),
            'object_id' => $data['object_id'],
            'method' => $request->getMethod(),
            'endpoint' => $request->getUri(),
            'status_code' => $status_code,
            'message' => $message,
            'ip_address' => $request->ip(),

        ]);
    }
}