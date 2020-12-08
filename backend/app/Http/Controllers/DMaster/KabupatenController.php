<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\KabupatenModel;

class KabupatenController extends Controller {  
    /**
     * daftar kabupaten
     */
    public function index(Request $request)
    {
        $kabupaten=KabupatenModel::all();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kabupaten'=>$kabupaten,                                                                                                                                   
                                    'message'=>'Fetch data kabupaten berhasil.'
                                ],200);     
    }
    /**
     * daftar kecamatan
     */
    public function kecamatan(Request $request,$id)
    {
        $kabupaten=KabupatenModel::find($id);
        if (is_null($kabupaten))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data kecamatan berdasarkan id kabupaten gagal"]
                                ],422); 
        }
        else
        {
            $kecamatan = $kabupaten->kecamatan;
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'kecamatan'=>$kecamatan,                                                                                                                                   
                                        'message'=>'Fetch data kecamatan berdasarkan id kabupaten berhasil.'
                                    ],200);     

        }
    }
}