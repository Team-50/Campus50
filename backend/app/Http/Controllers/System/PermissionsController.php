<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller {      
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $this->hasPermissionTo('SYSTEM-SETTING-PERMISSIONS_BROWSE');
        $user=$this->guard()->user();
        if ($user->hasRole('superadmin'))
        {
            $data = Permission::orderBy('name','ASC')
                                ->get();
        }
        else if ($user->hasRole('akademik'))
        {
            $data = Role::findByName('akademik')->permissions;
        }
        else if ($user->hasRole('pmb'))
        {
            $data = Role::findByName('pmb')->permissions;
        }
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'permissions'=>$data,
                                'message'=>'Fetch data permissions berhasil diperoleh'
                            ],200);              
    }    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-PERMISSIONS_STORE');

        $this->validate($request, [
            'name'=>[
                        'required',
                        function ($attribute,$value,$fail) {
                            if(Permission::where('name','like',"%$value%")->exists())
                            {
                                $fail('Nama Permission telah tersedia, mohon ganti dengan yang lain');
                            }
                        }
                    ]
        ],[
            'name.required'=>'Nama permission mohon untuk di isi',
        ]
        );        
        $permission = new Permission;        
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $nama = strtoupper($request->input('name'));   
        
        $permission->insert([
            ['name'=>"{$nama}_BROWSE",'guard_name'=>'api','created_at'=>$now, 'updated_at'=>$now],
            ['name'=>"{$nama}_SHOW",'guard_name'=>'api','created_at'=>$now, 'updated_at'=>$now],
            ['name'=>"{$nama}_STORE",'guard_name'=>'api','created_at'=>$now, 'updated_at'=>$now],
            ['name'=>"{$nama}_UPDATE",'guard_name'=>'api','created_at'=>$now, 'updated_at'=>$now],
            ['name'=>"{$nama}_DESTROY",'guard_name'=>'api','created_at'=>$now, 'updated_at'=>$now],
        ]);
        
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    // 'permission'=>$permission,                                    
                                    'message'=>'Data permission berhasil disimpan.'
                                ],200); 
    
    }
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-PERMISSIONS_DESTROY');

        $permission = Permission::find($id);
        if (is_null($permission))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Permission dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $nama_permission = $permission->name;            
            \DB::table('permissions')->where('id',$id)->delete();

            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $permission, 
                                                        'object_id'=>$permission->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Menghapus permission ('.$nama_permission.') berhasil'
                                                    ]);                                                                 
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Permission ($nama_permission) berhasil dihapus"
                                    ],200); 
        }
    }
}