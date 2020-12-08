<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\System\ConfigurationModel;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [            
            'username'=>'required',
            'password'=>'required',                        
        ]);
        $credentials = $request->only('username', 'password');
        $credentials['active']=1;

        if (! $token = $this->guard()->attempt($credentials)) {
            return response()->json([
                                    'page' => 'login',
                                    'error' => 'Unauthorized',                                    
                                ], 401);
        }
        //log user loggin
        \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $this->guard()->user(), 
                                                        'object_id'=>$this->getUserid(), 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'user '.$credentials['username'].' berhasil login'
                                                    ]);

        ConfigurationModel::toCache();
        return $this->respondWithToken($token);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->guard()->user()->toArray();
        if ($this->hasRole('mahasiswabaru'))
        {
            $formulir = \App\Models\SPMB\FormulirPendaftaranModel::find($user['id']);
            $user['idsmt']=$formulir->idsmt;
        }
        $user['role']=$this->getRoleNames();
        $user['issuperadmin']=$this->hasRole('superadmin');
        $user['permissions']=$this->guard()->user()->permissions->pluck('id','name')->toArray();
        return response()->json($user);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        //log user logout
        \App\Models\System\ActivityLog::log($request,[
            'object' => $this->guard()->user(),             
            'object_id' => $this->getUserid(), 
            'user_id' => $this->getUserid(), 
            'message' => 'user '.$this->guard()->user()->username.' berhasil logout'
        ],1);

        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out'],200);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }
}