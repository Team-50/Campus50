<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\SPMB\PMBPersyaratanModel;
use App\Helpers\Helper;

use Ramsey\Uuid\Uuid;

class PMBPersyaratanController extends Controller { 
    /**
     * digunakan untuk mendapatkan daftar persyaratan mahasiswa baru
     */
    public function index(Request $request)
    {
       $this->hasPermissionTo('SPMB-PMB-PERSYARATAN_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');
        
        $jumlah_persyaratan=\DB::table('pe3_persyaratan')->where('ta',$ta)->count();
        $data = FormulirPendaftaranModel::select(\DB::raw("users.id,users.name,users.nomor_hp,pe3_kelas.nkelas,users.active,users.foto,$jumlah_persyaratan AS jumlah_persyaratan,0 AS persyaratan,'BELUM LENGKAP' AS status,users.created_at,users.updated_at"))                    
                    ->join('users','pe3_formulir_pendaftaran.user_id','users.id')
                    ->join('pe3_kelas','pe3_formulir_pendaftaran.idkelas','pe3_kelas.idkelas')
                    ->whereExists(function ($query) {
                        $query->select(\DB::raw(1))
                              ->from('pe3_pmb_persyaratan')
                              ->whereRaw('pe3_pmb_persyaratan.user_id = users.id');
                    })
                    ->where('users.ta',$ta)
                    ->where('kjur1',$prodi_id)                    
                    ->where('users.active',1)
                    ->orderBy('users.name','ASC')
                    ->get();
        
        
        foreach ($data as $item)
        {
            $item->persyaratan=\DB::table('pe3_pmb_persyaratan')->where('user_id',$item->id)->count();
            $item->status=$item->persyaratan < $jumlah_persyaratan ? 'BELUM LENGKAP':'LENGKAP';
        }
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'persyaratan'=>$data,
                                'message'=>'Fetch data persyaratancalon mahasiswa baru berhasil diperoleh'
                            ],200);  

    }
    /**
     * digunakan untuk mendapatkan daftar persyaratan mahasiswa baru
     */
    public function show(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-PERSYARATAN_SHOW');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                        'status'=>0,
                                        'pid'=>'fetchdata',                
                                        'message'=>["User ID ($id) gagal diperoleh"]
                                    ],422); 
        }
        else
        {
            $subquery = \DB::table('pe3_pmb_persyaratan')
                            ->select(\DB::raw('id AS persyaratan_pmb_id,persyaratan_id,path,pe3_pmb_persyaratan.verified,created_at,updated_at'))
                            ->where('user_id',$id);

            $persyaratan=\DB::table('pe3_persyaratan')
                            ->select(\DB::raw('pe3_persyaratan.id AS persyaratan_id,
                                        pe3_pmb_persyaratan.persyaratan_pmb_id,
                                        pe3_persyaratan.nama_persyaratan,
                                        pe3_pmb_persyaratan.path,
                                        pe3_pmb_persyaratan.verified,
                                        pe3_pmb_persyaratan.created_at,
                                        pe3_pmb_persyaratan.updated_at'))
                            ->leftJoinSub($subquery,'pe3_pmb_persyaratan',function($join){
                                $join->on('pe3_persyaratan.id','=','pe3_pmb_persyaratan.persyaratan_id');
                            })
                            ->where('pe3_persyaratan.ta',$user->ta)
                            ->where('pe3_persyaratan.proses','pmb')
                            ->orderBy('pe3_persyaratan.nama_persyaratan','ASC')
                            ->get();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'persyaratan'=>$persyaratan,      
                                    'message'=>'Persyaratan user PMB '.$user->name.' berhasil diperoleh.'
                                ],200); 
        }
    }
    public function upload (Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-PERSYARATAN_STORE');

        $user = User::find($id); 
        
        if ($user == null)
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'store',                
                                    'message'=>["Data Mahasiswa tidak ditemukan."]
                                ],422);         
        }
        else
        {
            $this->validate($request, [      
                'persyaratan_pmb_id'=>'required',
                'persyaratan_id'=>'required|exists:pe3_persyaratan,id',                                                               
                'nama_persyaratan'=>'required',  
                'foto'=>'required'                        
            ]);
            $name=$user->name;
            $foto = $request->file('foto');
            $mime_type=$foto->getMimeType();
            if ($mime_type=='image/png' || $mime_type=='image/jpeg')
            {
                $folder=Helper::public_path('images/pmb/');
                $file_name=uniqid('img').".".$foto->getClientOriginalExtension();
                
                $persyaratan=PMBPersyaratanModel::find($request->input('persyaratan_pmb_id'));

                if (is_null($persyaratan))
                {
                    $now = \Carbon\Carbon::now()->toDateTimeString();        
                    $persyaratan=PMBPersyaratanModel::create([
                                                                'id'=>Uuid::uuid4()->toString(),
                                                                'persyaratan_id'=>$request->input('persyaratan_id'),
                                                                'user_id'=>$id,
                                                                'nama_persyaratan'=> $request->input('nama_persyaratan'),
                                                                'path'=>"storage/images/pmb/$file_name",                                            
                                                                'created_at'=>$now, 
                                                                'updated_at'=>$now
                                                            ]); 
                    
                }
                else
                {
                    $old_file=$persyaratan->path;
                    $persyaratan->path="storage/images/pmb/$file_name";
                    $persyaratan->save();
                    if ($old_file != 'images/no_photo.png')
                    {
                        $old_file=str_replace('storage/','',$old_file);
                        if (is_file(Helper::public_path($old_file)))
                        {
                            unlink(Helper::public_path($old_file));
                        }
                    }
                }                
                $foto->move($folder,$file_name);
                return Response()->json([
                                            'status'=>0,
                                            'pid'=>'store',
                                            'persyaratan'=>$persyaratan,                
                                            'message'=>"Persyaratan Mahasiswa baru ($name)  berhasil diupload"
                                        ],200);    
            }
            else
            {
                return Response()->json([
                                        'status'=>1,
                                        'pid'=>'store',
                                        'message'=>["Extensi file yang diupload bukan jpg atau png."]
                                    ],422); 
                

            }
        }
    }
    public function hapusfilepersyaratan(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-PERSYARATAN_DESTROY');

        $persyaratan = PMBPersyaratanModel::find($id); 
        
        if ($persyaratan == null)
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Persyaratan Mahasiswa Baru tidak ditemukan."]
                                ],422);         
        }
        else
        {
            $userid=$persyaratan->user_id;
            $old_file=$persyaratan->path;            
            $persyaratan->delete();

            if ($old_file != 'images/no-image.png')
            {
                $old_file=str_replace('storage/','',$old_file);
                if (is_file(Helper::public_path($old_file)))
                {
                    unlink(Helper::public_path($old_file));
                }
            }
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                                        
                                        'message'=>"Persyaratan Mahasiswa Baru user id ($userid)  berhasil dihapus"
                                    ],200);
        }
    }
    public function verifikasipersyaratan(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-PERSYARATAN_UPDATE');

        $persyaratan = PMBPersyaratanModel::find($id); 
        
        if ($persyaratan == null)
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Persyaratan Mahasiswa Baru tidak ditemukan."]
                                ],422);         
        }
        else
        {
            $persyaratan->verified=1;            
            $persyaratan->save();
         
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update', 
                                        'persyaratan'=>$persyaratan,                                       
                                        'message'=>"Persyaratan Dokumen (".$persyaratan->nama_persyaratan.") berhasil diverifikasi"
                                    ],200);
        }
    }
}
