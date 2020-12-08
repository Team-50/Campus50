<?php

namespace App\Http\Controllers\Akademik;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Akademik\RegisterMahasiswaModel;

class KemahasiswaanDaftarMahasiswaController  extends Controller 
{
/**
     * daftar mahasiswa
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-KEMAHASISWAAN-DAFTAR-MAHASISWA_BROWSE');

        $this->validate($request, [           
            'ta'=>'required',
            'prodi_id'=>'required'
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        
        $data = RegisterMahasiswaModel::select(\DB::raw('        
                                pe3_register_mahasiswa.user_id,
                                pe3_formulir_pendaftaran.no_formulir,
                                pe3_register_mahasiswa.nim,
                                pe3_register_mahasiswa.nirm,
                                pe3_formulir_pendaftaran.nama_mhs,                                
                                pe3_register_mahasiswa.idkelas,   
                                pe3_register_mahasiswa.k_status,
                                pe3_register_mahasiswa.created_at,                                      
                                pe3_register_mahasiswa.updated_at                                      
                            '))
                            ->join('pe3_formulir_pendaftaran','pe3_register_mahasiswa.user_id','pe3_formulir_pendaftaran.user_id')                                                    
                            ->where('pe3_register_mahasiswa.kjur',$prodi_id)                            
                            ->where('pe3_register_mahasiswa.tahun',$ta)                            
                            ->orderBy('nama_mhs','desc')
                            ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'mahasiswa'=>$data,                                                                                                                                   
                                    'message'=>'Fetch data daftar mahasiswa berhasil.'
                                ],200);     
    }
    /**
     * cetak ke excel
     *
     * @return \Illuminate\Http\Response
     */
    public function printtoexcel(Request $request)
    {   
        $this->hasPermissionTo('AKADEMIK-KEMAHASISWAAN-DAFTAR-MAHASISWA_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required',
            'nama_prodi'=>'required',
        ]);
        
        $data_report=[
            'TA'=>$request->input('TA'),
            'prodi_id'=>$request->input('prodi_id'),            
            'nama_prodi'=>$request->input('nama_prodi'),            
        ];

        $report= new \App\Models\Report\ReportKemahasiswaanModel ($data_report);          
        return $report->daftarmahasiswa();
    }
}