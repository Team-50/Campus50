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

class ReportSPMBProdiController extends Controller {         
    /**
     * cetak ke excel
     *
     * @return \Illuminate\Http\Response
     */
    public function printtoexcel(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-LAPORAN-PRODI_BROWSE');

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

        $report= new \App\Models\Report\ReportSPMBModel ($data_report);          
        return $report->prodi();
    }
}