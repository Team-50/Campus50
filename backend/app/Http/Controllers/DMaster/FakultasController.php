<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\FakultasModel;

class FakultasController extends Controller {  
    /**
     * daftar fakultas
     */
    public function index(Request $request)
    {
        $fakultas=FakultasModel::all();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'fakultas'=>$fakultas,                                                                                                                                   
                                    'message'=>'Fetch data fakultas berhasil.'
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
        $this->hasPermissionTo('DMASTER-FAKULTAS_STORE');

        $this->validate($request, [            
            'kode_fakultas'=>'required|numeric|unique:pe3_fakultas',
            'nama_fakultas'=>'required|string|unique:pe3_fakultas',            
        ]);
             
        $fakultas=FakultasModel::create([
            'kode_fakultas'=>$request->input('kode_fakultas'),
            'nama_fakultas'=>$request->input('nama_fakultas'),            
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $fakultas,
                                        'object_id'=>$fakultas->kode_fakultas, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah fakultas baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'fakultas'=>$fakultas,                                    
                                    'message'=>'Data fakultas berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-FAKULTAS_UPDATE');

        $fakultas = FakultasModel::find($id);
        if (is_null($fakultas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Kode Fakultas ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [
                                        'kode_fakultas'=>[
                                                        'required',                                                        
                                                        Rule::unique('pe3_fakultas')->ignore($fakultas->kode_fakultas,'kode_fakultas')
                                                    ],           
                                        
                                        'nama_fakultas'=>[
                                                        'required',
                                                        Rule::unique('pe3_fakultas')->ignore($fakultas->nama_fakultas,'nama_fakultas')
                                                    ],           
                                        
                                    ]); 
                                    
            $fakultas->kode_fakultas = $request->input('kode_fakultas');
            $fakultas->nama_fakultas = $request->input('nama_fakultas');            
            $fakultas->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $fakultas,
                                                        'object_id'=>$fakultas->kode_fakultas, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data fakultas ('.$fakultas->nama_fakultas.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'fakultas'=>$fakultas,      
                                    'message'=>'Data fakultas '.$fakultas->username.' berhasil diubah.'
                                ],200); 
        }
    }
    /**
     * daftar program studi
     */
    public function programstudi(Request $request,$id)
    {
        $fakultas=FakultasModel::find($id);
        if (is_null($fakultas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data program studi berdasarkan id fakultas gagal"]
                                ],422); 
        }
        else
        {
            $programstudi = $fakultas->programstudi;
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'programstudi'=>$programstudi,                                                                                                                                   
                                        'message'=>'Fetch data program studi berdasarkan id fakultas berhasil.'
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
        $this->hasPermissionTo('DMASTER-FAKULTAS_DESTROY');

        $fakultas = FakultasModel::find($id); 
        
        if (is_null($fakultas))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode fakultas ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $fakultas, 
                                                                'object_id' => $fakultas->kode_fakultas, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Fakultas ('.$id.') berhasil'
                                                            ]);
            $fakultas->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Fakultas dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}