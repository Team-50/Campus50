<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;

use Illuminate\Http\Request;

use App\Models\Keuangan\TransaksiDetailModel;

use Ramsey\Uuid\Uuid;

class DulangMahasiswaLamaController extends Controller 
{
    /**
     * daftar mahasiswa
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-LAMA_BROWSE');

        $this->validate($request, [           
            'ta'=>'required',
            'prodi_id'=>'required',
            'idsmt'=>'required',
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        $idsmt=$request->input('idsmt');
        
        $data = DulangModel::select(\DB::raw('
                                pe3_dulang.id,
                                pe3_dulang.user_id,
                                pe3_formulir_pendaftaran.no_formulir,
                                pe3_dulang.nim,
                                pe3_register_mahasiswa.nirm,
                                pe3_formulir_pendaftaran.nama_mhs,                                
                                pe3_dulang.idkelas,                                                                                                      
                                pe3_dulang.k_status,                                                                                                      
                                pe3_status_mahasiswa.n_status,                                                                                                      
                                pe3_dulang.created_at,                                      
                                pe3_dulang.updated_at                                      
                            '))
                            ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_dulang.user_id')
                            ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_dulang.user_id')
                            ->join('pe3_status_mahasiswa','pe3_status_mahasiswa.k_status','pe3_dulang.k_status')
                            ->where('pe3_dulang.tahun',$ta)   
                            ->where('pe3_dulang.idsmt',$idsmt)   
                            ->where('pe3_register_mahasiswa.kjur',$prodi_id)
                            ->orderBy('pe3_dulang.idsmt','desc')
                            ->orderBy('nama_mhs','desc')
                            ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'mahasiswa'=>$data,                                                                                                                                   
                                    'message'=>'Fetch data daftar ulang mahasiswa baru berhasil.'
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
        $this->hasPermissionTo('AKADEMIK-MATAKULIAH_DESTROY');

        $dulang = DulangModel::find($id); 
        
        if (is_null($mahasiswa))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Daftar Ulang Mahasiswa Baru ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $matakuliah, 
                                                                'object_id' => $matakuliah->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus daftar ulang mahasiswa baru dengan id ('.$dulang->user_id.') berhasil'
                                                            ]);
            $register_mahasiswa=$dulang->register_mahasiswa;
            $register_mahasiswa->delete();
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Daftar Ulang dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}