<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DMaster\PersyaratanModel;
use Ramsey\Uuid\Uuid;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('DMASTER-PERSYARATAN-PMB_STORE');

        $this->validate($request, [
            'proses'=>'required',
            'nama_persyaratan'=>'required',
            'ta'=>'required',
        ]);
             
        $persyaratan=PersyaratanModel::create([
            'id'=>Uuid::uuid4()->toString(),            
            'proses'=>$request->input('proses'),            
            'nama_persyaratan'=>$request->input('nama_persyaratan'),            
            'ta'=>$request->input('ta'),                       
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $persyaratan,
                                        'object_id'=>$persyaratan->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah persyaratan baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'persyaratan'=>$persyaratan,                                    
                                    'message'=>'Data persyaratan berhasil disimpan.'
                                ],200); 

    }
    /**
     * salin persyaratan dari tahun ke tahun lainnya
     */
    public function salin(Request $request,$id)
    {
        $this->hasPermissionTo('DMASTER-PERSYARATAN-PMB_UPDATE');
        
        $this->validate($request, [           
            'dari_tahun_pendaftaran'=>'required',                     
            'proses'=>'required',                     
        ]);

        $dari_tahun_pendaftaran=$request->input('dari_tahun_pendaftaran');
        $proses=$request->input('proses');
        
        \DB::table('pe3_persyaratan')
            ->where('ta',$id)
            ->where('proses','pmb')
            ->delete();

        $sql = "INSERT INTO pe3_persyaratan (id,
                                            proses,
                                            nama_persyaratan,
                                            prodi_id,
                                            ta,
                                            created_at,
                                            updated_at)
                SELECT UUID(),
                        '$proses' AS proses,
                        nama_persyaratan,
                        NULL AS prodi_id,
                        $id AS ta,
                        NOW() as created_at,
                        NOW() as updated_at
                FROM
                    pe3_persyaratan 
                WHERE
                    ta='$dari_tahun_pendaftaran'
                    AND proses='pmb'";

        \DB::statement($sql);
        
        $persyaratan=PersyaratanModel::where('ta',$id)->get();

        \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $persyaratan,
                                                        'object_id'=>'N.A', 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => "Menyalin data persyaratan dari tahun $dari_tahun_pendaftaran ke $id berhasil."
                                                    ]);

        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',  
                                'persyaratan'=>$persyaratan,                                                                                                                                   
                                'message' => "Menyalin data persyaratan dari tahun $dari_tahun_pendaftaran ke $id berhasil."
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('DMASTER-PERSYARATAN-PMB_UPDATE');

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
            $this->validate($request, [           
                                        'nama_persyaratan'=>'required',                    
                                    ]);
            $persyaratan->nama_persyaratan=$request->input('nama_persyaratan');
            $persyaratan->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'persyaratan'=>$persyaratan,      
                                    'message'=>'Data persyaratan '.$persyaratan->nama_persyaratan.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-PERSYARATAN-PMB_DESTROY');

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
                                                                'object_id' => $persyaratan->id, 
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