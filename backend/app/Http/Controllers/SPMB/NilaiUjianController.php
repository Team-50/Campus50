<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\SPMB\NilaiUjianPMBModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

use Ramsey\Uuid\Uuid;

class NilaiUjianController extends Controller {             
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang telah mengisi formulir pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_BROWSE']);

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');

        $data = FormulirPendaftaranModel::select(\DB::raw('
                        users.id,
                        pe3_formulir_pendaftaran.no_formulir,
                        users.name,
                        users.nomor_hp,
                        COALESCE(pe3_nilai_ujian_pmb.nilai,\'N.A\') AS nilai,
                        pe3_nilai_ujian_pmb.ket_lulus,
                        CASE
                            WHEN pe3_nilai_ujian_pmb.ket_lulus IS NULL THEN \'N.A\'
                            WHEN pe3_nilai_ujian_pmb.ket_lulus=0 THEN \'TIDAK LULUS\'
                            WHEN pe3_nilai_ujian_pmb.ket_lulus=1 THEN \'LULUS\'
                        END AS status,
                        pe3_kelas.nkelas,
                        users.active,
                        users.foto,
                        users.created_at,
                        users.updated_at
                    '))
                    ->join('users','pe3_formulir_pendaftaran.user_id','users.id')                    
                    ->join('pe3_kelas','pe3_formulir_pendaftaran.idkelas','pe3_kelas.idkelas')                    
                    ->leftJoin('pe3_nilai_ujian_pmb','pe3_formulir_pendaftaran.user_id','pe3_nilai_ujian_pmb.user_id')                    
                    ->where('users.ta',$ta)
                    ->where('kjur1',$prodi_id)            
                    ->whereNotNull('pe3_formulir_pendaftaran.idkelas')   
                    ->where('users.active',1)    
                    ->orderBy('users.name','ASC') 
                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'pmb'=>$data,
                                'message'=>'Fetch data calon mahasiswa baru berhasil diperoleh'
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
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_STORE']);

        $kjur=$request->input('kjur');
        $this->validate($request, [
            'user_id'=>[
                        'required',
                        Rule::exists('pe3_formulir_pendaftaran')->where(function ($query) use ($kjur) {
                            return $query->whereNotNull('idkelas')
                                        ->where('kjur1',$kjur);
                        })
                    ],            
            'no_transaksi'=>[
                        'required',
                        Rule::exists('pe3_transaksi')->where(function ($query) {
                            return $query->where('status',1);
                        })
                    ],   
            'nilai'=>'required|numeric',            
            'kjur'=>'required',            
            'ket_lulus'=>'required',            
        ]);
        $data_nilai = \DB::transaction(function () use ($request){
            $user_id=$request->input('user_id');
            $ket_lulus=$request->input('ket_lulus');
            $data_nilai=NilaiUjianPMBModel::create([
                'user_id'=>$user_id,
                'jadwal_ujian_id'=>null,
                'jumlah_soal'=>null,
                'jawaban_benar'=>null,
                'jawaban_salah'=>null,
                'soal_tidak_terjawab'=>null,
                'passing_grade_1'=>null,
                'passing_grade_2'=>null,
                'nilai'=>$request->input('nilai'),
                'kjur'=>$request->input('kjur'),
                'ket_lulus'=>$ket_lulus,
                'desc'=>$request->input('desc'),
            ]);          
            $keterangan=$ket_lulus == 0 ? 'TIDAK LULUS' : 'LULUS';

            \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $data_nilai, 
                                                            'object_id' => $data_nilai->user_id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message'=>"Menyimpan status kelulusan Mahasiswa Baru ($keterangan) berhasil dilakukan."
                                                        ]);
            if ($ket_lulus==1)
            {        
                $formulir=$data_nilai->formulir;       
                $this->createTransaksiDulang($formulir);
                $this->createTransaksiSPP($formulir);
                
            }

            return $data_nilai;
        });        
        $keterangan=$data_nilai->ket_lulus == 0 ? 'TIDAK LULUS' : 'LULUS';
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'data_nilai'=>$data_nilai,                                    
                                    'message'=>"Menyimpan status kelulusan Mahasiswa Baru ($keterangan) berhasil dilakukan."
                                ],200); 

    }      
    /**
     * Detail nilai dan jadwal ujian
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_SHOW']);

        $formulir=FormulirPendaftaranModel::select(\DB::raw('   
                                                            pe3_formulir_pendaftaran.user_id,                                                       
                                                            kjur1,
                                                            CONCAT(pe3_prodi.nama_prodi,\'(\',pe3_prodi.nama_jenjang,\')\') AS nama_prodi
                                                        '))
                                            ->join('users','users.id','pe3_formulir_pendaftaran.user_id')
                                            ->join('pe3_prodi','pe3_prodi.id','pe3_formulir_pendaftaran.kjur1')                                            
                                            ->find($id);
        if (is_null($formulir))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $transaksi_detail=TransaksiDetailModel::select(\DB::raw('
                                                        pe3_transaksi.no_transaksi,
                                                        pe3_transaksi.status
                                                    '))
                                                    ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                                                    ->where('pe3_transaksi.user_id',$formulir->user_id)
                                                    ->where('kombi_id',101)                                                    
                                                    ->first(); 

            $transaksi_status=0;
            $no_transaksi='N.A';
            if (!is_null($transaksi_detail))
            {
                $no_transaksi=$transaksi_detail->no_transaksi;
                $transaksi_status=$transaksi_detail->status;
            }             
            $daftar_prodi[]=['prodi_id'=>$formulir->kjur1,'nama_prodi'=>$formulir->nama_prodi];
            $data_nilai_ujian=NilaiUjianPMBModel::find($id);                     
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                                                        
                                        'no_transaksi'=>"$no_transaksi ",                                                                           
                                        'transaksi_status'=>$transaksi_status,
                                        'daftar_prodi'=>$daftar_prodi,
                                        'kjur'=>$formulir->kjur1,                                        
                                        'data_nilai_ujian'=>$data_nilai_ujian,                                        
                                        'message'=>"Data nilai dengan ID ($id) berhasil diperoleh"
                                    ],200);        
        }

    }   
    /**
     * update formulir pendaftaran
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_UPDATE']);

        $data_nilai=NilaiUjianPMBModel::find($id);

        if (is_null($data_nilai))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Nilai ujian dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [                         
                'no_transaksi'=>[
                            'required',
                            Rule::exists('pe3_transaksi')->where(function ($query) {
                                return $query->where('status',1);
                            })
                        ],   
                'nilai'=>'required|numeric',            
                'kjur'=>'required',            
                'ket_lulus'=>'required',            
            ]);
            $data_nilai = \DB::transaction(function () use ($request,$data_nilai){
                $ket_lulus=$request->input('ket_lulus');
                $data_nilai->nilai=$request->input('nilai');
                $data_nilai->kjur=$request->input('kjur');
                $data_nilai->ket_lulus=$ket_lulus;
                $data_nilai->desc=$request->input('desc');
                $data_nilai->save();
                
                $keterangan=$ket_lulus == 0 ? 'TIDAK LULUS' : 'LULUS';

                \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $data_nilai, 
                                                            'object_id' => $data_nilai->user_id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message'=>"Mengubah status kelulusan Mahasiswa Baru menjadi ($keterangan) berhasil dilakukan."
                                                        ]);
                if ($ket_lulus==1)
                {        
                    $formulir=$data_nilai->formulir;       
                    $this->createTransaksiDulang($formulir);
                    $this->createTransaksiSPP($formulir);
                    
                }   
                return $data_nilai;
            });
            $keterangan=$data_nilai->ket_lulus == 0 ? 'TIDAK LULUS' : 'LULUS';
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'store',
                                        'data_nilai'=>$data_nilai,
                                        'message'=>"Mengubah status kelulusan Mahasiswa Baru menjadi ($keterangan) berhasil dilakukan."
                                    ],200); 
        }
    }           
    /**
     * Menghapus data nilai ujian sekaligus pendaftaran
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_DESTROY']);

        $data_nilai = NilaiUjianPMBModel::where('ket_lulus',0)
                    ->find($id); 
        
        if (is_null($data_nilai))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Nilai Ujian dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $name=$data_nilai->name;
            $data_nilai->delete();

            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $this->guard()->user(), 
                                                                'object_id' => $this->guard()->user()->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Data nilai ujian pmb dengan user id ('.$data_nilai->user_id.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message' => 'Menghapus Data nilai ujian pmb dengan user id ('.$data_nilai->user_id.') berhasil'
                                    ],200);         
        }
                  
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