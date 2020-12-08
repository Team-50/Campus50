<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Akademik\PembagianKelasModel;
use App\Models\Akademik\PembagianKelasPesertaModel;
use App\Models\Akademik\PembagianKelasPenyelenggaraanModel;
use App\Models\Akademik\PenyelenggaraanDosenModel;
use App\Models\UserDosen;

use Ramsey\Uuid\Uuid;

class PembagianKelasController extends Controller
{
    /**
     * daftar penyelenggaraan
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_BROWSE');

        $this->validate($request, [
            'ta'=>'required',
            'semester_akademik'=>'required',            
        ]);

        $ta=$request->input('ta');        
        $semester_akademik=$request->input('semester_akademik');

        $pembagiankelas=PembagianKelasModel::select(\DB::raw('
                            pe3_kelas_mhs.id,
                            pe3_kelas_mhs.idkelas,                            
                            pe3_kelas_mhs.hari,     
                            \'\' AS nama_hari,        
                            pe3_kelas_mhs.jam_masuk,
                            pe3_kelas_mhs.jam_keluar,
                            pe3_kelas_mhs.kmatkul,
                            pe3_kelas_mhs.nmatkul,
                            pe3_kelas_mhs.sks,
                            pe3_dosen.nama_dosen,
                            pe3_dosen.nidn,
                            pe3_kelas_mhs.ruang_kelas_id,
                            pe3_ruangkelas.namaruang,
                            pe3_ruangkelas.kapasitas,
                            0 AS jumlah_mhs,
                            pe3_kelas_mhs.created_at,
                            pe3_kelas_mhs.updated_at                   
                        '))
                        ->join('pe3_dosen','pe3_kelas_mhs.user_id','pe3_dosen.user_id')
                        ->join('pe3_ruangkelas','pe3_ruangkelas.id','pe3_kelas_mhs.ruang_kelas_id')                            
                        ->where('pe3_kelas_mhs.tahun',$ta)
                        ->where('pe3_kelas_mhs.idsmt',$semester_akademik)                                              
                        ->get();
                        
        $pembagiankelas->transform(function ($item,$key){              
            $item->nama_hari=\App\Helpers\Helper::getNamaHari($item->hari);          
            $item->jumlah_mhs=\DB::table('pe3_kelas_mhs_peserta')->where('kelas_mhs_id',$item->id)->count();
            return $item;
        });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'pembagiankelas'=>$pembagiankelas,                                                                                                                                   
                                    'message'=>'Fetch data pembagian kelas berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ; 
    }
    /**
     * simpan
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE');

        $this->validate($request, [            
            'idkelas'=>'required',                        
            'hari'=>'required|numeric',
            'jam_masuk'=>'required',   
            'jam_keluar'=>'required',   
            'penyelenggaraan_dosen_id'=>'required',   
            'ruang_kelas_id'=>'required|exists:pe3_ruangkelas,id',   
            
        ]);

        $pembagiankelas = \DB::transaction(function () use ($request){               
            
            $uuid=Uuid::uuid4()->toString();
            $pembagiankelas=PembagianKelasModel::create([
                'id'=>$uuid,
                'user_id'=>$request->input('user_id'),
                'kmatkul'=>$request->input('kmatkul'),
                'nmatkul'=>$request->input('nmatkul'),
                'sks'=>$request->input('sks'),
                'idkelas'=>$request->input('idkelas'),                
                'hari'=>$request->input('hari'),
                'jam_masuk'=>$request->input('jam_masuk'),
                'jam_keluar'=>$request->input('jam_keluar'),                
                'ruang_kelas_id'=>$request->input('ruang_kelas_id'),                
                'idsmt'=>$request->input('idsmt'),
                'tahun'=>$request->input('tahun'),

            ]);
            
            $penyelenggaraan_dosen=json_decode($request->input('penyelenggaraan_dosen_id'),true);

            foreach ($penyelenggaraan_dosen as $v)
            {
                PembagianKelasPenyelenggaraanModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'kelas_mhs_id'=>$uuid,
                    'penyelenggaraan_dosen_id'=>$v
                ]);
            }

            return $pembagiankelas;

        });
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                    
                                'pembagiankelas'=>$pembagiankelas,                                            
                                'message'=>'Pembagian Kelas berhasil ditambahkan.'
                            ],200);
    }
    public function show(Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_SHOW');

        $pembagiankelas = PembagianKelasModel::select(\DB::raw('
                                                    pe3_kelas_mhs.user_id,                                                    
                                                    pe3_kelas_mhs.id,
                                                    pe3_kelas_mhs.idkelas,                            
                                                    pe3_kelas_mhs.hari,     
                                                    \'\' AS nama_hari,        
                                                    pe3_kelas_mhs.jam_masuk,
                                                    pe3_kelas_mhs.jam_keluar,
                                                    pe3_kelas_mhs.kmatkul,
                                                    pe3_kelas_mhs.nmatkul,
                                                    pe3_kelas_mhs.sks,
                                                    pe3_dosen.nama_dosen,
                                                    pe3_dosen.nidn,
                                                    pe3_kelas_mhs.ruang_kelas_id,
                                                    pe3_ruangkelas.namaruang,
                                                    pe3_ruangkelas.kapasitas,
                                                    0 AS jumlah_mhs,
                                                    pe3_kelas_mhs.tahun,
                                                    pe3_kelas_mhs.idsmt,
                                                    pe3_kelas_mhs.created_at,
                                                    pe3_kelas_mhs.updated_at                   
                                                '))
                                            ->join('pe3_dosen','pe3_kelas_mhs.user_id','pe3_dosen.user_id')
                                            ->join('pe3_ruangkelas','pe3_ruangkelas.id','pe3_kelas_mhs.ruang_kelas_id')                            
                                            ->find($id);
        if (is_null($pembagiankelas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Pembagian Kelas dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $pembagiankelas->nama_hari=\App\Helpers\Helper::getNamaHari($pembagiankelas->hari);          
            $pembagiankelas->jumlah_mhs=\DB::table('pe3_kelas_mhs_peserta')->where('kelas_mhs_id',$pembagiankelas->id)->count();

            $penyelenggaraan=PembagianKelasPenyelenggaraanModel::select(\DB::raw('
                                        pe3_kelas_mhs_penyelenggaraan.id,
                                        pe3_penyelenggaraan_dosen.penyelenggaraan_id,
                                        pe3_matakuliah.kmatkul,
                                        pe3_matakuliah.nmatkul,                                        
                                        pe3_matakuliah.sks,
                                        pe3_matakuliah.ta,
                                        pe3_matakuliah.kjur,
                                        0 AS jumlah_mhs
                                    '))
                                    ->join('pe3_penyelenggaraan_dosen','pe3_penyelenggaraan_dosen.id','pe3_kelas_mhs_penyelenggaraan.penyelenggaraan_dosen_id')
                                    ->join('pe3_penyelenggaraan','pe3_penyelenggaraan_dosen.penyelenggaraan_id','pe3_penyelenggaraan.id')                            
                                    ->join('pe3_matakuliah','pe3_matakuliah.id','pe3_penyelenggaraan.matkul_id')
                                    ->where('kelas_mhs_id',$id)
                                    ->get();
            
        $penyelenggaraan->transform(function ($item,$key){                      
            $item->jumlah_mhs=\DB::table('pe3_krsmatkul')->where('penyelenggaraan_id',$item->penyelenggaraan_id)->count();
            return $item;
        });

        $peserta=PembagianKelasPesertaModel::select(\DB::raw('
                                        pe3_kelas_mhs_peserta.id,
                                        pe3_krs.nim,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.idkelas,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.kjur
                                    '))
                                    ->join('pe3_krsmatkul','pe3_krsmatkul.id','pe3_kelas_mhs_peserta.krsmatkul_id')
                                    ->join('pe3_krs','pe3_krs.id','pe3_krsmatkul.krs_id')                            
                                    ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_krs.user_id')
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_register_mahasiswa.user_id')
                                    ->where('kelas_mhs_id',$id)
                                    ->get();
                                    
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                    
                                'pembagiankelas'=>$pembagiankelas,                                            
                                'penyelenggaraan'=>$penyelenggaraan,                                            
                                'peserta'=>$peserta,                                            
                                'message'=>"Pembagian kelas dengan id ($id) matakuliah berhasil diperoleh."
                            ],200);
        }
    }
    public function matakuliah (Request $request,$id)
    {
        $pembagian = PembagianKelasModel::find($id); 
        
        if (is_null($pembagian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Kelas Mahasiswa dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $penyelenggaraan=PembagianKelasPenyelenggaraanModel::select(\DB::raw('
                                        pe3_kelas_mhs_penyelenggaraan.id,
                                        pe3_penyelenggaraan_dosen.penyelenggaraan_id,
                                        pe3_matakuliah.kmatkul,
                                        pe3_matakuliah.nmatkul,                                        
                                        pe3_matakuliah.sks,
                                        pe3_matakuliah.ta,
                                        pe3_matakuliah.kjur,
                                        0 AS jumlah_mhs
                                    '))
                                    ->join('pe3_penyelenggaraan_dosen','pe3_penyelenggaraan_dosen.id','pe3_kelas_mhs_penyelenggaraan.penyelenggaraan_dosen_id')
                                    ->join('pe3_penyelenggaraan','pe3_penyelenggaraan_dosen.penyelenggaraan_id','pe3_penyelenggaraan.id')                            
                                    ->join('pe3_matakuliah','pe3_matakuliah.id','pe3_penyelenggaraan.matkul_id')
                                    ->where('kelas_mhs_id',$id)
                                    ->get();
            return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                                                                         
                                'penyelenggaraan'=>$penyelenggaraan,                                            
                                'message'=>"Daftar Peserta MHS dari Kelas MHS dengan id ($id) berhasil diperoleh."
                            ],200);
        }
    }
    public function peserta (Request $request,$id)
    {
        $pembagian = PembagianKelasModel::find($id); 
        
        if (is_null($pembagian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Kelas Mahasiswa dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $peserta=PembagianKelasPesertaModel::select(\DB::raw('
                                        pe3_kelas_mhs_peserta.id,
                                        pe3_krs.nim,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.idkelas,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.kjur
                                    '))
                                    ->join('pe3_krsmatkul','pe3_krsmatkul.id','pe3_kelas_mhs_peserta.krsmatkul_id')
                                    ->join('pe3_krs','pe3_krs.id','pe3_krsmatkul.krs_id')                            
                                    ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_krs.user_id')
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_register_mahasiswa.user_id')
                                    ->where('kelas_mhs_id',$id)
                                    ->get();
            return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                                                                         
                                'peserta'=>$peserta,                                            
                                'message'=>"Daftar Peserta MHS dari Kelas MHS dengan id ($id) berhasil diperoleh."
                            ],200);
        }
    }
    public function pengampu (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_SHOW');

        $this->validate($request, [            
            'pid'=>'required', 
            'idpenyelenggaraan'=>'required|exists:pe3_penyelenggaraan,id'           
        ]);
        
        $data=[];
        $idpenyelenggaraan=$request->input('idpenyelenggaraan');
        switch($request->input('pid'))
        {
            case 'belumterdaftar':
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
        }
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'dosen'=>$data,
                                'message'=>'Fetch data Dosen Pengampu berhasil diperoleh'
                            ],200);  
    }
    public function storematakuliah (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE');

        $this->validate($request, [   
            'kelas_mhs_id'=>'required|exists:pe3_kelas_mhs,id',           
            'penyelenggaraan_dosen_id'=>'required',  
        ]);
        $kelas_mhs_id=$request->input('kelas_mhs_id');       
        
        $penyelenggaraan_dosen=json_decode($request->input('penyelenggaraan_dosen_id'),true);

        foreach ($penyelenggaraan_dosen as $v)
        {
            PembagianKelasPenyelenggaraanModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'kelas_mhs_id'=>$kelas_mhs_id,
                'penyelenggaraan_dosen_id'=>$v
            ]);
        }        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                                                                                           
                                'message'=>"Menambahkan daftar matakuliah dengan ID ($kelas_mhs_id)  berhasil ditambahkan."
                            ],200);
    }
    public function storepeserta (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE');

        $members_selected=json_decode($request->input('members_selected'),true);
        $request->merge(['members_selected'=>$members_selected]);

        $this->validate($request, [   
            'kelas_mhs_id'=>'required|exists:pe3_kelas_mhs,id',           
            'members_selected.*'=>'required',            
        ]);
        $kelas_mhs_id=$request->input('kelas_mhs_id');
        
        $daftar_members=[];
        foreach ($members_selected as $v)
        {
            $daftar_members[]=[
                'id'=>Uuid::uuid4()->toString(),
                'kelas_mhs_id'=>$kelas_mhs_id,
                'krsmatkul_id'=>$v['id'],                
            ];
        }
        PembagianKelasPesertaModel::insert($daftar_members);
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                    
                                'daftar_members'=>$daftar_members,                                            
                                'message'=>"Peserta kelas dengan ID($kelas_mhs_id)  berhasil ditambahkan."
                            ],200);
    }

    public function update(Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_UPDATE');
        
        $pembagian = PembagianKelasModel::find($id); 

        if (is_null($pembagian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Dosen Pengampu dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [                                     
                'hari'=>'required|numeric',
                'jam_masuk'=>'required',   
                'jam_keluar'=>'required',                   
                'ruang_kelas_id'=>'required|exists:pe3_ruangkelas,id',   
            ]);

            $pembagian->hari=$request->input('hari');
            $pembagian->jam_masuk=$request->input('jam_masuk');
            $pembagian->jam_keluar=$request->input('jam_keluar');
            $pembagian->ruang_kelas_id=$request->input('ruang_kelas_id');
            $pembagian->save();
            
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $pembagian, 
                                                                'object_id' => $pembagian->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Mengupdate pembagian kelas dengan id ('.$id.') berhasil'
                                                            ]);
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',                
                                        'message' => 'Mengupdate pembagian kelas dengan id ('.$id.') berhasil'
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
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_DESTROY');

        $pembagiankelas = PembagianKelasModel::find($id); 
        
        if (is_null($pembagiankelas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kelas dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $pembagiankelas, 
                                                                'object_id' => $pembagiankelas->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus pembagian kelas dengan id ('.$id.') berhasil'
                                                            ]);
            $pembagiankelas->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Kelas dengan ID ($id) berhasil dihapus"
                                    ],200);         
        }
                    
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroymatkul(Request $request,$id)
    { 
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_DESTROY');

        $penyelenggaraan = PembagianKelasPenyelenggaraanModel::select(\DB::raw('
                                                                pe3_kelas_mhs_penyelenggaraan.id,
                                                                pe3_kelas_mhs_penyelenggaraan.penyelenggaraan_dosen_id,
                                                                pe3_kelas_mhs_penyelenggaraan.kelas_mhs_id,
                                                                pe3_penyelenggaraan_dosen.penyelenggaraan_id
                                                            '))
                                                            ->join('pe3_penyelenggaraan_dosen','pe3_kelas_mhs_penyelenggaraan.penyelenggaraan_dosen_id','pe3_penyelenggaraan_dosen.id')
                                                            ->find($id); 
        
        if (is_null($penyelenggaraan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Anggota Matakuliah di kelas dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $penyelenggaraan, 
                                                                'object_id' => $penyelenggaraan->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus matauliah kelas di mahasiswa dengan id ('.$id.') berhasil'
                                                            ]);

            $penyelenggaraan_id = \DB::transaction(function () use ($penyelenggaraan){      
                $penyelenggaraan_id=$penyelenggaraan->penyelenggaraan_id;         
                \DB::table('pe3_kelas_mhs_peserta')
                        ->join('pe3_krsmatkul','pe3_krsmatkul.id','pe3_kelas_mhs_peserta.krsmatkul_id')
                        ->where('pe3_krsmatkul.penyelenggaraan_id',$penyelenggaraan_id)
                        ->delete();

                \DB::table('pe3_kelas_mhs_penyelenggaraan')                        
                        ->where('id',$penyelenggaraan->id)
                        ->delete();

                return $penyelenggaraan_id;
            });           
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',      
                                        'penyelenggaraan_id'=>$penyelenggaraan_id,          
                                        'message' => 'Menghapus matakuliah di kelas mahasiswa dengan id ('.$id.') berhasil'
                                    ],200);         
        }
                    
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroypeserta(Request $request,$id)
    { 
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_DESTROY');

        $peserta = PembagianKelasPesertaModel::find($id); 
        
        if (is_null($peserta))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Peserta dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $peserta, 
                                                                'object_id' => $peserta->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus peserta kelas mahasiswa dengan id ('.$id.') berhasil'
                                                            ]);
            $peserta->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message' => 'Menghapus peserta kelas mahasiswa dengan id ('.$id.') berhasil'
                                    ],200);         
        }
                    
    }
}
