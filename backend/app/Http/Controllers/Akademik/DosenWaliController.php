<?php

namespace App\Http\Controllers\Akademik;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDosen;
use Spatie\Permission\Models\Role;
use Ramsey\Uuid\Uuid;

class DosenWaliController extends Controller {         
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {           
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN-WALI_BROWSE');
        $data = User::role('dosen')
                    ->select(\DB::raw('
                        users.id,
                        users.username,
                        users.name,
                        pe3_dosen.nidn,
                        pe3_dosen.nipy,
                        users.email,
                        users.nomor_hp,
                        users.foto,
                        pe3_dosen.is_dw,
                        users.created_at,
                        users.updated_at
                    '))
                    ->join('pe3_dosen','pe3_dosen.user_id','users.id')
                    ->where('pe3_dosen.is_dw',true)
                    ->orderBy('username','ASC')
                    ->get();       
                    
        $role = Role::findByName('dosen');
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'role'=>$role,
                                'users'=>$data,
                                'message'=>'Fetch data dosen wali berhasil diperoleh'
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
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN-WALI_STORE');
        
        $this->validate($request, [
            'name'=>'required',
            'nidn'=>'numeric|unique:pe3_dosen',
            'nipy'=>'required|string|unique:pe3_dosen',
            'email'=>'required|string|email|unique:users',
            'nomor_hp'=>'required|string|unique:users',
            'username'=>'required|string|unique:users',
            'password'=>'required',                        
        ]);
        $user = \DB::transaction(function () use ($request){

            $now = \Carbon\Carbon::now()->toDateTimeString();        
            $user=User::create([
                'id'=>Uuid::uuid4()->toString(),
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'nomor_hp'=>$request->input('nomor_hp'),
                'username'=> $request->input('username'),
                'password'=>Hash::make($request->input('password')),                        
                'theme'=>'default',
                'foto'=> 'storage/images/users/no_photo.png',
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);            
            $role='dosen';   
            $user->assignRole($role);               
            
            $permission=Role::findByName('dosen')->permissions;
            $permissions=$permission->pluck('name');
            $user->givePermissionTo($permissions);
            
            if ($request->input('is_dw')=='true')
            {
                $user->assignRole('dosenwali'); 
                $permission=Role::findByName('dosenwali')->permissions;
                $permissions=$permission->pluck('name');
                $user->givePermissionTo($permissions);
            }
            
            UserDosen::create([
                'user_id'=>$user->id,
                'nama_dosen'=>$request->input('name'),                                
                'nidn'=>$request->input('nidn'),                                
                'nipy'=>$request->input('nipy'),                                
                'is_dw'=>$request->input('is_dw'),                                
            ]);
            
            return $user;
        });

        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $this->guard()->user(), 
                                        'object_id' => $this->guard()->user()->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah dosen wali ('.$user->username.') berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'user'=>$user,                                    
                                    'message'=>'Data dosen wali berhasil disimpan.'
                                ],200); 

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
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN-WALI_UPDATE');

        $user = UserDosen::where('is_dw',true)
                            ->find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [           
                                        'pid'=>'required',            
                                        'user_id'=>'required|:users,email,'.$user->id,                                        
                                    ]);             
            $message='no pid';
            switch($request->input('pid'))
            {
                case 'alihkan_mhs' ://digunakan untuk mengalihkan mahasiswa dari dw ini ke dw lain
                    $dw_id=$request->input('user_id');

                    $message='Mengalihkan mahasiswa data dosen wali ('.$user->nama_dosen.') ini berhasil';
                    \App\Models\System\ActivityLog::log($request,[
                                                                    'object' => $this->guard()->user(), 
                                                                    'object_id' => $dw_id, 
                                                                    'user_id' => $this->getUserid(), 
                                                                    'message' => $message
                                                                ]);
                break;
            }

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'user'=>$user,      
                                    'message'=>$message,                                    
                                ],200); 
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN-WALI_DESTROY');

        $user = UserDosen::find($id); 
        
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Dosen wali dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $username=$user->username;
            $user->is_dw=false;
            $user->save();

            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $this->guard()->user(), 
                                                                'object_id' => $this->guard()->user()->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus dosen wali ('.$username.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Dosen Wali ($username) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}