<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Keuangan\KomponenBiayaModel;

class KomponenBiayaController extends Controller {  
    /**
     * daftar komponen biaya
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-KOMPONEN-BIAYA_BROWSE');

        $kombi=KomponenBiayaModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kombi'=>$kombi,                                                                                                                                   
                                    'message'=>'Fetch data komponen biaya berhasil.'
                                ],200);     
    }  
}