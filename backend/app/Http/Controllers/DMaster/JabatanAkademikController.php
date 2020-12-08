<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\JabatanAkademikModel;

class JabatanAkademikController extends Controller {  
    /**
     * daftar jabatan akademik
     */
    public function index(Request $request)
    {
        $jabatan_akademik=JabatanAkademikModel::orderBy('id_jabatan','ASC')
                                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'jabatan_akademik'=>$jabatan_akademik,                                                                                                                                   
                                    'message'=>'Fetch data jabatan akademik berhasil.'
                                ],200);     
    }  
}