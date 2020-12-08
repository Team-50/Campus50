<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Akademik\PenyelenggaraanMatakuliahModel;
use App\Models\Akademik\PenyelenggaraanDosenModel;
use App\Models\UserDosen;

use Ramsey\Uuid\Uuid;

class PenyelenggaraanMatakuliahController extends Controller
{
    /**
     * daftar penyelenggaraan
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_BROWSE');

        $this->validate($request, [
            'ta'=>'required',
            'semester_akademik'=>'required',
            'prodi_id'=>'required'
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        $semester_akademik=$request->input('semester_akademik');

        $penyelenggaraan=PenyelenggaraanMatakuliahModel::select(\DB::raw('
                                                            id,
                                                            COALESCE(nama_dosen,\'N.A\') AS nama_dosen,
                                                            kmatkul,
                                                            nmatkul,                                                            
                                                            sks,       
                                                            semester,
                                                            ta_matkul,                                                                                                                 
                                                            0 AS jumlah_dosen,
                                                            0 AS jumlah_mhs
                                                        '))
                                                        ->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_penyelenggaraan.user_id')
                                                        ->where('tahun',$ta)
                                                        ->where('idsmt',$semester_akademik)
                                                        ->where('kjur',$prodi_id)
                                                        ->orderBy('ta_matkul','ASC')    
                                                        ->orderBy('semester','ASC')                      
                                                        ->orderBy('kmatkul','ASC')                                                            
                                                        ->get();
                                                        
        $penyelenggaraan->transform(function ($item,$key){
            $item->jumlah_dosen=\DB::table('pe3_penyelenggaraan_dosen')->where('penyelenggaraan_id',$item->id)->count();
            $item->jumlah_mhs=\DB::table('pe3_krsmatkul')->where('penyelenggaraan_id',$item->id)->count();;
            return $item;
        });

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'penyelenggaraan'=>$penyelenggaraan,                                                                                                                                   
                                    'message'=>'Fetch data penyelenggaraan matakuliah berhasil.'
                                ],200); 
    }
    /**
     * simpan
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE');

        $matkul_selected=json_decode($request->input('matkul_selected'),true);
        $request->merge(['matkul_selected'=>$matkul_selected]);

        $this->validate($request, [            
            'ta'=>'required',
            'semester_akademik'=>'required',
            'prodi_id'=>'required',   
            'matkul_selected.*'=>'required',                   
        ]);
        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        $semester_akademik=$request->input('semester_akademik');

        $daftar_matkul=[];
        foreach ($matkul_selected as $v)
        {
            $daftar_matkul[]=[
                'id'=>Uuid::uuid4()->toString(),
                'matkul_id'=>$v['id'],
                'kmatkul'=>$v['kmatkul'],
                'nmatkul'=>$v['nmatkul'],
                'sks'=>$v['sks'],
                'semester'=>$v['semester'],
                'ta_matkul'=>$v['ta'],
                'idsmt'=>$semester_akademik,
                'tahun'=>$ta,
                'kjur'=>$prodi_id,
                'created_at'=>\Carbon\Carbon::now(),
                'updated_at'=>\Carbon\Carbon::now()
            ];
        }
        PenyelenggaraanMatakuliahModel::insert($daftar_matkul);

        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                    
                                'matkul_selected'=>$matkul_selected,                                            
                                'message'=>'Penyelenggaraan matakuliah berhasil ditambahkan.'
                            ],200);
    }
    public function show(Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_SHOW');

        $penyelenggaraan = PenyelenggaraanMatakuliahModel::find($id);
        if (is_null($penyelenggaraan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Penyelenggaraan dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                    
                                    'penyelenggaraan'=>$penyelenggaraan,                                            
                                    'message'=>"Penyelenggaraan dengan id ($id) matakuliah berhasil diperoleh."
                                ],200);
        }
    }
    public function member(Request $request,$id)
    {
        
    }
    public function members(Request $request)
    {
        $this->validate($request, [            
            'pid'=>'required|in:belumterdaftar',             
            'penyelenggaraan'=>'required',             
        ]);
        $penyelenggaraan=json_decode($request->input('penyelenggaraan',false));
        $penyelenggaraan_id=[];
        foreach($penyelenggaraan as $v)
        {
            $penyelenggaraan_id[]=$v->penyelenggaraan_id;
        }
        $peserta=[];
        switch($request->input('pid'))
        {
            case 'belumterdaftar':
                $peserta=\DB::table('pe3_krsmatkul')
                        ->select(\DB::raw('
                            pe3_krsmatkul.id,
                            pe3_krsmatkul.nim,
                            pe3_formulir_pendaftaran.nama_mhs,
                            pe3_register_mahasiswa.kjur,
                            pe3_register_mahasiswa.idkelas,
                            pe3_register_mahasiswa.tahun
                        '))
                        ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.nim','pe3_krsmatkul.nim')
                        ->join('pe3_formulir_pendaftaran','pe3_register_mahasiswa.user_id','pe3_formulir_pendaftaran.user_id')
                        ->whereIn('penyelenggaraan_id',$penyelenggaraan_id)      
                        ->whereNotIn('pe3_krsmatkul.id',function($query){
                            $query->select('krsmatkul_id')
                                ->from('pe3_kelas_mhs_peserta');                                        
                                
                        })  
                        ->orderBy('pe3_formulir_pendaftaran.nama_mhs','ASC')                
                        ->get();
            break;
        }
        

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'members'=>$peserta,
                                'message'=>'Fetch data peserta berdasarkan penyelenggaraan_id berhasil diperoleh'
                            ],200);  
    }
    public function pengampu (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_SHOW');

        $this->validate($request, [            
            'pid'=>'required|in:belumterdaftar,terdaftar,daftarpengampu',             
        ]);
        
        $data=[];        
        switch($request->input('pid'))
        {
            case 'belumterdaftar':
                $this->validate($request, [                                
                    'idpenyelenggaraan'=>'required|exists:pe3_penyelenggaraan,id'           
                ]);
                $idpenyelenggaraan=$request->input('idpenyelenggaraan');
                $data=UserDosen::select(\DB::raw('
                                    user_id,
                                    nidn,                                    
                                    nama_dosen
                                '))       
                                ->where('active',1)                                  
                                ->whereNotIn('user_id',function($query) use ($idpenyelenggaraan){
                                    $query->select('user_id')
                                        ->from('pe3_penyelenggaraan_dosen')
                                        ->where('penyelenggaraan_id',$idpenyelenggaraan);                                        
                                })
                                ->orderBy('nama_dosen','ASC')                                                      
                                ->get();
            break;
            case 'terdaftar':
                $this->validate($request, [            
                    'idpenyelenggaraan'=>'required|exists:pe3_penyelenggaraan,id'             
                ]);
                $idpenyelenggaraan=$request->input('idpenyelenggaraan');
                $data=UserDosen::select(\DB::raw('
                                    pe3_penyelenggaraan_dosen.id,
                                    pe3_penyelenggaraan_dosen.penyelenggaraan_id,
                                    pe3_penyelenggaraan_dosen.user_id,
                                    pe3_dosen.nidn,                                    
                                    pe3_dosen.nama_dosen,
                                    pe3_penyelenggaraan_dosen.is_ketua,
                                    pe3_penyelenggaraan_dosen.created_at,
                                    pe3_penyelenggaraan_dosen.updated_at
                                '))       
                                ->join('pe3_penyelenggaraan_dosen','pe3_penyelenggaraan_dosen.user_id','pe3_dosen.user_id')                                                                  
                                ->where('penyelenggaraan_id',$idpenyelenggaraan)                                       
                                ->orderBy('nama_dosen','ASC')                                                      
                                ->get();
            break;
            case 'daftarpengampu':
                $this->validate($request, [            
                    'ta'=>'required',
                    'semester_akademik'=>'required',                    
                ]);

                $ta=$request->input('ta');                
                $semester_akademik=$request->input('semester_akademik');
                
                $data=PenyelenggaraanDosenModel::select(\DB::raw('
                                                DISTINCT(pe3_dosen.user_id) AS user_id,                                                                                                
                                                pe3_dosen.nama_dosen,
                                                pe3_dosen.nidn
                                            '))
                                            ->join('pe3_penyelenggaraan','pe3_penyelenggaraan_dosen.penyelenggaraan_id','pe3_penyelenggaraan.id')
                                            ->join('pe3_dosen','pe3_dosen.user_id','pe3_penyelenggaraan_dosen.user_id')
                                            ->where('pe3_penyelenggaraan.tahun',$ta)                                            
                                            ->where('pe3_penyelenggaraan.idsmt',$semester_akademik)
                                            ->orderBy('pe3_dosen.nama_dosen','ASC')
                                            ->get();

            break;
        }
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'dosen'=>$data,
                                'message'=>'Fetch data Dosen Pengampu berhasil diperoleh'
                            ],200);  
    }
    public function matakuliah(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_SHOW');

        $this->validate($request, [                                
            'user_id'=>'required|exists:pe3_dosen,user_id',           
            'ta'=>'required',
            'semester_akademik'=>'required|in:1,2,3',            
        ]);
        
        $ta=$request->input('ta');
        $user_id=$request->input('user_id');        
        $semester_akademik=$request->input('semester_akademik');
        
        $data=PenyelenggaraanDosenModel::select(\DB::raw(' 
                                                pe3_penyelenggaraan_dosen.id,
                                                pe3_penyelenggaraan.matkul_id,
                                                CONCAT(pe3_matakuliah.nmatkul,\' [\',pe3_matakuliah.kmatkul,\']\',\'MATKUL TA \',pe3_matakuliah.ta) AS nmatkul                                                
                                            '))
                                            ->join('pe3_penyelenggaraan','pe3_penyelenggaraan_dosen.penyelenggaraan_id','pe3_penyelenggaraan.id')
                                            ->join('pe3_matakuliah','pe3_matakuliah.id','pe3_penyelenggaraan.matkul_id')
                                            ->where('pe3_penyelenggaraan.tahun',$ta)                                            
                                            ->where('pe3_penyelenggaraan_dosen.user_id',$user_id)
                                            ->where('pe3_penyelenggaraan.idsmt',$semester_akademik)
                                            ->orderBy('pe3_matakuliah.kmatkul','ASC')
                                            ->get();

        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                    
                                'matakuliah'=>$data,                                            
                                'message'=>"Daftar matakuliah yang di ampu oleh ($user_id) berhasil diperoleh."
                            ],200);
        
    }
    public function storedosenpengampu (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE');

        $this->validate($request, [   
            'penyelenggaraan_id'=>'required|exists:pe3_penyelenggaraan,id',           
            'dosen_id'=>'required|exists:pe3_dosen,user_id',
            'is_ketua'=>'required'
        ]);
        $idpenyelenggaraan=$request->input('penyelenggaraan_id');
        $is_ketua=$request->input('is_ketua');
        if ($is_ketua)
        {
            PenyelenggaraanDosenModel::where('penyelenggaraan_id',$idpenyelenggaraan)
                                    ->update(['is_ketua'=>false]);
        }
        $dosen=PenyelenggaraanDosenModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'penyelenggaraan_id'=>$idpenyelenggaraan,
            'user_id'=>$request->input('dosen_id'),
            'is_ketua'=>$request->input('is_ketua')
        ]);
        if ($is_ketua)
        {
            $penyelenggaraan=$dosen->penyelenggaraan;
            $penyelenggaraan->user_id=$request->input('dosen_id');
            $penyelenggaraan->save();
        }
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                    
                                'dosen'=>$dosen,                                            
                                'message'=>'Dosen pengampu Penyelenggaraan matakuliah ini berhasil ditambahkan.'
                            ],200);
    }

    public function updateketua(Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_UPDATE');
        
        $dosen = PenyelenggaraanDosenModel::find($id); 

        if (is_null($dosen))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Dosen Pengampu dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [                                     
                'is_ketua'=>'required'
            ]);
            $idpenyelenggaraan=$request->input('penyelenggaraan_id');

            PenyelenggaraanDosenModel::where('penyelenggaraan_id',$idpenyelenggaraan)
                                    ->update(['is_ketua'=>false]);

            $dosen->is_ketua=$request->input('is_ketua');
            $dosen->save();

            $penyelenggaraan=$dosen->penyelenggaraan;
            $penyelenggaraan->user_id=$dosen->user_id;
            $penyelenggaraan->save();
            
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $dosen, 
                                                                'object_id' => $dosen->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Mengupdate ketua group dosen pengampu dengan id penyelenggaraan ('.$idpenyelenggaraan.') berhasil'
                                                            ]);
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',                
                                        'message' => 'Mengupdate ketua group dosen pengampu dengan id penyelenggaraan ('.$idpenyelenggaraan.') berhasil'
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
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_DESTROY');

        $penyelenggaraan = PenyelenggaraanMatakuliahModel::find($id); 
        
        if (is_null($penyelenggaraan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Penyelenggaraan dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $penyelenggaraan, 
                                                                'object_id' => $penyelenggaraan->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus penyelenggaraan matakuliah dengan id ('.$id.') berhasil'
                                                            ]);
            $penyelenggaraan->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Penyelenggaraan dengan ID ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroypengampu(Request $request,$id)
    { 
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_DESTROY');

        $dosen = PenyelenggaraanDosenModel::find($id); 
        
        if (is_null($dosen))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Dosen Pengampu dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $dosen, 
                                                                'object_id' => $dosen->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus penyelenggaraan dosen dengan id ('.$id.') berhasil'
                                                            ]);
            $dosen->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Penyelenggaraan Dosen dengan ID ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}
