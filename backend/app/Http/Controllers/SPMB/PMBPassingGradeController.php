<?php

namespace App\Http\Controllers\SPMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SPMB\PMBPassingGradeModel;
use App\Models\SPMB\JadwalUjianPMBModel;

class PMBPassingGradeController extends Controller 
{
    /**
     * digunakan untuk mendapatkan passing grade dari jadwal ujian
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-PASSING-GRADE_BROWSE');

        $this->validate($request, [                       
            'jadwal_ujian_id'=>'required|exists:pe3_jadwal_ujian_pmb,id',                       
        ]);

        $jadwal_ujian_id=$request->input('jadwal_ujian_id');
        $jadwal_ujian=JadwalUjianPMBModel::select(\DB::raw('
                                                id,
                                                nama_kegiatan,
                                                ta,
                                                idsmt
                                            '))
                                            ->find($jadwal_ujian_id);

        $data = PMBPassingGradeModel::where('jadwal_ujian_id',$jadwal_ujian_id)
                                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'jadwal_ujian'=>$jadwal_ujian,
                                'passing_grade'=>$data,
                                'message'=>'Fetch data passing grade ujian pmb berhasil diperoleh'
                            ],200);  
        
    }
    /**
     * digunakan untuk meload passing grade prodi pertama kali
     *
     * @return \Illuminate\Http\Response
     */
    public function loadprodi (Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB-PASSING-GRADE_STORE');

        $this->validate($request, [                       
            'jadwal_ujian_id'=>'required|exists:pe3_jadwal_ujian_pmb,id',                       
        ]);
        
        $jadwal_ujian_id=$request->input('jadwal_ujian_id');

        $sql = "INSERT INTO pe3_passing_grade_pmb (
                    id,
                    jadwal_ujian_id,
                    kjur,
                    nilai,
                    created_at,
                    updated_at
                ) SELECT 
                    UUID(),
                    '$jadwal_ujian_id',
                    id,
                    0 AS nilai,
                    NOW(),
                    NOW() 
                FROM pe3_prodi 
                WHERE id NOT IN (
                                    SELECT 
                                        kjur 
                                    FROM 
                                        pe3_passing_grade_pmb 
                                    WHERE jadwal_ujian_id='$jadwal_ujian_id'
                                )
            ";
        \DB::statement($sql);
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                                                
                                'message'=>'Data nilai passing grade ujian pmb masing-masing prodi berhasil digenerate'
                            ],200);  
    }    
    /**
     * digunakan untuk meload passing grade prodi pertama kali
     *
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request,$id)
    {   
        $this->hasPermissionTo('SPMB-PMB-PASSING-GRADE_STORE');

        $this->validate($request, [                       
            'id'=>'required|exists:pe3_passing_grade_pmb,id',                       
            'nilai'=>'required|numeric',                       
        ]);
        
        $passing_id=$request->input('id');

        $passing=PMBPassingGradeModel::find($passing_id);
        $passing->nilai=$request->input('nilai');        
        $passing->save();        

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                
                                'passing_grade'=>$passing,
                                'message'=>'Data nilai passing grade ujian pmb sudah diubah'
                            ],200);  
    }
}