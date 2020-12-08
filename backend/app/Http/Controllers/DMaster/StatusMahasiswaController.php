<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\StatusMahasiswaModel;

class StatusMahasiswaController extends Controller {  
    /**
     * daftar status mahasiswa
     */
    public function index(Request $request)
    {
        $status_mahasiswa=StatusMahasiswaModel::orderBy('k_status','ASC')
                                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'status_mahasiswa'=>$status_mahasiswa,                                                                                                                                   
                                    'message'=>'Fetch data status mahasiswa berhasil.'
                                ],200);     
    }  
}