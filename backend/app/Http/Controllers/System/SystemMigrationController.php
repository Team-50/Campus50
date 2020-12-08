<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;
use App\Models\System\DataMHSMigrationModel;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SystemMigrationController extends Controller {    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SYSTEM-MIGRATION_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',            
        ]);
        
        $ta=$request->input('TA');
        $daftar_tasmt=[];
        
        for ($tahun=$ta;$tahun < 2020; $tahun++)
        {
            $daftar_tasmt[]=[
                'id'=>$tahun.'1',
                'ta'=>$tahun.'/'.($tahun+1),
                'semester'=>'GANJIL',
            ];   
            $daftar_tasmt[]=[
                'id'=>$tahun.'2',
                'ta'=>$tahun.'/'.($tahun+1),
                'semester'=>'GENAP',
            ];   
        }        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'daftar_tasmt'=>$daftar_tasmt,
                                'message'=>'Fetch data daftar tahun semester berhasil diperoleh'
                            ],200); 
    }
    public function store(Request $request)
    {
        $this->hasPermissionTo('SYSTEM-MIGRATION_STORE');
        
        $status_mhs=json_decode($request->input('status_mhs'),true);
        $request->merge(['status_mhs'=>$status_mhs]);

        $this->validate($request, [
            'nim'=>'required|numeric|unique:pe3_register_mahasiswa,nim',        
            'nirm'=>'required|numeric|unique:pe3_register_mahasiswa,nirm',
            'nama_mhs'=>'required',            
            'dosen_id'=>[
                'required',
                Rule::exists('pe3_dosen','user_id')->where(function($query){
                    return $query->where('is_dw',1);
                })
            ],            
            'prodi_id'=>'required|numeric',            
            'idkelas'=>'required',
            'tahun_pendaftaran'=>'required',
            'status_mhs.*'=>'required',                        
        ]);
        
        $user = \DB::transaction(function () use ($request){
            $now = \Carbon\Carbon::now()->toDateTimeString();   
            $uuid=Uuid::uuid4()->toString();
            $no_hp=mt_rand(1000,9999);
            $ta=$request->input('tahun_pendaftaran');
            $nim=$request->input('nim');
            $user=User::create([
                'id'=>$uuid,
                'name'=>$request->input('nama_mhs'),
                'email'=>"$uuid@staimutanjungpinang.ac.id",
                'username'=> $nim,
                'password'=>Hash::make('12345678'),
                'nomor_hp'=>"+62$no_hp",
                'ta'=>$ta,
                'email_verified_at'=>'',
                'theme'=>'default',  
                'code'=>0,     
                'default_role'=>'mahasiswa',     
                'active'=>1,         
                'foto'=>'storage/images/users/no_photo.png', 
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);    
            DataMHSMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nim'=>$nim,
                'nama_mhs'=>$request->input('nama_mhs'),  
                'aktivitas'=>"Input data ke user berhasil dengan username ($nim)",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $no_formulir='1'.mt_rand();
            $formulir=FormulirPendaftaranModel::create([
                'user_id'=>$user->id,
                'no_formulir'=>$no_formulir,
                'nama_mhs'=>$request->input('nama_mhs'),                
                'telp_hp'=>$request->input('nomor_hp'),
                'kjur1'=>$request->input('prodi_id'),
                'ta'=>$ta,
                'idsmt'=>1,
                'idkelas'=>$request->input('idkelas'),
            ]);
            DataMHSMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nim'=>$nim,
                'nama_mhs'=>$request->input('nama_mhs'),  
                'aktivitas'=>"Input data ke formulir pendaftaran berhasil dengan nomor formulir ($no_formulir)",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $mahasiswa=RegisterMahasiswaModel::create([
                'user_id'=>$user->id,
                'dosen_id'=>$request->input('dosen_id'),
                'konsentrasi_id'=>null,
                'nim'=>$nim,
                'nirm'=>$request->input('nirm'),
                'tahun'=>$formulir->ta,
                'idsmt'=>$formulir->idsmt,
                'kjur'=>$formulir->kjur1,
                'k_status'=>'A',
                'idkelas'=>$formulir->idkelas,
            ]);
            
            $role='mahasiswa';   
            $user->assignRole($role);
            $permission=Role::findByName('mahasiswa')->permissions;
            $user->givePermissionTo($permission->pluck('name'));            
            
            DataMHSMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nim'=>$nim,
                'nama_mhs'=>$request->input('nama_mhs'),  
                'aktivitas'=>"Input data ke register mahasiswa berhasil.",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $status_mhs=$request->input('status_mhs');            
            $i=0;
            for ($tahun=$ta;$tahun < 2020; $tahun++)
            {
                if (isset($status_mhs[$i]) && isset($status_mhs[$i+1]))
                {
                    $status_sebelumnya = $i > 0 ? $status_mhs[$i-1]:$status_mhs[$i];
                    DulangModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nim'=>$mahasiswa->nim,
                        'tahun'=>$tahun,
                        'idsmt'=>1,
                        'tasmt'=>$tahun.'1',
                        'idkelas'=>$formulir->idkelas,
                        'status_sebelumnya'=>$status_sebelumnya,
                        'k_status'=>$status_mhs[$i],
                    ]);
                    
                    DataMHSMigrationModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nim'=>$nim,
                        'nama_mhs'=>$request->input('nama_mhs'),  
                        'aktivitas'=>"Input data ke daftar ulang tahun ".$tahun."1 berhasil dengan status ".$status_mhs[$i],  
                        'tahun'=>$ta,
                        'idsmt'=>1
                    ]);
                    $i+=1;                    
                    DulangModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nim'=>$mahasiswa->nim,
                        'tahun'=>$tahun,
                        'idsmt'=>2,
                        'tasmt'=>$tahun.'2',
                        'idkelas'=>$formulir->idkelas,
                        'status_sebelumnya'=>$status_mhs[$i-1],
                        'k_status'=>$status_mhs[$i],
                    ]);
                    
                    DataMHSMigrationModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nim'=>$nim,
                        'nama_mhs'=>$request->input('nama_mhs'),  
                        'aktivitas'=>"Input data ke daftar ulang tahun ".$tahun."2 berhasil dengan status ".$status_mhs[$i],  
                        'tahun'=>$ta,
                        'idsmt'=>1
                    ]);
                    $i+=1;
                }                
            }   
            return $user;
        });        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                                
                                'user'=>$user,                                
                                'message'=>'Proses migrasi mahasiswa ini berhasil dilakukan, silahkan cek dimasing-masing halaman'
                            ],200);
    }
}
