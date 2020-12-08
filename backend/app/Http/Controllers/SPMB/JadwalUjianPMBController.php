<?php

namespace App\Http\Controllers\SPMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SPMB\JadwalUjianPMBModel;
use App\Models\SPMB\PesertaUjianPMBModel;
use App\Models\SPMB\SoalPMBModel;

use Ramsey\Uuid\Uuid;

class JadwalUjianPMBController extends Controller {  
    /**
     * daftar jadwal ujian pmb
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_BROWSE');

        $this->validate($request, [                       
            'tahun_pendaftaran'=>'required',
            'semester_pendaftaran'=>'required'
        ]);
        $tahun_pendaftaran=$request->input('tahun_pendaftaran');
        $semester_pendaftaran=$request->input('semester_pendaftaran');
        
        if ($this->hasRole(['mahasiswabaru','mahasiswa']))
        {
            $jadwal_ujian=JadwalUjianPMBModel::select(\DB::raw('pe3_jadwal_ujian_pmb.id,
                                                pe3_jadwal_ujian_pmb.nama_kegiatan, 
                                                pe3_jadwal_ujian_pmb.jumlah_soal, 
                                                pe3_jadwal_ujian_pmb.tanggal_ujian, 
                                                pe3_jadwal_ujian_pmb.jam_mulai_ujian, 
                                                pe3_jadwal_ujian_pmb.jam_selesai_ujian, 
                                                pe3_jadwal_ujian_pmb.tanggal_akhir_daftar, 
                                                pe3_jadwal_ujian_pmb.durasi_ujian, 
                                                pe3_jadwal_ujian_pmb.status_pendaftaran, 
                                                pe3_jadwal_ujian_pmb.status_ujian,                                                 
                                                pe3_jadwal_ujian_pmb.ruangkelas_id,
                                                pe3_ruangkelas.namaruang,
                                                0 AS jumlah_peserta,
                                                pe3_jadwal_ujian_pmb.created_at,
                                                pe3_jadwal_ujian_pmb.updated_at
                                            '))
                                            ->join('pe3_peserta_ujian_pmb','pe3_peserta_ujian_pmb.jadwal_ujian_id','pe3_jadwal_ujian_pmb.id')
                                            ->leftJoin('pe3_ruangkelas','pe3_ruangkelas.id','pe3_jadwal_ujian_pmb.ruangkelas_id')
                                            ->where('ta',$tahun_pendaftaran)
                                            ->where('idsmt',$semester_pendaftaran)
                                            ->orderBy('tanggal_akhir_daftar','desc')
                                            ->get();
        }
        else
        {
            $jadwal_ujian=JadwalUjianPMBModel::select(\DB::raw('pe3_jadwal_ujian_pmb.id,
                                                pe3_jadwal_ujian_pmb.nama_kegiatan, 
                                                pe3_jadwal_ujian_pmb.jumlah_soal, 
                                                pe3_jadwal_ujian_pmb.tanggal_ujian, 
                                                pe3_jadwal_ujian_pmb.jam_mulai_ujian, 
                                                pe3_jadwal_ujian_pmb.jam_selesai_ujian, 
                                                pe3_jadwal_ujian_pmb.tanggal_akhir_daftar, 
                                                pe3_jadwal_ujian_pmb.durasi_ujian, 
                                                pe3_jadwal_ujian_pmb.status_pendaftaran, 
                                                pe3_jadwal_ujian_pmb.status_ujian,                                                 
                                                pe3_jadwal_ujian_pmb.ruangkelas_id,
                                                pe3_ruangkelas.namaruang,
                                                0 AS jumlah_peserta,
                                                pe3_jadwal_ujian_pmb.created_at,
                                                pe3_jadwal_ujian_pmb.updated_at
                                            '))
                                            ->leftJoin('pe3_ruangkelas','pe3_ruangkelas.id','pe3_jadwal_ujian_pmb.ruangkelas_id')
                                            ->where('ta',$tahun_pendaftaran)
                                            ->where('idsmt',$semester_pendaftaran)
                                            ->orderBy('tanggal_akhir_daftar','desc')
                                            ->get();
        }
        
        
        
        $jumlah_bank_soal=SoalPMBModel::where('ta',$tahun_pendaftaran)
                                        ->where('semester',$semester_pendaftaran)
                                        ->count();

        $jadwal_ujian->transform(function ($item,$key)
        {
            $item->jumlah_peserta=PesertaUjianPMBModel::where('jadwal_ujian_id',$item->id)
                                ->count();
            return $item;
        });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'jadwal_ujian'=>$jadwal_ujian,      
                                    'jumlah_bank_soal'=>$jumlah_bank_soal,                                                                                                                             
                                    'message'=>'Fetch data jadwal ujian pmb berhasil.'
                                ],200);     
    }  
    /**
     * simpan jadwal ujian baru
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_STORE');

        $this->validate($request, [           
            'nama_kegiatan'=>'required',
            'jumlah_soal'=>'required',
            'tanggal_ujian'=>'required',
            'jam_mulai_ujian'=>'required',
            'jam_selesai_ujian'=>'required',
            'tanggal_akhir_daftar'=>'required',            
            'durasi_ujian'=>'required',            
            'ruangkelas_id'=>'required',
            'ta'=>'required',
            'idsmt'=>'required'
        ]);
        
        $jadwal_ujian=JadwalUjianPMBModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'nama_kegiatan'=>$request->input('nama_kegiatan'),
            'jumlah_soal'=>$request->input('jumlah_soal'),
            'tanggal_ujian'=>$request->input('tanggal_ujian'),
            'jam_mulai_ujian'=>$request->input('jam_mulai_ujian'),
            'jam_selesai_ujian'=>$request->input('jam_selesai_ujian'),
            'tanggal_akhir_daftar'=>$request->input('tanggal_akhir_daftar'),            
            'durasi_ujian'=>$request->input('durasi_ujian'),            
            'ruangkelas_id'=>$request->input('ruangkelas_id'),
            'ta'=> $request->input('ta'),
            'idsmt'=>$request->input('idsmt'),                        
        ]);            
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'jadwal_ujian'=>$jadwal_ujian,                                                                                                                                 
                                    'message'=>'Data jadwal ujian berhasil disimpan.'
                                ],200); 
    }
    /**
     * detail jadwal ujian pmb
     */
    public function show(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_SHOW');

        $jadwal_ujian=JadwalUjianPMBModel::find($id);
        if (is_null($jadwal_ujian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'show',                
                                    'message'=>["Fetch data jadwal ujian pmb dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                                          
                                        'jadwal_ujian'=>$jadwal_ujian,                                                                                                                                                                                                                                                                                                           
                                        'message'=>"Fetch data jadwal ujian pmb dengan id ($id) berhasil diperoleh."
                                    ],200);     
        }
    } 
    /**
     * update jadwal ujian pmb
     */
    public function update(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_UPDATE');

        $jadwal_ujian=JadwalUjianPMBModel::find($id);
        if (is_null($jadwal_ujian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Fetch data jadwal ujian pmb dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [           
                'nama_kegiatan'=>'required',
                'jumlah_soal'=>'required',
                'tanggal_ujian'=>'required',
                'jam_mulai_ujian'=>'required',
                'jam_selesai_ujian'=>'required',
                'tanggal_akhir_daftar'=>'required',            
                'durasi_ujian'=>'required',            
                'ruangkelas_id'=>'required',                
            ]);            
                
            $jadwal_ujian->nama_kegiatan=$request->input('nama_kegiatan');
            $jadwal_ujian->jumlah_soal=$request->input('jumlah_soal');
            $jadwal_ujian->tanggal_ujian=$request->input('tanggal_ujian');
            $jadwal_ujian->jam_mulai_ujian=$request->input('jam_mulai_ujian');
            $jadwal_ujian->jam_selesai_ujian=$request->input('jam_selesai_ujian');
            $jadwal_ujian->tanggal_akhir_daftar=$request->input('tanggal_akhir_daftar');
            $jadwal_ujian->durasi_ujian=$request->input('durasi_ujian');
            $jadwal_ujian->ruangkelas_id=$request->input('ruangkelas_id');
            $jadwal_ujian->save();
                 
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',  
                                        'jadwal_ujian'=>$jadwal_ujian,                                                                                                                                                                                                                                                                                                                                                    
                                        'message'=>"Mengubah data jadwal ujian pmb dengan id ($id) berhasil."                                        
                                    ],200);    
        }
    }     
    /**
     * update status ujian jadwal ujian pmb
     */
    public function updatestatusujian(Request $request,$id)
    {
        $jadwal_ujian=JadwalUjianPMBModel::find($id);
        if (is_null($jadwal_ujian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Fetch data jadwal ujian pmb dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [           
                'status_ujian'=>'required|integer|digits_between:1,3',                               
            ]);            
                
            $jadwal_ujian->status_ujian=$request->input('status_ujian');            
            $jadwal_ujian->save();
                 
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',  
                                        'jadwal_ujian'=>$jadwal_ujian,                                                                                                                                                                                                                                                                                                                                                    
                                        'message'=>"ujian pmb dengan id ($id) berhasil dimulai."                                        
                                    ],200);    
        }
    }

    /**
     * Menghapus jadwal ujian pmb
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_DESTROY');

        $jadwal_ujian=JadwalUjianPMBModel::find($id);
        
        if (is_null($jadwal_ujian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Menghapus jadwal ujian PMB dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $nama_kegiatan=$jadwal_ujian->nama_kegiatan;
            $jadwal_ujian->delete();

            \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $this->guard()->user(), 
                                                            'object_id' => $this->guard()->user()->id, 
                                                            'jadwal_ujian_id' => $jadwal_ujian->id, 
                                                            'message' => 'Menghapus Jadwal Ujian PMB ('.$nama_kegiatan.') berhasil'
                                                        ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Jadwal Ujian PMB ($nama_kegiatan) berhasil dihapus"
                                    ],200);         
        }
                  
    } 
}