<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Akademik\GroupMatakuliahModel;
use App\Models\Akademik\MatakuliahModel;

use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;

class MatakuliahController extends Controller {  
    /**
     * daftar matakuliah
     */
    public function index(Request $request)
    {
        $this->validate($request, [                       
            'prodi_id'=>'required'
        ]);
                
        $prodi_id=$request->input('prodi_id');

        $matakuliah=MatakuliahModel::select(\DB::raw('
                                    id,
                                    group_alias,                                    
                                    kmatkul,
                                    nmatkul,
                                    sks,
                                    semester,
                                    minimal_nilai,
                                    syarat_skripsi,
                                    status,
                                    0 AS jummlah_penyelenggaraan
                                '))       
                                ->where('kjur',$prodi_id)                                
                                ->orderBy('semester','ASC')                      
                                ->orderBy('kmatkul','ASC')                      
                                ->get();
        
        $matakuliah->transform(function ($item,$key) {                
            $item->jummlah_penyelenggaraan=\DB::table('pe3_penyelenggaraan')->where('matkul_id',$item->id)->count();                
            return $item;
        });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'matakuliah'=>$matakuliah,                                                                                                                                   
                                    'message'=>'Fetch data matakuliah berhasil.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * digunakan untuk mendapatkan detail matakuliah
     */
    public function show(Request $request, $id)
    {
        $this->hasPermissionTo('AKADEMIK-MATAKULIAH_SHOW');

        $matakuliah = MatakuliahModel::select(\DB::raw('
                                            pe3_matakuliah.*,
                                            COALESCE(pe3_matakuliah.sks_praktikum,\'N.A\') AS sks_praktikum2,
                                            COALESCE(pe3_matakuliah.sks_praktik_lapangan,\'N.A\') AS sks_praktik_lapangan2,
                                            nama_prodi                                            
                                        '))
                                        ->join('pe3_prodi','pe3_prodi.id','pe3_matakuliah.kjur')                                        
                                        ->find($id);

        if (is_null($matakuliah))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["matakuliah dengan ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                                    
                                    'matakuliah'=>$matakuliah,                                    
                                    'message'=>'Data matakuliah dengan id ('.$id.') berhasil diperoleh.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);

        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-MATAKULIAH_STORE');

        $kjur=$request->input('kjur');        

        $rule=[            
            'id_group'=>'required',
            'kmatkul'=>[
                'required',
                Rule::unique('pe3_matakuliah')->where(function ($query) use ($kjur) {
                    $query->where('kjur',$kjur);
                })
            ],
            'nmatkul'=>[
                'required',
                Rule::unique('pe3_matakuliah')->where(function ($query) use ($kjur) {
                    $query->where('kjur',$kjur);
                })
            ],
            'sks'=>'required|numeric',            
            'semester'=>'required|numeric',            
            'sks_tatap_muka'=>'required|numeric',            
            'minimal_nilai'=>'required',                         
            'kjur'=>'required',  
        ];
    
        $this->validate($request, $rule);
        
        $id_group=$request->input('id_group');
        $nama_group=$request->input('nama_group');
        $group_alias=$request->input('group_alias');

        $group_matakuliah=GroupMatakuliahModel::find($id_group);
        if(!is_null($group_matakuliah))
        {
            $nama_group=$group_matakuliah->nama_group;
            $group_alias=$group_matakuliah->group_alias;
        }

        $matakuliah=MatakuliahModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'id_group'=>$id_group,
            'nama_group'=>$nama_group,
            'group_alias'=>$group_alias,
            'kmatkul'=>strtoupper(trim($request->input('kmatkul'))),
            'nmatkul'=>ucwords(trim($request->input('nmatkul'))),            
            'sks'=>$request->input('sks'),            
            'idkonsentrasi'=>$request->input('idkonsentrasi'),            
            'ispilihan'=>$request->input('ispilihan'),            
            'islintas_prodi'=>$request->input('islintas_prodi'),            
            'semester'=>$request->input('semester'),            
            'sks_tatap_muka'=>$request->input('sks_tatap_muka'),            
            'sks_praktikum'=>$request->input('sks_praktikum'),            
            'sks_praktik_lapangan'=>$request->input('sks_praktik_lapangan'),            
            'minimal_nilai'=>$request->input('minimal_nilai'),            
            'syarat_skripsi'=>$request->input('syarat_skripsi'),            
            'status'=>$request->input('status'),                        
            'kjur'=>$request->input('kjur'),            
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $matakuliah,
                                        'object_id'=>$matakuliah->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah matakuliah baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',                                    
                                    'matakuliah'=>$matakuliah,                                    
                                    'message'=>'Data matakuliah berhasil disimpan.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);

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
        $this->hasPermissionTo('AKADEMIK-MATAKULIAH_UPDATE');

        $matakuliah = MatakuliahModel::find($id);
        if (is_null($matakuliah))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["matakuliah dengan id ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $kjur=$matakuliah->kjur;            
            $this->validate($request, [
                                        'id_group'=>'required',
                                        'kmatkul'=>[
                                                        'required',
                                                        Rule::unique('pe3_matakuliah')->ignore($matakuliah->kmatkul,'kmatkul')->where(function ($query) use ($kjur) {
                                                            $query->where('kjur',$kjur);
                                                        })
                                                    ],           
                                        
                                        'nmatkul'=>[
                                                        'required',                                                        
                                                        Rule::unique('pe3_matakuliah')->ignore($matakuliah->nmatkul,'nmatkul')->where(function ($query) use ($kjur) {
                                                            $query->where('kjur',$kjur);
                                                        })
                                                    ],           
                                        'sks'=>'required|numeric',            
                                        'semester'=>'required|numeric',            
                                        'sks_tatap_muka'=>'required|numeric',            
                                        'minimal_nilai'=>'required',                                                    
                                        'kjur'=>'required',                                          
                                    ]); 
            
            $id_group=$request->input('id_group');
            $nama_group=$request->input('nama_group');
            $group_alias=$request->input('group_alias');
            
            $group_matakuliah=GroupMatakuliahModel::find($id_group);
            if(!is_null($group_matakuliah))
            {
                $nama_group=$group_matakuliah->nama_group;
                $group_alias=$group_matakuliah->group_alias;
            }

            $matakuliah->id_group = $request->input('id_group');
            $matakuliah->nama_group = $request->input('nama_group');            
            $matakuliah->group_alias = $request->input('group_alias');            
            $matakuliah->kmatkul = strtoupper(trim($request->input('kmatkul')));            
            $matakuliah->nmatkul = ucwords(trim($request->input('nmatkul')));
            $matakuliah->sks = $request->input('sks');            
            $matakuliah->idkonsentrasi = $request->input('idkonsentrasi');            
            $matakuliah->ispilihan = $request->input('ispilihan');            
            $matakuliah->islintas_prodi = $request->input('islintas_prodi');            
            $matakuliah->semester = $request->input('semester');            
            $matakuliah->sks_tatap_muka = $request->input('sks_tatap_muka');            
            $matakuliah->sks_praktikum = $request->input('sks_praktikum');            
            $matakuliah->sks_praktik_lapangan = $request->input('sks_praktik_lapangan');            
            $matakuliah->minimal_nilai = $request->input('minimal_nilai');            
            $matakuliah->syarat_skripsi = $request->input('syarat_skripsi');            
            $matakuliah->status = $request->input('status');           
            
            $matakuliah->save();
            
            if ($request->input('update_penyelenggaraan')=='true')
            {
                \DB::table('pe3_penyelenggaraan')
                        ->where('matkul_id',$matakuliah->id)
                        ->update([
                                    'kmatkul'=>$matakuliah->kmatkul,
                                    'nmatkul'=>$matakuliah->nmatkul,
                                    'sks'=>$matakuliah->sks,
                                ]);
            }

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $matakuliah,
                                                        'object_id'=>$matakuliah->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data matakuliah ('.$matakuliah->nama_prodi.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'matakuliah'=>$matakuliah,      
                                    'message'=>'Data matakuliah '.$matakuliah->username.' berhasil diubah.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
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
        $this->hasPermissionTo('AKADEMIK-MATAKULIAH_DESTROY');

        $matakuliah = MatakuliahModel::find($id); 
        
        if (is_null($matakuliah))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode matakuliah ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $matakuliah, 
                                                                'object_id' => $matakuliah->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus matakuliah dengan id ('.$id.') berhasil'
                                                            ]);
            $matakuliah->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"matakuliah dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
    public function penyelenggaraan (Request $request)
    {
        $this->validate($request, [                       
            'prodi_id'=>'required',
            'ta_akademik'=>'required',
            'semester_akademik'=>'required'
        ]);
        
        $prodi_id=$request->input('prodi_id');
        $ta_akademik=$request->input('ta_akademik');
        $semester_akademik=$request->input('semester_akademik');

        $matakuliah=MatakuliahModel::select(\DB::raw('
                                    id,
                                    group_alias,                                    
                                    kmatkul,
                                    nmatkul,
                                    sks,
                                    semester,
                                    minimal_nilai,
                                    syarat_skripsi,
                                    status
                                '))       
                                ->where('kjur',$prodi_id)                                
                                ->whereNotIn('id',function($query) use($ta_akademik,$prodi_id,$semester_akademik){
                                    $query->select('matkul_id')
                                        ->from('pe3_penyelenggaraan')
                                        ->where('kjur',$prodi_id)
                                        ->where('tahun',$ta_akademik)
                                        ->where('idsmt',$semester_akademik);
                                })
                                ->orderBy('semester','ASC')                      
                                ->orderBy('kmatkul','ASC')                      
                                ->get();
        
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'matakuliah'=>$matakuliah,                                                                                                                                   
                                    'message'=>'Fetch data matakuliah berhasil.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}