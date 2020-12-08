<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\KelasModel;
use App\Models\System\ConfigurationModel;

class KelasController extends Controller {  
    /**
     * daftar kelas
     */
    public function index(Request $request)
    {
        $kelas=KelasModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kelas'=>$kelas,                                                                                                                                   
                                    'message'=>'Fetch data kelas berhasil.'
                                ],200);     
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('DMASTER-KELAS_STORE');

        $rule=[            
            'idkelas'=>'required|string|max:1|unique:pe3_kelas,idkelas',
            'nkelas'=>'required|string|unique:pe3_kelas,nkelas',                     
        ];
    
        $this->validate($request, $rule);
             
        $kelas=KelasModel::create([
            'idkelas'=>$request->input('idkelas'),            
            'nkelas'=>strtoupper($request->input('nkelas')),                        
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $kelas,
                                        'object_id'=>$kelas->idkelas, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah kelas baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kelas'=>$kelas,                                    
                                    'message'=>'Data kelas berhasil disimpan.'
                                ],200); 

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('DMASTER-KELAS_UPDATE');

        $kelas = KelasModel::find($id);
        if (is_null($kelas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Kode Kelas ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $bentuk_pt=ConfigurationModel::getCache('BENTUK_PT');
            if ($bentuk_pt=='sekolahtinggi')
            {
                $this->validate($request, [
                                            'idkelas'=>[
                                                            'required',                                                        
                                                            'string',
                                                            'max:1',
                                                            Rule::unique('pe3_kelas')->ignore($kelas->idkelas,'idkelas')                                                       
                                                        ],           
                                            
                                            'nkelas'=>[
                                                            'required',
                                                            'string',
                                                            Rule::unique('pe3_kelas')->ignore($kelas->nkelas,'nkelas')
                                                        ],           
                                            
                                        ]); 
            }
            else
            {
                $this->validate($request, [
                                            'kode_fakultas'=>[
                                                'required',
                                                'string',
                                                'max:1',
                                                'exists:pe3_kelas,idkelas',                                                     
                                            ], 
                                            'nama_kelas'=>[
                                                'required',
                                                'string',
                                                Rule::unique('pe3_kelas')->ignore($kelas->nkelas,'nkelas')
                                            ],           
                                            
                                        ]); 
            }                       
            $kelas->idkelas = $request->input('idkelas');
            $kelas->nkelas = $request->input('nkelas');
                     
            $kelas->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $kelas,
                                                        'object_id'=>$kelas->idkelas, 
                                                        'user_id' => $this->guard()->user()->idkelas, 
                                                        'message' => 'Mengubah data kelas ('.$kelas->nkelas.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kelas'=>$kelas,      
                                    'message'=>'Data kelas '.$kelas->nkelas.' berhasil diubah.'
                                ],200); 
        }
    }    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('DMASTER-KELAS_DESTROY');

        $kelas = KelasModel::find($id); 
        
        if (is_null($kelas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode kelas ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $kelas, 
                                                                'object_id' => $kelas->idkelas, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Kelas ('.$id.') berhasil'
                                                            ]);
            $kelas->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Kelas dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}