<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\PersyaratanModel;

class PersyaratanController extends Controller {  
    /**
     * daftar persyaratan
     */
    public function index(Request $request)
    {
        $this->validate($request, [           
            'TA'=>'required',            
        ]);
        $ta=$request->input('TA');

        $persyaratan=PersyaratanModel::where('ta',$ta)->get();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'persyaratan'=>$persyaratan,                                                                                                                                   
                                    'message'=>'Fetch data persyaratan berhasil.'
                                ],200);     
    }    
    /**
     * daftar persyaratan dari sebuah proses 
     */
    public function proses(Request $request,$id)
    {
        //id == proses id misalnya PMB, SKRIPSI, atau yang lainnya.
        switch($id)
        {
            case 'pmb' :     
                $prodi_id=intval($request->input('prodi_id'));        
                if ($prodi_id >0 )
                {
                    $this->validate($request, [            
                        'prodi_id'=>'required|numeric|exists:pe3_prodi,id',  
                        'TA'=>'required',          
                    ]);
                    $ta=$request->input('TA');
                    $persyaratan=PersyaratanModel::where('prodi_id',$request->input('prodi_id'))
                                                ->where('proses','pmb')
                                                ->where('ta',$ta)
                                                ->get();
                }
                else
                {
                    $this->validate($request, [                                    
                        'TA'=>'required',          
                    ]);
                    $ta=$request->input('TA');
                    $persyaratan=PersyaratanModel::where('proses','pmb')
                                                ->where('ta',$ta)
                                                ->get();
                }                
            break;
        }
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'persyaratan'=>$persyaratan,                                                                                                                                   
                                    'message'=>"Fetch data persyaratan $id berhasil diperoleh."
                                ],200);     


    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('DMASTER-FAKULTAS_DESTROY');

        $persyaratan = PersyaratanModel::find($id); 
        
        if (is_null($persyaratan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode persyaratan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $persyaratan, 
                                                                'object_id' => $persyaratan->kode_persyaratan, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Persyaratan ('.$id.') berhasil'
                                                            ]);
            $persyaratan->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Persyaratan dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}