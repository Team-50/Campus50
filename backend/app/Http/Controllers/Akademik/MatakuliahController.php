<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Akademik\GroupMatakuliahModel;
use App\Models\Akademik\MatakuliahModel;

use Ramsey\Uuid\Uuid;

class MatakuliahController extends Controller {  
    /**
     * daftar matakuliah
     */
    public function index(Request $request)
    {
        $this->validate($request, [           
            'ta'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('ta');
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
                                    status
                                '))       
                                ->where('kjur',$prodi_id)
                                ->where('ta',$ta)   
                                ->orderBy('semester','ASC')                      
                                ->orderBy('kmatkul','ASC')                      
                                ->get();
        
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'matakuliah'=>$matakuliah,                                                                                                                                   
                                    'message'=>'Fetch data matakuliah berhasil.'
                                ],200);     
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
                                            nama_prodi,
                                            tahun_akademik
                                        '))
                                        ->join('pe3_prodi','pe3_prodi.id','pe3_matakuliah.kjur')
                                        ->join('pe3_ta','pe3_ta.tahun','pe3_matakuliah.ta')
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
                                ],200); 

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
        $ta=$request->input('ta');

        $rule=[            
            'id_group'=>'required',
            'kmatkul'=>[
                'required',
                Rule::unique('pe3_matakuliah')->where(function ($query) use ($ta,$kjur) {
                    $query->where('ta', $ta)
                        ->where('kjur',$kjur);
                })
            ],
            'nmatkul'=>[
                'required',
                Rule::unique('pe3_matakuliah')->where(function ($query) use ($ta,$kjur) {
                    $query->where('ta', $ta)
                        ->where('kjur',$kjur);
                })
            ],
            'sks'=>'required|numeric',            
            'semester'=>'required|numeric',            
            'sks_tatap_muka'=>'required|numeric',            
            'minimal_nilai'=>'required',            
            'ta'=>'required',            
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
            'kmatkul'=>strtoupper($request->input('kmatkul')),
            'nmatkul'=>ucwords($request->input('nmatkul')),            
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
            'ta'=>$request->input('ta'),            
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
                                ],200); 

    }
    /**
     * digunakan untuk menyalin matakuliah
     */
    public function salinmatkul (Request $request,$id)
    {
        $this->validate($request, [           
            'dari_tahun_akademik'=>'required',  
            'prodi_id'=>'required'          
        ]);

        $dari_tahun_akademik=$request->input('dari_tahun_akademik');
        $prodi_id=$request->input('prodi_id');
        
        $sql = "INSERT INTO pe3_matakuliah (id,
                                            id_group,
                                            nama_group,
                                            group_alias,
                                            kmatkul,
                                            nmatkul,
                                            sks,
                                            idkonsentrasi,
                                            ispilihan,
                                            islintas_prodi,
                                            semester,
                                            sks_tatap_muka,
                                            sks_praktikum,
                                            sks_praktik_lapangan,
                                            minimal_nilai,
                                            syarat_skripsi,
                                            status,
                                            ta,
                                            kjur,
                                            created_at,
                                            updated_at
                                        )
                SELECT UUID(),
                    id_group,
                    nama_group,
                    group_alias,
                    kmatkul,
                    nmatkul,
                    sks,
                    idkonsentrasi,
                    ispilihan,
                    islintas_prodi,
                    semester,
                    sks_tatap_muka,
                    sks_praktikum,
                    sks_praktik_lapangan,
                    minimal_nilai,
                    syarat_skripsi,
                    status,
                    $id AS ta,
                    $prodi_id AS kjur,
                    NOW() AS created_at,
                    NOW() AS updated_at 
                FROM pe3_matakuliah 
                WHERE 
                    ta=$dari_tahun_akademik AND 
                    kjur=$prodi_id AND 
                    kmatkul 
                        NOT IN (
                        SELECT 
                            kmatkul 
                        FROM pe3_matakuliah 
                        WHERE ta=$id AND 
                            kjur=$prodi_id
            )";                
            \DB::statement($sql);
            
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
                                ->where('ta',$id)   
                                ->orderBy('semester','ASC')                      
                                ->orderBy('kmatkul','ASC')                      
                                ->get();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $matakuliah,
                                                        'object_id'=>'N.A', 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => "Menyalin data matakuliah dari tahun $dari_tahun_akademik ke $id berhasil."
                                                    ]);
                                                    
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',  
                                'matakuliah'=>$matakuliah,                                                                                                                                   
                                'message' => "Menyalin data matakuliah dari tahun $dari_tahun_akademik ke $id berhasil."
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
            $ta=$matakuliah->ta;
            $this->validate($request, [
                                        'id_group'=>'required',
                                        'kmatkul'=>[
                                                        'required',
                                                        Rule::unique('pe3_matakuliah')->ignore($matakuliah->kmatkul,'kmatkul')->where(function ($query) use ($ta,$kjur) {
                                                            $query->where('ta', $ta)
                                                                ->where('kjur',$kjur);
                                                        })
                                                    ],           
                                        
                                        'nmatkul'=>[
                                                        'required',                                                        
                                                        Rule::unique('pe3_matakuliah')->ignore($matakuliah->nmatkul,'nmatkul')->where(function ($query) use ($ta,$kjur) {
                                                            $query->where('ta', $ta)
                                                                ->where('kjur',$kjur);
                                                        })
                                                    ],           
                                        'sks'=>'required|numeric',            
                                        'semester'=>'required|numeric',            
                                        'sks_tatap_muka'=>'required|numeric',            
                                        'minimal_nilai'=>'required',            
                                        'ta'=>'required',            
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
            $matakuliah->kmatkul = $request->input('kmatkul');            
            $matakuliah->nmatkul = $request->input('nmatkul');            
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
                                    'kmatkul'=>$matakuliah->matakul,
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
            'ta'=>'required',
            'prodi_id'=>'required',
            'semester_akademik'=>'required'
        ]);
        
        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
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
                                    status,
                                    ta
                                '))       
                                ->where('kjur',$prodi_id)
                                ->where('ta',$ta)   
                                ->whereNotIn('id',function($query) use($ta,$prodi_id,$semester_akademik){
                                    $query->select('matkul_id')
                                        ->from('pe3_penyelenggaraan')
                                        ->where('kjur',$prodi_id)
                                        ->where('tahun',$ta)
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
                                ],200);     
    }
}