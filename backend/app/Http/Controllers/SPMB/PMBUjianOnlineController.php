<?php

namespace App\Http\Controllers\SPMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SPMB\JadwalUjianPMBModel;
use App\Models\SPMB\PesertaUjianPMBModel;
use App\Models\SPMB\SoalPMBModel;
use App\Models\SPMB\JawabanUjianPMBModel;

use Ramsey\Uuid\Uuid;

class PMBUjianOnlineController extends Controller {  
    /**
     * daftar soal berdasarkan user id
     */
    public function soal(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB-UJIAN-ONLINE_BROWSE');
                
        $peserta=PesertaUjianPMBModel::find($id);

        if (is_null($peserta))
        {
            return Response()->json([
                                        'status'=>0,
                                        'pid'=>'fetchdata',                
                                        'message'=>["Peserta Ujian dengan ID ($id) gagal diperoleh, mungkin belum mendaftar"]
                                    ],422); 
        }
        else
        {
            $soal=SoalPMBModel::select(\DB::raw('id,soal'))
                            ->whereNotIn('id',function($query) use ($id){
                                $query->select('soal_id')
                                        ->from('pe3_jawaban_ujian')
                                        ->where('user_id',$id);
                            })
                            ->inRandomOrder()
                            ->first();
            
            if (is_null($soal))
            {
                $status=0;
                $soal='';
                $jawaban=[];
            }
            else
            {
                $status=1;
                $jawaban=$soal->jawabanUjian;
            }           
            return Response()->json([
                                    'status'=>$status,
                                    'pid'=>'fetchdata',  
                                    'soal'=>$soal,     
                                    'jawaban'=>$jawaban,
                                    'peserta'=>$peserta,                                                                                                                              
                                    'message'=>'Fetch data soal pmb berhasil.'
                                ],200);     
        }        
    }  
    /**
     * digunakan untuk mendapatkan daftar jadwal ujian
     */
    public function jadwal (Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_BROWSE');

        $this->validate($request, [                       
            'tahun_pendaftaran'=>'required',
            'semester_pendaftaran'=>'required'
        ]);
        $tahun_pendaftaran=$request->input('tahun_pendaftaran');
        $semester_pendaftaran=$request->input('semester_pendaftaran');

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
                                                pe3_jadwal_ujian_pmb.created_at,
                                                pe3_jadwal_ujian_pmb.updated_at
                                            '))
                                            ->leftJoin('pe3_ruangkelas','pe3_ruangkelas.id','pe3_jadwal_ujian_pmb.ruangkelas_id')
                                            ->where('ta',$tahun_pendaftaran)
                                            ->where('idsmt',$semester_pendaftaran)
                                            ->whereRaw('CURRENT_DATE() <= pe3_jadwal_ujian_pmb.tanggaL_akhir_daftar')
                                            ->orderBy('tanggal_akhir_daftar','desc')
                                            ->get();

        $jumlah_bank_soal=SoalPMBModel::where('ta',$tahun_pendaftaran)
                                        ->where('semester',$semester_pendaftaran)
                                        ->count();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'jadwal_ujian'=>$jadwal_ujian,      
                                    'jumlah_bank_soal'=>$jumlah_bank_soal,                                                                                                                             
                                    'message'=>'Fetch data jadwal ujian pmb berhasil.'
                                ],200);     
    }
    /**
     * digunakan untuk mendapatkan profil peserta ujian
     */
    public function peserta (Request $request,$id)
    {   
        $peserta=PesertaUjianPMBModel::find($id);
        
        if (is_null($peserta))
        {
            return Response()->json([
                                        'status'=>0,
                                        'pid'=>'fetchdata', 
                                        'jadwal_ujian'=>null, 
                                        'peserta'=>$peserta,
                                        'message'=>'Fetch data peserta ujian pmb gagal, mungkin belum terdaftar.'
                                    ],200); 
        }
        else
        {   
            $jadwal_ujian=$peserta->jadwalujian;
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'peserta'=>$peserta,
                                    'jadwal_ujian'=>$jadwal_ujian,
                                    'message'=>'Fetch data peserta ujian pmb berhasil.'
                                ],200);     
        }
    }
    /**
     * digunakan untuk mendapatkan profil peserta ujian
     */
    public function daftarujian (Request $request)
    {   
        
        $this->validate($request,[
            'user_id'=>'required|exists:users,id',
            'jadwal_ujian_id'=>'required|exists:pe3_jadwal_ujian_pmb,id',                    
        ]);
        
        $jadwal_ujian_id=$request->input('jadwal_ujian_id');
        $no_peserta=\DB::table('pe3_peserta_ujian_pmb')->where('jadwal_ujian_id',$jadwal_ujian_id)->count()+1;
        $peserta=PesertaUjianPMBModel::create([
            'user_id'=>$request->input('user_id'),
            'no_peserta'=>$no_peserta,
            'jadwal_ujian_id'=>$jadwal_ujian_id,            
        ]);

        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',  
                                'peserta'=>$peserta,
                                'message'=>'Mendaftarkan peserta ujian pmb ke jadwal ujian berhasil.'
                            ],200);     
    
    }
    /**
     * digunakan untuk memulai ujian 
     */
    public function mulaiujian (Request $request)
    {
        $this->validate($request,[
            'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',                               
        ]);
        
        $peserta =PesertaUjianPMBModel::find($request->input('user_id'));        
        $peserta->mulai_ujian=\Carbon\Carbon::now()->toDateTimeString();
        $peserta->save();

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',  
                                'peserta'=>$peserta,
                                'message'=>'peserta ujian memulai ujian pmb berhasil.'
                            ],200);
    }
    /**
     * digunakan untuk menyimpan jawaban ujian
     */
    public function store (Request $request)
    {
        $this->validate($request,[
                                  'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',
                                  'soal_id'=>'required|exists:pe3_soal,id',
                                  'jawaban_id'=>'required|exists:pe3_jawaban_soal,id',
                                ]);
        
        
        $jawaban_ujian = JawabanUjianPMBModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'user_id'=>$request->input('user_id'),
            'soal_id'=>$request->input('soal_id'),
            'jawaban_id'=>$request->input('jawaban_id'),
        ]);
        
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',                                                                                                                                    
                                    'message'=>'Data Jawaban Ujian berhasil disimpan.'
                                ],200); 
    }  
    /**
     * digunakan untuk menyimpan jawaban ujian
     */
    public function selesaiujian (Request $request)
    {
        $this->validate($request,[
            'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',                               
        ]);
        
        $peserta =PesertaUjianPMBModel::find($request->input('user_id'));        
        $peserta->selesai_ujian=\Carbon\Carbon::now()->toDateTimeString();
        $peserta->isfinish=1;
        $peserta->save();

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',  
                                'peserta'=>$peserta,
                                'message'=>'peserta ujian berhasil menyelesaikan ujian pmb.'
                            ],200);
    }
}