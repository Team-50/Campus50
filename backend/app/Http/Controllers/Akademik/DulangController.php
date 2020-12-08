<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;

use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class DulangController extends Controller 
{
    /**
     * daftar dulang id yang tidak ada didalam krs
     */
    public function dulangnotinkrs (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-MHS_BROWSE');

        $this->validate($request, [      
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',                 
        ]);
        
        $nim=$request->input('nim');

        $daftar_dulang=DulangModel::select(\DB::raw('
                                    id AS value,
                                    CONCAT("DAFTAR ULANG T.A ",tahun,idsmt) AS text
                                '))       
                                ->where('nim',$nim)                                
                                ->where('k_status','A')   
                                ->whereNotIn('id',function($query) use($nim){
                                    $query->select('dulang_id')
                                        ->from('pe3_krs')
                                        ->where('nim',$nim);
                                })
                                ->orderBy('tahun','ASC')                      
                                ->orderBy('idsmt','ASC')                      
                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'daftar_dulang'=>$daftar_dulang,                                                                                                                                   
                                    'message'=>'daftar dulang mahasiswa yang tidak ada di KRS dengan status aktif berhasil diperoleh'
                                ],200);  
    }
    /**
     * cek apakah mahasiswa telah melakukan daftar ulang
     */
    public function cekdulangkrs (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-MHS_SHOW');

        $this->validate($request, [      
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',     
            'ta'=>'required',
            'idsmt'=>'required'
        ]);
        
        $nim=$request->input('nim');
        $ta=$request->input('ta');
        $idsmt=$request->input('idsmt');
        
        $isdulang = DulangModel::where('nim',$nim)
                                ->where('tahun',$ta)
                                ->where('idsmt',$idsmt)
                                ->where('k_status','A')
                                ->exists();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'isdulang'=>$isdulang,                                                                                                                                   
                                    'message'=>'Cek dulang mahasiswa'
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