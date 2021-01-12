<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesController extends Controller {    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_DESTROY');
        $data = Role::all();
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'roles'=>$data,
                                'message'=>'Fetch data stores berhasil diperoleh'
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
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_STORE');
        $this->validate($request, [
            'name'=>'required|unique:roles',
        ],[
            'name.required'=>'Nama role mohon untuk di isi',
            'name.unique'=>'Nama role telah ada, mohon untuk diganti dengan yang lain'
        ]
        );
        
        $role = new Role;
        $role->name = $request->input('name');
        $role->save();
       
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'role'=>$role,                                    
                                    'message'=>'Data role berhasil disimpan.'
                                ],200); 

    }
    /**
     * Store a roles resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storerolepermissions(Request $request)
    {      
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_STORE');

        $post = $request->all();
        $permissions = isset($post['chkpermission'])?$post['chkpermission']:[];
        $role_id = $post['role_id'];

        foreach($permissions as $k=>$v)
        {
            $records[]=$v['name'];
        }        
        
        $role = Role::find($role_id);
        $role->syncPermissions($records);
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'message'=>'Permission role '.$role->name.' berhasil disimpan.'
                                ],200); 
    }
    /**
     * Store user permissions resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revokerolepermissions(Request $request)
    {      
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_DESTROY');

        $role = \DB::transaction(function () use ($request) {
            $post = $request->all();
            $name = $post['name'];
            $role_id = $post['role_id'];        
            
            $role = Role::find($role_id);
            $role->revokePermissionTo($name);

            \App\Models\System\ActivityLog::log($request,[
                'object' => $this->guard()->user(), 
                'object_id' => $this->guard()->user()->id, 
                'user_id' => $this->getUserid(), 
                'message' => 'Menghilangkan permission('.$name.') role ('.$role->name.') berhasil'
            ]);

            return $role;
        });
       
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',
                                    'message'=>'Role '.$role->name.' berhasil di revoke.'
                                ],200); 
    }
    /**
     * Display the specified role permissions by id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rolepermissions($id)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_SHOW');
        $role=Role::findByID($id);
        if (is_null($role))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Role ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',
                                        'permissions'=>$role->permissions,                                    
                                        'message'=>'Fetch permission role '.$role->name.' berhasil diperoleh.'
                                    ],200); 
        }
    }    
    /**
     * Display the specified role permissions by name.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rolepermissionsbyname($id)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_SHOW');
        $role=Role::findByName($id);
        if (is_null($role))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Role ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',
                                        'permissions'=>$role->permissions,                                    
                                        'message'=>'Fetch permission role '.$role->name.' berhasil diperoleh.'
                                    ],200); 
        }
    }    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-ROLES_UPDATE');

        $role = Role::find($id);

        $this->validate($request, [                                
            'name'=>[
                'required',
                Rule::unique('roles')->ignore($role->name,'name')
            ],                     
        ],[
            'name.required'=>'Nama role mohon untuk di isi',
        ]);        
       
        $role->name = $request->input('name');
        $role->save();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'role'=>$role,                                    
                                    'message'=>'Data role '.$role->name.' berhasil diubah.'
                                ],200); 
    }
}