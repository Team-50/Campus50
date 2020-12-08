<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Helper;
use App\Models\DMaster\KelasModel;
use App\Models\Keuangan\KomponenBiayaModel;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;

class BiayaKomponenPeriodeController extends Controller {  
    /**
     * daftar komponen biaya per periode
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_BROWSE');
        
        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');
        
        $kombi=BiayaKomponenPeriodeModel::join('pe3_kelas','pe3_kombi_periode.idkelas','pe3_kelas.idkelas')
                                        ->where('tahun',$ta)
                                        ->where('kjur',$prodi_id)
                                        ->orderBy('pe3_kombi_periode.idkelas','asc')
                                        ->orderBy('kombi_id','asc');
        if ($request->has('filter_idkelas'))
        {
            $kombi=$kombi->where('pe3_kombi_periode.idkelas',$request->input('filter_idkelas'));
        }
        $kombi=$kombi->get();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kombi'=>$kombi,                                                                                                                                   
                                    'message'=>'Fetch data biaya komponen periode berhasil.'
                                ],200);     
    } 
    /**
     * digunakan untuk meload daftar kombi pertama kali atau selanjutnya ke table pe3_kombi_periode
     */
    public function loadkombiperiode (Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE');
        
        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');
        
        $daftar_kelas=KelasModel::all();
        foreach ($daftar_kelas as $kelas)
        {
            $idkelas=$kelas->idkelas;
            $sql = "INSERT INTO pe3_kombi_periode (id,kombi_id,nama_kombi,periode,idkelas,kjur,tahun,biaya,created_at,updated_at)
                    SELECT UUID(),id,nama AS nama_kombi,periode,'$idkelas' AS idkelas,$prodi_id AS kjur,$ta AS tahun,0 AS biaya,NOW() AS created_at,NOW() AS updated_at FROM pe3_kombi WHERE id NOT IN (SELECT kombi_id FROM pe3_kombi_periode WHERE tahun='$ta' AND kjur=$prodi_id AND idkelas='$idkelas')";                
                    
            \DB::statement($sql);
        }        

        $kombi=BiayaKomponenPeriodeModel::join('pe3_kelas','pe3_kombi_periode.idkelas','pe3_kelas.idkelas')
                                        ->where('tahun',$ta)
                                        ->where('kjur',$prodi_id)
                                        ->orderBy('pe3_kombi_periode.idkelas','asc')
                                        ->orderBy('kombi_id','asc')
                                        ->get();
        
        \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $kombi,
                                                        'object_id'=>'N.A', 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Menyalin data kombi ke data kombi periode berhasil.'
                                                    ]);
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',  
                                    'kombi'=>$kombi,                                                                                                                                   
                                    'message'=>'Menyalin data kombi ke data kombi periode berhasil.'
                                ],200);     
    } 
    /**
     * digunakan untuk merubah biaya komponen
     */
    public function updatebiaya (Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE');
        
        $this->validate($request, [           
            'id'=>'required|exists:pe3_kombi_periode,id',
            'biaya'=>'required'
        ]);
        $id=$request->input('id');
        $biaya=$request->input('biaya');
        
        $kombi_biaya=BiayaKomponenPeriodeModel::find($id);
        $old_biaya=$kombi_biaya->biaya;
        $kombi_biaya->biaya=$biaya;
        $kombi_biaya->save();
        
        \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $kombi_biaya,
                                                        'object_id'=>$kombi_biaya->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah besaran biaya Rp. '.Helper::formatUang($old_biaya).' menjadi '.Helper::formatUang($biaya).' komponen ('.$kombi_biaya->nama_kombi.') berhasil dilakukan'
                                                    ]);
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',     
                                    'kombi_biaya'=>$kombi_biaya,                                                                                                                                                               
                                    'message'=>'Mengubah biaya komponen '.$kombi_biaya->nama_kombi.' berhasil.'
                                ],200);     
    } 
}