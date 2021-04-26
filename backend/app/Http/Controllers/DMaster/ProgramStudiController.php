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
        $prodi=ProgramStudiModel::select(\DB::raw("
                                    id,
                                    kode_forlap,
                                    nama_prodi,                                    
                                    '' AS nama_prodi2,                                    
                                    nama_prodi_alias,
                                    COALESCE(konsentrasi,'N.A') AS konsentrasi,
                                    kode_jenjang,
                                    nama_jenjang,
                                    pe3_fakultas.kode_fakultas,
                                    nama_fakultas,
                                    pe3_prodi.config,
                                    created_at,
                                    updated_at
                                "))
                                ->leftJoin('pe3_fakultas','pe3_fakultas.kode_fakultas','pe3_prodi.kode_fakultas')
                                ->get();
        $prodi->transform(function ($item,$key) {
            if (is_null($item->konsentrasi) || empty(trim($item->konsentrasi)) || $item->konsentrasi == 'N.A')
            {
                $item->nama_prodi2 = $item->nama_prodi . ' ('. $item->nama_jenjang.')';
            }
            else
            {
                $item->nama_prodi2 = $item->nama_prodi . ' Kons. '.$item->konsentrasi.' ('. $item->nama_jenjang.')';                
            }                           
            return $item;
        });              
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

        $custom_atribute = [];

        $bentuk_pt=ConfigurationModel::getCache('BENTUK_PT');
        if ($bentuk_pt=='sekolahtinggi')
        {
            $rule=[            
                'id'=>'required|numeric|unique:pe3_prodi',
                'kode_forlap'=>'required|numeric',
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
                'id'=>'required|numeric|unique:pe3_prodi',
                'kode_forlap'=>'required|numeric',
                'kode_fakultas'=>'required|exists:pe3_fakultas,kode_fakultas',
                'nama_prodi'=>'required|string|unique:pe3_prodi',         
                'nama_prodi_alias'=>'required|string|unique:pe3_prodi',         
                'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                'nama_jenjang'=>'required',               
            ];
            $kode_fakultas=$request->input('kode_fakultas');
        }
        $this->validate($request, $rule, [], $custom_atribute);
             
        $prodi=ProgramStudiModel::create([
            'id'=>$request->input('id'),
            'kode_forlap'=>$request->input('kode_forlap'),
            'kode_fakultas'=>$kode_fakultas,
            'nama_prodi'=>$request->input('nama_prodi'),            
            'nama_prodi_alias'=>$request->input('nama_prodi_alias'),            
            'konsentrasi'=>$request->input('konsentrasi'),            
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
     * ubah kaprodi
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateconfig (Request $request,$id)
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
            $this->validate($request,[
                'config'=>'required'
            ]);
            $kaprodi=$request->input('config');
            $prodi->config=$request->input('config');
            $prodi->save();
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'prodi'=>$prodi,      
                                    'message'=>'Konfigurasi program studi '.$prodi->nama_prodi.' berhasil disimpan.'
                                ],200);
        }
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
        
        $custom_atribute = [];

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
            $custom_atribute = [
                'nama_prodi'=>'Nama Program Studi',
                'nama_prodi_alias'=>'Nama Singkatan Program Studi',
            ];
            if ($bentuk_pt=='sekolahtinggi')
            {
                $this->validate($request, 
                                [
                                    'id'=>[
                                        'required',                                                        
                                        'numeric',
                                        Rule::unique('pe3_prodi')->ignore($prodi->id,'id')
                                    ],  

                                    'kode_forlap'=>[
                                        'required',                                                        
                                        'numeric'                                                       
                                    ],           
                                    
                                    'nama_prodi'=>[
                                        'required',
                                        'string',
                                    ],           
                                    'nama_prodi_alias'=>[
                                        'required',
                                        'string',
                                        Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi_alias,'nama_prodi_alias')
                                    ],                                                          
                                    'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                                    'nama_jenjang'=>'required',                                                        
                                ],
                                [],
                                $custom_atribute
                            ); 
            }
            else
            {
                $this->validate($request, 
                                [
                                    'id'=>[
                                        'required',                                                        
                                        'numeric',
                                        Rule::unique('pe3_prodi')->ignore($prodi->id,'id')
                                    ],
                                    'kode_fakultas'=>[
                                        'required',
                                        'exists:pe3_fakultas,kode_fakultas',                                                     
                                    ],
                                    'kode_forlap'=>[
                                        'required',                                                        
                                        'numeric'                                                       
                                    ],    
                                    
                                    'nama_prodi'=>[
                                        'required',
                                        'string',                                        
                                    ],           
                                    'nama_prodi_alias'=>[
                                        'required',
                                        'string',
                                        Rule::unique('pe3_prodi')->ignore($prodi->nama_prodi_alias,'nama_prodi_alias')
                                    ],   
                                    'kode_jenjang'=>'required|exists:pe3_jenjang_studi,kode_jenjang',            
                                    'nama_jenjang'=>'required',  
                                ],
                                [],
                                $custom_atribute
                        ); 
            }                       
            $prodi->id = $request->input('id');
            $prodi->kode_fakultas = $request->input('kode_fakultas');
            $prodi->kode_forlap = $request->input('kode_forlap');
            $prodi->nama_prodi = $request->input('nama_prodi');            
            $prodi->nama_prodi_alias = $request->input('nama_prodi_alias');            
            $prodi->konsentrasi = $request->input('konsentrasi');            
            $prodi->kode_jenjang = $request->input('kode_jenjang');            
            $prodi->nama_jenjang = $request->input('nama_jenjang');            
            $prodi->save();

            \DB::table('usersprodi')
                ->where('id',$id)
                ->update([
                    'kode_forlap' => $request->input('kode_forlap'),
                    'nama_prodi' => $request->input('nama_prodi'),            
                    'nama_prodi_alias' => $request->input('nama_prodi_alias'),
                    'konsentrasi' => $request->input('konsentrasi'),
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
                                    'message'=>'Data program studi '.$prodi->nama_prodi.' berhasil diubah.'
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
                                                                'object_id' => $prodi->kode_forlap, 
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