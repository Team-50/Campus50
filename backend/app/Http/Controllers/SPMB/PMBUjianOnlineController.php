<?php

namespace App\Http\Controllers\SPMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SPMB\JadwalUjianPMBModel;
use App\Models\SPMB\PesertaUjianPMBModel;
use App\Models\SPMB\SoalPMBModel;
use App\Models\SPMB\JawabanUjianPMBModel;
use App\Models\SPMB\NilaiUjianPMBModel;
use App\Models\SPMB\PMBPassingGradeModel;

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
        else if ($peserta->isfinish)        
        {
            return Response()->json([
                                        'status'=>0,
                                        'pid'=>'fetchdata',                
                                        'message'=>['Jadwal Ujian PMB ini sudah dinyatakan selesai, oleh karena itu tidak bisa dilanjutkan']
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
        
        // $is_bayar = \DB::table('pe3_transaksi')
        //                 ->join('pe3_transaksi_detail','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
        //                 ->where('kombi_id',101)
        //                 ->where('status',1)
        //                 ->where('pe3_transaksi_detail.user_id', $request->input('user_id'))
        //                 ->exists();
        $is_bayar = true;
        if ($is_bayar)
        {
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
        else
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'store',  
                                    'message'=>'Mendaftarkan peserta ujian pmb ke jadwal ujian gagal karena belum melakukan pembayaran.'
                                ],422);    
        }     
    
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
     * digunakan untuk keluar dari sebuah jadwal ujian
     */
    public function keluar (Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-JADWAL-UJIAN_DESTROY');

        $this->validate($request,[
            'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',            
        ]);

        \DB::transaction(function () use ($request) {

            \DB::table('pe3_jawaban_ujian')
                ->where('user_id', $request->input('user_id'))
                ->delete();

            \DB::table('pe3_peserta_ujian_pmb')
                ->where('user_id', $request->input('user_id'))
                ->delete();
        });

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Peserta berhasil dihapus dari jadwal ujian ini"
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
        
        $user_id = $request->input('user_id');
        $isfinish=PesertaUjianPMBModel::where('user_id',$user_id)
                                        ->where('isfinish',1)
                                        ->exists();
        if ($isfinish)
        {
            return Response()->json([
                                        'status'=>0,
                                        'pid'=>'store',                                                                                                                                    
                                        'message'=>['Jadwal Ujian PMB ini sudah dinyatakan selesai, oleh karena itu tidak bisa dilanjutkan']
                                    ], 422); 
        } 
        else
        {
            $jawaban_ujian = JawabanUjianPMBModel::create([
                                                            'id'=>Uuid::uuid4()->toString(),
                                                            'user_id'=>$user_id,
                                                            'soal_id'=>$request->input('soal_id'),
                                                            'jawaban_id'=>$request->input('jawaban_id'),
                                                        ]);
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'store',                                                                                                                                    
                                        'message'=>'Data Jawaban Ujian berhasil disimpan.'
                                    ],200); 
        }               
    }  
    /**
     * digunakan untuk menyimpan jawaban ujian
     */
    public function selesaiujian (Request $request)
    {
        $this->validate($request,[
            'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',                               
        ]);
        
        $peserta = \DB::transaction(function () use ($request) {

            $user_id = $request->input('user_id');
            $peserta = PesertaUjianPMBModel::find($user_id);        
            $jadwalujian = $peserta->jadwalujian;
            
            $jadwal_ujian_id = $peserta->jadwal_ujian_id;

            $passing_grade = PMBPassingGradeModel::where('jadwal_ujian_id',$jadwal_ujian_id)
                                                    ->first();

            $jawaban = JawabanUjianPMBModel::select(\DB::raw('
                                                pe3_jawaban_soal.`status`,
                                                COUNT(pe3_jawaban_ujian.id) AS jumlah
                                            '))
                                            ->join('pe3_jawaban_soal','pe3_jawaban_soal.id','pe3_jawaban_ujian.jawaban_id')
                                            ->where('user_id',$user_id)
                                            ->groupBy('pe3_jawaban_soal.status')
                                            ->orderBy('pe3_jawaban_soal.status','asc')
                                            ->get();

            $salah = 0;
            $benar = 0;
            foreach($jawaban as $v)
            {
                switch($v->status)
                {
                    case 0:
                        $salah = $v->jumlah;
                    break;
                    case 1:
                        $benar = $v->jumlah;
                    break;
                }
            }
            $jumlah_soal = $jadwalujian->jumlah_soal;            
            $nilai = \App\Helpers\Helper::formatPersen($benar,$jumlah_soal);            
            $nilai_passing_grade = is_null($passing_grade)?0:$passing_grade->nilai;
            NilaiUjianPMBModel::create([
                'user_id'=>$user_id,
                'jadwal_ujian_id'=>$jadwal_ujian_id,
                'jumlah_soal'=>$jumlah_soal,
                'jawaban_benar'=>$benar,
                'jawaban_salah'=>$salah,
                'soal_tidak_terjawab'=>$jumlah_soal - ($benar+$salah),
                'passing_grade_1'=>$nilai_passing_grade,
                'passing_grade_2'=>0,
                'nilai'=>$nilai,
                'ket_lulus'=>(($nilai>$nilai_passing_grade)?1:0),
                'desc'=>'Nilai beserta kelulusan dihitung otomatis oleh sistem'
            ]);           

            $peserta->selesai_ujian=\Carbon\Carbon::now()->toDateTimeString();
            $peserta->isfinish=1;
            $peserta->save();

            return $peserta;
        });

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',  
                                'peserta'=>$peserta,
                                'message'=>'peserta ujian berhasil menyelesaikan ujian pmb.'
                            ],200);
    }
    public function recalculate(Request $request)
    {
        $this->validate($request,[
            'user_id'=>'required|exists:pe3_peserta_ujian_pmb,user_id',                               
        ]);
        
        $peserta = \DB::transaction(function () use ($request) {

            $user_id = $request->input('user_id');
            $peserta = PesertaUjianPMBModel::find($user_id);        
            $jadwalujian = $peserta->jadwalujian;
            
            $jadwal_ujian_id = $peserta->jadwal_ujian_id;

            $passing_grade = PMBPassingGradeModel::where('jadwal_ujian_id',$jadwal_ujian_id)
                                                    ->first();

            $jawaban = JawabanUjianPMBModel::select(\DB::raw('
                                                pe3_jawaban_soal.`status`,
                                                COUNT(pe3_jawaban_ujian.id) AS jumlah
                                            '))
                                            ->join('pe3_jawaban_soal','pe3_jawaban_soal.id','pe3_jawaban_ujian.jawaban_id')
                                            ->where('user_id',$user_id)
                                            ->groupBy('pe3_jawaban_soal.status')
                                            ->orderBy('pe3_jawaban_soal.status','asc')
                                            ->get();

            $salah = 0;
            $benar = 0;
            foreach($jawaban as $v)
            {
                switch($v->status)
                {
                    case 0:
                        $salah = $v->jumlah;
                    break;
                    case 1:
                        $benar = $v->jumlah;
                    break;
                }
            }
            $jumlah_soal = $jadwalujian->jumlah_soal;            
            $nilai = \App\Helpers\Helper::formatPersen($benar,$jumlah_soal);            
            $nilai_passing_grade = is_null($passing_grade)?0:$passing_grade->nilai;
            $nilai_ujian = NilaiUjianPMBModel::find($user_id);
            if(is_null($nilai_ujian))
            {
                NilaiUjianPMBModel::create([
                    'user_id'=>$user_id,
                    'jadwal_ujian_id'=>$jadwal_ujian_id,
                    'jumlah_soal'=>$jumlah_soal,
                    'jawaban_benar'=>$benar,
                    'jawaban_salah'=>$salah,
                    'soal_tidak_terjawab'=>$jumlah_soal - ($benar+$salah),
                    'passing_grade_1'=>$nilai_passing_grade,
                    'passing_grade_2'=>0,
                    'nilai'=>$nilai,
                    'ket_lulus'=>(($nilai>$nilai_passing_grade)?1:0),
                    'desc'=>'Nilai beserta kelulusan dihitung otomatis oleh sistem'
                ]);           
            }
            else
            {
                $nilai_ujian->jawaban_benar = $benar;
                $nilai_ujian->jawaban_salah = $salah;
                $nilai_ujian->soal_tidak_terjawab = $jumlah_soal - ($benar+$salah);
                $nilai_ujian->nilai = $nilai;
                $nilai_ujian->ket_lulus = (($nilai>$nilai_passing_grade)?1:0);
                $nilai_ujian->desc = 'Nilai beserta kelulusan dihitung otomatis oleh sistem';
                $nilai_ujian->save();
            }

            $peserta->selesai_ujian=\Carbon\Carbon::now()->toDateTimeString();
            $peserta->isfinish=1;
            $peserta->save();

            return $peserta;
        });

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',  
                                'peserta'=>$peserta,
                                'message'=>'peserta ujian berhasil menyelesaikan ujian pmb.'
                            ],200);
    }
}