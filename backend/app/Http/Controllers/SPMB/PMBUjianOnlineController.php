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
use App\Models\SPMB\FormulirPendaftaranModel;

use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

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
            $jadwal_ujian=$peserta->jadwalujian;
            $jumlah_soal=$jadwal_ujian->jumlah_soal;

            $soal=null;
            $jumlah_soal_terjawab=\DB::table('pe3_jawaban_ujian')
                                ->where('user_id',$id)
                                ->count();
            
            if ($jumlah_soal_terjawab<$jumlah_soal)
            {
                $soal=SoalPMBModel::select(\DB::raw('id,soal,gambar'))
                                ->whereNotIn('id',function($query) use ($id){
                                    $query->select('soal_id')
                                            ->from('pe3_jawaban_ujian')
                                            ->where('user_id',$id);
                                })
                                ->inRandomOrder()
                                ->first();
            }            
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
     * digunakan untuk mendapatkan daftar jadwal ujian yang belum selesai
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
                                            ->whereRaw('(status_ujian=0 OR status_ujian=1)')
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
            $jadwal_ujian = $peserta->jadwalujian;
            $nilai = $peserta->nilaiujian;

            if ($peserta->isfinish == 1 && is_null($nilai))
            {
                $this->hitungNilaiUjian($peserta->user_id);
                $nilai = NilaiUjianPMBModel::find($peserta->user_id);                            
            }
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'peserta'=>$peserta,
                                    'jadwal_ujian'=>$jadwal_ujian,
                                    'nilai'=>$nilai,
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
        
        $peserta = $this->hitungNilaiUjian($request->input('user_id'));

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
        
        $peserta = $this->hitungNilaiUjian($request->input('user_id'));

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',  
                                'peserta'=>$peserta,
                                'message'=>'peserta ujian berhasil menyelesaikan ujian pmb.'
                            ],200);
    }
    private function hitungNilaiUjian($user_id)
    {
        $peserta = \DB::transaction(function () use ($user_id) {
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

            $formulir = FormulirPendaftaranModel::find($user_id);
            $kjur = $nilai > $nilai_passing_grade ? $formulir->kjur1 : null;
            $ket_lulus = $nilai > $nilai_passing_grade;

            $kjur = $nilai > $nilai_passing_grade ? $formulir->kjur1 : null;
            
            NilaiUjianPMBModel::where('user_id',$user_id)
                                ->delete();

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
                'ket_lulus'=>$ket_lulus,
                'kjur'=>$kjur,
                'desc'=>'Dihitung otomatis oleh sistem'
            ]);

            $peserta->selesai_ujian=\Carbon\Carbon::now()->toDateTimeString();
            $peserta->isfinish=1;
            $peserta->save();
            
            if ($ket_lulus)
            {
                $this->createTransaksiDulang($formulir);
                $this->createTransaksiSPP($formulir);
            }
            return $peserta;
        });

        return $peserta;
    }
    //buat transaksi keuangan daftar ulang 
    private function createTransaksiDulang($formulir)
    {
        $transaksi_detail=TransaksiDetailModel::where('user_id',$formulir->user_id)->where('kombi_id',102)->first();                
        if (is_null($transaksi_detail))
        {   
            $kombi=\App\Models\Keuangan\BiayaKomponenPeriodeModel::where('kombi_id',102)
                                                                ->where('kjur',$formulir->kjur1)
                                                                ->where('idkelas',$formulir->idkelas)
                                                                ->where('tahun',$formulir->ta)
                                                                ->first();
            if (!is_null($kombi))
            {                
                $no_transaksi='102'.date('YmdHms');
                $transaksi=TransaksiModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$formulir->user_id,
                    'no_transaksi'=>$no_transaksi,
                    'no_faktur'=>'',
                    'kjur'=>$formulir->kjur1,
                    'ta'=>$formulir->ta,
                    'idsmt'=>$formulir->idsmt,
                    'idkelas'=>$formulir->idkelas,
                    'no_formulir'=>$formulir->no_formulir,
                    'nim'=>$formulir->nim,
                    'commited'=>0,
                    'total'=>0,
                    'tanggal'=>date('Y-m-d'),
                ]);  
                
                $transaksi_detail=TransaksiDetailModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$formulir->user_id,
                    'transaksi_id'=>$transaksi->id,
                    'no_transaksi'=>$transaksi->no_transaksi,
                    'kombi_id'=>$kombi->kombi_id,
                    'nama_kombi'=>$kombi->nama_kombi,
                    'biaya'=>$kombi->biaya,
                    'jumlah'=>1,
                    'sub_total'=>$kombi->biaya    
                ]);
                $transaksi->total=$kombi->biaya;
                $transaksi->desc=$kombi->nama_kombi;
                $transaksi->save();
            }
        }        
        
    }   
    //buat transaksi spp      
    private function createTransaksiSPP($formulir)
    {            
        $mulai_bulan_pembayaran=9;
        $transaksi_detail=TransaksiDetailModel::where('user_id',$formulir->user_id)
                                                ->where('kombi_id',201)
                                                ->where('bulan',$mulai_bulan_pembayaran)
                                                ->first();       

        if (is_null($transaksi_detail))
        {   
            $kombi=\App\Models\Keuangan\BiayaKomponenPeriodeModel::where('kombi_id',201)
                                                                ->where('kjur',$formulir->kjur1)
                                                                ->where('idkelas',$formulir->idkelas)
                                                                ->where('tahun',$formulir->ta)
                                                                ->first();
            if (!is_null($kombi))
            {                
                $no_transaksi='201'.date('YmdHms');
                $transaksi=TransaksiModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$formulir->user_id,
                    'no_transaksi'=>$no_transaksi,
                    'no_faktur'=>'',
                    'kjur'=>$formulir->kjur1,
                    'ta'=>$formulir->ta,
                    'idsmt'=>$formulir->idsmt,
                    'idkelas'=>$formulir->idkelas,
                    'no_formulir'=>$formulir->no_formulir,
                    'nim'=>$formulir->nim,
                    'commited'=>0,
                    'total'=>0,
                    'tanggal'=>date('Y-m-d'),
                ]);  
                
                $transaksi_detail=TransaksiDetailModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$formulir->user_id,
                    'transaksi_id'=>$transaksi->id,
                    'no_transaksi'=>$transaksi->no_transaksi,
                    'kombi_id'=>$kombi->kombi_id,
                    'nama_kombi'=>$kombi->nama_kombi,
                    'biaya'=>$kombi->biaya,
                    'jumlah'=>1,
                    'bulan'=>$mulai_bulan_pembayaran,
                    'sub_total'=>$kombi->biaya    
                ]);
                $transaksi->total=$kombi->biaya;
                $transaksi->desc=$kombi->nama_kombi;
                $transaksi->save();
            }
        }
    }     
}