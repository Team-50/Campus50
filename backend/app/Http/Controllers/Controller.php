<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

class Controller extends BaseController
{
    /**
     * @return object auth api
     */
    public function guard() 
    {
        return Auth::guard('api');
    }
    /**
     * digunakan untuk mendapatkan userid
     */
    public function getUserid ()
    {
        return $this->guard()->user()->id;
    }
    /**
     * digunakan untuk mendapatkan username
     */
    public function getUsername ()
    {
        return $this->guard()->user()->username;
    }
    /**
     * @return boolean roles of user in array
     */
    public function getRoleNames() 
    {
        return $this->guard()->user()->getRoleNames()->toArray();
    }
    /**
     * @return boolean has role
     */
    public function hasRole($name) 
    {
        return $this->guard()->user()->hasRole($name);        
    }
    /**
     * @return object auth api
     */
    public function hasPermissionTo($permission) 
    {
        $user = Auth::guard('api')->user();
        if ($this->guard()->guest())
        {
            return true;
        }
        elseif ($user->hasPermissionTo($permission) || $user->hasRole('superadmin'))
        {
            return true;
        }
        else
        {
            abort(403,'Forbidden: You have not a privilege to execute this process '.$permission);
        }        
    }
    /**
     * @return object auth api
     */
    public function hasAnyPermission($permission) 
    {
        $user = Auth::guard('api')->user();
        if ($this->guard()->guest())
        {
            return true;
        }
        elseif ($user->hasAnyPermission($permission) || $user->hasRole('superadmin'))
        {
            return true;
        }
        else
        {
            abort(403,'Forbidden: You have not a privilege to execute this process '.$permission);
        }        
    }
    /**
     * Display the specified user permissions.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userpermissions($id)
    {
        $user = User::find($id);
        $permissions=is_null($user)?[]:$user->permissions;     
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'permissions'=>$permissions,                                    
                                    'message'=>'Fetch permission role '.$user->username.' berhasil diperoleh.'
                                ],200); 
    }
}
