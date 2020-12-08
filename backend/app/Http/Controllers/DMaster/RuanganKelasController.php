<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\RuanganKelasModel;

use Ramsey\Uuid\Uuid;
class RuanganKelasController extends Controller {  
    /**
     * daftar ruang kelas
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('DMASTER-RUANGAN-KELAS_BROWSE');
        
        $ruangan=RuanganKelasModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'ruangan'=>$ruangan,                                                                                                                                   
                                    'message'=>'Fetch data ruangan kelas berhasil.'
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
        $this->hasPermissionTo('DMASTER-RUANGAN-KELAS_STORE');

        $rule=[            
            'namaruang'=>'required|string|unique:pe3_ruangkelas,namaruang',
            'kapasitas'=>'required|numeric',                     
        ];
    
        $this->validate($request, $rule);
             
        $ruangan=RuanganKelasModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'namaruang'=>$request->input('namaruang'),            
            'kapasitas'=>strtoupper($request->input('kapasitas')),                        
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $ruangan,
                                        'object_id'=>$ruangan->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah ruang kelas baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'ruangan'=>$ruangan,                                    
                                    'message'=>'Data ruang kelas berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-RUANGAN-KELAS_UPDATE');

        $ruangan = RuanganKelasModel::find($id);
        if (is_null($ruangan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Rung Kelas ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            
            $this->validate($request, [
                                        'namaruang'=>[
                                                        'required',                                                        
                                                        'string',                                                            
                                                        Rule::unique('pe3_ruangkelas')->ignore($ruangan->namaruang,'namaruang')                                                       
                                                    ],           
                                        
                                        'kapasitas'=>[
                                                        'required',
                                                        'numeric',                                                            
                                                    ],           
                                        
                                    ]); 
        
            $ruangan->namaruang = $request->input('namaruang');
            $ruangan->kapasitas = $request->input('kapasitas');
                     
            $ruangan->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $ruangan,
                                                        'object_id'=>$ruangan->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data ruang kelas ('.$ruangan->namaruang.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'ruangan'=>$ruangan,      
                                    'message'=>'Data ruang kelas '.$ruangan->namaruang.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-RUANGAN-KELAS_DESTROY');

        $ruangan = RuanganKelasModel::find($id); 
        
        if (is_null($ruangan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Ruang kelas dengan id ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $ruangan, 
                                                                'object_id' => $ruangan->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Ruang Kelas ('.$id.') berhasil'
                                                            ]);
            $ruangan->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Ruang Kelas dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}