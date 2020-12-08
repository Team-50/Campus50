<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Akademik\GroupMatakuliahModel;

class GroupMatakuliahController extends Controller {  
    /**
     * daftar group matakuliah
     */
    public function index(Request $request)
    {
        $group_matakuliah=GroupMatakuliahModel::all();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'group_matakuliah'=>$group_matakuliah,                                                                                                                                   
                                    'message'=>'Fetch data group matakuliah berhasil.'
                                ],200);     
    }
}