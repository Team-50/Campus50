<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\KecamatanModel;

class KecamatanController extends Controller {  
    /**
     * daftar kecamatan
     */
    public function index(Request $request)
    {
        $kecamatan=KecamatanModel::all();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kecamatan'=>$kecamatan,                                                                                                                                   
                                    'message'=>'Fetch data kecamatan berhasil.'
                                ],200);     
    }
    /**
     * daftar kecamatan
     */
    public function desa(Request $request,$id)
    {
        $kecamatan=KecamatanModel::find($id);
        if (is_null($kecamatan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data desa berdasarkan id kecamatan gagal"]
                                ],422); 
        }
        else
        {
            $desa = $kecamatan->desa;
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'desa'=>$desa,                                                                                                                                   
                                        'message'=>'Fetch data desa berdasarkan id kecamatan berhasil.'
                                    ],200);     

        }
    }
}