<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\System\ConfigurationModel;
use App\Helpers\Helper;
use App\Mail\MahasiswaBaruRegistered;
use App\Mail\VerifyEmailAddress;

use Ramsey\Uuid\Uuid;

class ReportSPMBFakultasController extends Controller {     
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang telah mengisi formulir pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-LAPORAN-FAKULTAS_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'fakultas_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $fakultas_id=$request->input('fakultas_id');

        $data = User::where('default_role','mahasiswabaru')
                    ->select(\DB::raw('users.id,users.name,users.nomor_hp,pe3_kelas.nkelas,users.active,users.foto,users.created_at,users.updated_at'))
                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','users.id')
                    ->join('pe3_kelas','pe3_formulir_pendaftaran.idkelas','pe3_kelas.idkelas')
                    ->join('pe3_prodi','pe3_prodi.id','pe3_formulir_pendaftaran.kjur1')                                    
                    ->where('users.ta',$ta)
                    ->where('kode_fakultas',$fakultas_id)                    
                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'pmb'=>$data,
                                'message'=>'Fetch data calon mahasiswa baru berhasil diperoleh'
                            ],200);  
    }      
    /**
     * cetak ke excel
     *
     * @return \Illuminate\Http\Response
     */
    public function printtoexcel(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-LAPORAN-FAKULTAS_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'fakultas_id'=>'required',
            'nama_fakultas'=>'required',
        ]);
        
        $data_report=[
            'TA'=>$request->input('TA'),
            'fakultas_id'=>$request->input('fakultas_id'),            
            'nama_fakultas'=>$request->input('nama_fakultas'),            
        ];

        $report= new \App\Models\Report\ReportSPMBModel ($data_report);          
        return $report->fakultas();
    }
}