<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDosen;
use Spatie\Permission\Models\Role;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class UsersDosenController extends Controller {         
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {           
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_BROWSE');
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
                        users.theme,
                        pe3_dosen.is_dw,
                        users.default_role,
                        users.created_at,
                        users.updated_at
                    '))
                    ->join('pe3_dosen','pe3_dosen.user_id','users.id')
                    ->orderBy('username','ASC')
                    ->get();                           
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'users'=>$data,
                                'message'=>'Fetch data users Dosen berhasil diperoleh'
                            ],200);  
    }  
    public function pengampu (Request $request)
    {
        $data = UserDosen::select(\DB::raw('
                                user_id,
                                nidn,
                                nama_dosen
                            '))
                            ->where('active',1)
                            ->orderBy('nama_dosen', 'ASC')
                            ->get();

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'dosen'=>$data,
                                'message'=>'Fetch data Dosen Pengampu berhasil diperoleh'
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
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_STORE');
        
        $this->validate($request, [
            'name'=>'required',
            'nidn'=>'numeric|unique:pe3_dosen',
            'nipy'=>'required|numeric|unique:pe3_dosen',
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
                'default_role'=>'dosen',            
                'foto'=> 'storage/images/users/no_photo.png',
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);            
            $role='dosen';   
            $user->assignRole($role);               
            
            $permission=Role::findByName('dosen')->permissions;
            $permissions=$permission->pluck('name');
            $user->givePermissionTo($permissions);
            
            if (filter_var($request->input('is_dw'),FILTER_VALIDATE_BOOLEAN))
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
                                        'message' => 'Menambah user Dosen ('.$user->username.') berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'user'=>$user,                                    
                                    'message'=>'Data user Dosen berhasil disimpan.'
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
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_UPDATE');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate"]
                                ],422); 
        }
        elseif ($user->default_role=='dosen')
        {
            $this->validate($request, [
                                        'username'=>[
                                                        'required',
                                                        'unique:users,username,'.$user->id
                                                    ],           
                                        'nidn'=>[
                                                    'numeric',
                                                    'unique:pe3_dosen,nidn,'.$user->id.',user_id'
                                                ],           
                                        'nipy'=>[
                                                    'numeric',
                                                    'unique:pe3_dosen,nipy,'.$user->id.',user_id'
                                                ],           
                                        'name'=>'required',            
                                        'email'=>'required|string|email|unique:users,email,'.$user->id,
                                        'nomor_hp'=>'required|string|unique:users,nomor_hp,'.$user->id,                                           
                                    ]); 

            $user = \DB::transaction(function () use ($request,$user){

                $user->name = $request->input('name');                
                $user->email = $request->input('email');
                $user->nomor_hp = $request->input('nomor_hp');
                $user->username = $request->input('username');        
                if (!empty(trim($request->input('password')))) {
                    $user->password = Hash::make($request->input('password'));
                }    
                $user->updated_at = \Carbon\Carbon::now()->toDateTimeString();                
                $user->save();

                $user_dosen=UserDosen::find($user->id);
                $user_dosen->nama_dosen=$request->input('name');
                $user_dosen->nidn = $request->input('nidn');
                $user_dosen->nipy = $request->input('nipy');
                $user_dosen->is_dw = $request->input('is_dw');                                             
                
                if (filter_var($request->input('is_dw'),FILTER_VALIDATE_BOOLEAN))
                {
                    $user->assignRole('dosenwali'); 
                    $permission=Role::findByName('dosenwali')->permissions;
                    $permissions=$permission->pluck('name');
                    $user->givePermissionTo($permissions);
                }
                else
                {
                    $user->removeRole('dosenwali');
                    $permission=Role::findByName('dosenwali')->permissions;
                    $permissions=$permission->pluck('name');
                    $user->revokePermissionTo($permissions);
                }
                $user_dosen->save();   
                return $user;
            });
            
            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $this->guard()->user(), 
                                                        'object_id' => $this->guard()->user()->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data user Dosen ('.$user->username.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'user'=>$user,      
                                    'message'=>'Data user Dosen '.$user->username.' berhasil diubah.'
                                ],200); 
        }
        else
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate karena bukan pada tempatnya"]
                                ],422); 
        }
        
        
    }
    /**
     * dapatkan data biodata diri dosen
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function biodatadiri(Request $request,$id)
    { 
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_SHOW');

        $biodatadiri = UserDosen::select(\DB::raw('
                                    pe3_dosen.*,
                                    users.username,
                                    users.email,
                                    users.nomor_hp,
                                    users.foto
                                '))
                                ->join('users','pe3_dosen.user_id','users.id')
                                ->where('pe3_dosen.active',1)
                                ->find($id);
        if (is_null($biodatadiri))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                                    
                                    'biodatadiri'=>$biodatadiri,      
                                    'message'=>'Data Biodata Diri Dosen '.$biodatadiri->username.' berhasil diperoleh.'
                                ],200); 
        }
    }
    /**
     * dapatkan data biodata diri dosen
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatebiodatadiri(Request $request,$id)
    {
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_UPDATE');

        $biodatadiri = UserDosen::where('pe3_dosen.active',1)
                                ->find($id);
        if (is_null($biodatadiri))
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
                'nidn'=>[
                            'numeric',
                            'unique:pe3_dosen,nidn,'.$biodatadiri->user_id.',user_id'
                        ],           
                'nipy'=>[
                            'numeric',
                            'unique:pe3_dosen,nipy,'.$biodatadiri->user_id.',user_id'
                        ],           
                'nama_dosen'=>'required',            
                'tempat_lahir'=>'required',            
                'tanggal_lahir'=>'required',            
                'jk'=>'required',                            
                'email'=>'required|string|email|unique:users,email,'.$biodatadiri->user_id,
                'nomor_hp'=>'required|string|unique:users,nomor_hp,'.$biodatadiri->user_id,                                           
                'alamat_rumah'=>'required',            
            ]); 

            $biodatadiri->nidn=$request->input('nidn');           
            $biodatadiri->nipy=$request->input('nipy');           
            $biodatadiri->gelar_depan=$request->input('gelar_depan');                               
            $biodatadiri->nama_dosen=$request->input('nama_dosen');           
            $biodatadiri->gelar_belakang=$request->input('gelar_belakang');           

            $biodatadiri->tempat_lahir=$request->input('tempat_lahir');           
            $biodatadiri->tanggal_lahir=$request->input('tanggal_lahir');           
            $biodatadiri->jk=$request->input('jk');                      
               
            $biodatadiri->address1_provinsi_id=$request->input('address1_provinsi_id');
            $biodatadiri->address1_provinsi=$request->input('address1_provinsi');
            $biodatadiri->address1_kabupaten_id=$request->input('address1_kabupaten_id');
            $biodatadiri->address1_kabupaten=$request->input('address1_kabupaten');
            $biodatadiri->address1_kecamatan_id=$request->input('address1_kecamatan_id');
            $biodatadiri->address1_kecamatan=$request->input('address1_kecamatan');
            $biodatadiri->address1_desa_id=$request->input('address1_desa_id');
            $biodatadiri->address1_kelurahan=$request->input('address1_kelurahan');
            $biodatadiri->alamat_rumah=$request->input('alamat_rumah');   

            $biodatadiri->save();
            $user=$biodatadiri->user;
            $user->email=$request->input('email');
            $user->nomor_hp=$request->input('nomor_hp');
            $user->save();

            
            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $this->guard()->user(), 
                                                        'object_id' => $this->guard()->user()->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah biodata pribadi Dosen ('.$user->username.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'user'=>$user,      
                                    'biodatadiri'=>$biodatadiri,      
                                    'message'=>'Biodata diri Dosen '.$user->username.' berhasil diubah.'
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
        $this->hasPermissionTo('SYSTEM-USERS-DOSEN_DESTROY');

        $user = User::where('isdeleted','1')
                    ->find($id); 
        
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["User ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $username=$user->username;
            $user->delete();

            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $this->guard()->user(), 
                                                                'object_id' => $this->guard()->user()->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus user Dosen ('.$username.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"User Dosen ($username) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}