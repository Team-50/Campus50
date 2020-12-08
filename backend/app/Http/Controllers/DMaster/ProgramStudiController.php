<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\ProgramStudiModel;
use App\Models\DMaster\JenjangStudiModel;
use App\Models\System\ConfigurationModel;

class ProgramStudiController extends Controller {  
    /**
     * daftar program studi
     */
    public function index(Request $request)
    {
        $prodi=ProgramStudiModel::select(\DB::raw('id,kode_prodi,nama_prodi,CONCAT(nama_prodi,\' (\',nama_jenjang,\')\') AS nama_prodi2,nama_prodi_alias,kode_jenjang,nama_jenjang,pe3_fakultas.kode_fakultas,nama_fakultas'))
                                ->leftJoin('pe3_fakultas','pe3_fakultas.kode_fakultas','pe3_prodi.kode_fakultas')
                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'prodi'=>$prodi,                                                                                                                                   
                                    'message'=>'Fetch data program studi berhasil.'
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
        $this->hasPermissionTo('DMASTER-PRODI_STORE');

        $bentuk_pt=ConfigurationModel::getCache('BENTUK_PT');
        if ($bentuk_pt=='sekolahtinggi')
        {
            $rule=[            
                'kode_prodi'=>'required|numeric|unique:pe3_prodi',
                'nama_prodi'=>'required|string|unique:pe3_prodi',            
                'nama_prodi_alias'=>'required|string|unique:pe3_prodi',            
                'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                'nama_jenjang'=>'required',            
            ];
            $kode_fakultas=null;
        }
        else
        {
            $rule=[            
                'kode_prodi'=>'required|numeric',
                'kode_fakultas'=>'required|exists:pe3_fakultas,kode_fakultas',
                'nama_prodi'=>'required|string|unique:pe3_prodi',         
                'nama_prodi_alias'=>'required|string|unique:pe3_prodi',         
                'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                'nama_jenjang'=>'required',               
            ];
            $kode_fakultas=$request->input('kode_fakultas');
        }
        $this->validate($request, $rule);
             
        $prodi=ProgramStudiModel::create([
            'kode_prodi'=>$request->input('kode_prodi'),
            'kode_fakultas'=>$kode_fakultas,
            'nama_prodi'=>$request->input('nama_prodi'),            
            'nama_prodi_alias'=>$request->input('nama_prodi_alias'),            
            'kode_jenjang'=>$request->input('kode_jenjang'),            
            'nama_jenjang'=>$request->input('nama_jenjang'),            
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $prodi,
                                        'object_id'=>$prodi->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah program studi baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'prodi'=>$prodi,                                    
                                    'message'=>'Data program studi berhasil disimpan.'
                                ],200); 

    }
    /**
     * digunakan untuk mendapatkan daftar jenjang studi program studi
     */
    public function jenjangstudi ()
    {
        $jenjangstudi = JenjangStudiModel::all();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'jenjangstudi'=>$jenjangstudi,                                    
                                    'message'=>'Jenjang studi berhasil diperoleh.'
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
        $this->hasPermissionTo('DMASTER-PRODI_UPDATE');

        $prodi = ProgramStudiModel::find($id);
        if (is_null($prodi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Kode Program Studi ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $bentuk_pt=ConfigurationModel::getCache('BENTUK_PT');
            if ($bentuk_pt=='sekolahtinggi')
            {
                $this->validate($request, [
                                            'kode_prodi'=>[
                                                            'required',                                                        
                                                            'numeric'                                                       
                                                        ],           
                                            
                                            'nama_prodi'=>[
                                                            'required',
                                                            'string',
                                                            Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi,'nama_prodi')
                                                        ],           
                                            'nama_prodi_alias'=>[
                                                            'required',
                                                            'string',
                                                            Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi_alias,'nama_prodi_alias')
                                                        ],  
                                                        
                                            'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                                            'nama_jenjang'=>'required',            
                                            
                                        ]); 
            }
            else
            {
                $this->validate($request, [
                                            'kode_fakultas'=>[
                                                'required',
                                                'exists:pe3_fakultas,kode_fakultas',                                                     
                                            ],
                                            'kode_prodi'=>[
                                                            'required',                                                        
                                                            'numeric'                                                       
                                                        ],           
                                            
                                            'nama_prodi'=>[
                                                            'required',
                                                            'string',
                                                            Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi,'nama_prodi')
                                                        ],           
                                            'nama_prodi'=>[
                                                            'required',
                                                            'string',
                                                            Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi_alias,'nama_prodi_alias')
                                                        ],   
                                            'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                                            'nama_jenjang'=>'required',            
                                            
                                        ]); 
            }                       
            $prodi->kode_fakultas = $request->input('kode_fakultas');
            $prodi->kode_prodi = $request->input('kode_prodi');
            $prodi->nama_prodi = $request->input('nama_prodi');            
            $prodi->nama_prodi_alias = $request->input('nama_prodi_alias');            
            $prodi->kode_jenjang = $request->input('kode_jenjang');            
            $prodi->nama_jenjang = $request->input('nama_jenjang');            
            $prodi->save();

            \DB::table('usersprodi')
                ->where('id',$id)
                ->update([
                    'kode_prodi' => $request->input('kode_prodi'),
                    'nama_prodi' => $request->input('nama_prodi'),            
                    'nama_prodi_alias' => $request->input('nama_prodi_alias'),
                    'kode_jenjang' => $request->input('kode_jenjang'),
                    'nama_jenjang' => $request->input('nama_jenjang'),
                ]);

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $prodi,
                                                        'object_id'=>$prodi->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data program studi ('.$prodi->nama_prodi.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'prodi'=>$prodi,      
                                    'message'=>'Data program studi '.$prodi->username.' berhasil diubah.'
                                ],200); 
        }
    }
    /**
     * daftar program studi
     */
    public function programstudi(Request $request,$id)
    {
        $prodi=ProgramStudiModel::find($id);
        if (is_null($prodi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data program studi berdasarkan id program studi gagal"]
                                ],422); 
        }
        else
        {
            $programstudi = $prodi->programstudi;
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'programstudi'=>$programstudi,                                                                                                                                   
                                        'message'=>'Fetch data program studi berdasarkan id program studi berhasil.'
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
        $this->hasPermissionTo('DMASTER-PRODI_DESTROY');

        $prodi = ProgramStudiModel::find($id); 
        
        if (is_null($prodi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode program studi ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $prodi, 
                                                                'object_id' => $prodi->kode_prodi, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Program Studi ('.$id.') berhasil'
                                                            ]);
            $prodi->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Program Studi dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}