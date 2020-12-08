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

class ReportKelulusanController extends Controller {             
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang telah mengisi formulir pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasAnyPermission(['SPMB-PMB-LAPORAN-KELULUSAN_BROWSE']);

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required',
            'filter_status'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');
        $filter_status=$request->input('filter_status');

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
                    ->where('pe3_nilai_ujian_pmb.ket_lulus',$filter_status)
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
            $transaksi_detail=TransaksiDetailModel::select(\DB::raw('pe3_transaksi.no_transaksi,pe3_transaksi.status'))
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
                                        'no_transaksi'=>"$no_transaksi",                                                                           
                                        'transaksi_status'=>$transaksi_status,
                                        'daftar_prodi'=>$daftar_prodi,
                                        'kjur'=>$formulir->kjur1,                                        
                                        'data_nilai_ujian'=>$data_nilai_ujian,                                        
                                        'message'=>"Data nilai dengan ID ($id) berhasil diperoleh"
                                    ],200);        
        }

    }   
    /**
     * cetak ke excel
     *
     * @return \Illuminate\Http\Response
     */
    public function printtoexcel(Request $request)
    {   
        $this->hasAnyPermission(['SPMB-PMB-LAPORAN-KELULUSAN_BROWSE']);

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required',
            'nama_prodi'=>'required',
            'filter_status'=>'required'
        ]);
        
        $data_report=[
            'TA'=>$request->input('TA'),
            'prodi_id'=>$request->input('prodi_id'),            
            'nama_prodi'=>$request->input('nama_prodi'), 
            'filter_status'=>$request->input('filter_status'),            
        ];

        $report= new \App\Models\Report\ReportSPMBModel ($data_report);          
        return $report->kelulusan();
    }
}