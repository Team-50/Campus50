<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\JenjangStudiModel;

class JenjangStudiController extends Controller {  
    /**
     * daftar jenjang studi
     */
    public function index(Request $request)
    {
        $jenjang_studi=JenjangStudiModel::orderBy('kode_jenjang','ASC')
                                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'jenjang_studi'=>$jenjang_studi,                                                                                                                                   
                                    'message'=>'Fetch data jenjang studi berhasil.'
                                ],200);     
    }  
}