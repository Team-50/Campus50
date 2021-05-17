<?php

namespace App\Http\Controllers\Kemahasiswaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KemahasiswaanProfilController extends Controller {  
    /**
     * melakukan pencarian untuk nim
     */
    public function search(Request $request)
    {
        $this->hasPermissionTo('KEMAHASISWAAN-PROFIL-MHS_SHOW');

        $this->validate($request,[
            'search'=>'required'        
        ]);
        
        $daftar_mhs = \DB::table('pe3_register_mahasiswa AS A')
                            ->select(\DB::raw('
                                A.user_id,
                                A.nim,
                                B.nama_mhs,
                                CONCAT(\'[\',A.nim,\'] \',B.nama_mhs) AS nama_mhs_alias,
                                D.nama_prodi,
                                C.foto
                            '))
                            ->join('pe3_formulir_pendaftaran AS B','A.user_id','B.user_id')
                            ->join('users AS C','A.user_id','C.id')
                            ->join('pe3_prodi AS D','A.kjur','D.id')
                            ->whereRaw('(A.nim LIKE \''.$request->input('search').'%\' OR B.nama_mhs LIKE \'%'.$request->input('search').'%\')')                                                    
                            ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'daftar_mhs'=>$daftar_mhs,  
                                    'jumlah'=>$daftar_mhs->count(),                                                                                                                                   
                                    'message'=>'Daftar Mahasiswa berhasil diperoleh.'
                                ],200); 
    
    }
    /**
     * melakukan pencarian untuk nim
     */
    public function searchnonampulan(Request $request)
    {
        $this->hasPermissionTo('KEMAHASISWAAN-PROFIL-MHS_SHOW');

        $this->validate($request,[
            'search'=>'required'        
        ]);
        
        $daftar_mhs = \DB::table('pe3_register_mahasiswa AS A')
                            ->select(\DB::raw('
                                A.user_id,
                                A.nim,
                                B.nama_mhs,
                                CONCAT(\'[\',A.nim,\'] \',B.nama_mhs) AS nama_mhs_alias,
                                D.nama_prodi,
                                C.foto
                            '))
                            ->join('pe3_formulir_pendaftaran AS B','A.user_id','B.user_id')
                            ->join('users AS C','A.user_id','C.id')
                            ->join('pe3_prodi AS D','A.kjur','D.id')
                            ->whereNotIn('A.user_id',function($query){
                                $query->select('user_id')
                                    ->from('pe3_nilai_konversi1');                                    
                            })
                            ->where('A.nim','LIKE',$request->input('search').'%')
                            ->orWhere('B.nama_mhs', 'LIKE', '%'.$request->input('search').'%')
                            ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'daftar_mhs'=>$daftar_mhs,  
                                    'jumlah'=>$daftar_mhs->count(),                                                                                                                                   
                                    'message'=>'Daftar Mahasiswa berhasil diperoleh.'
                                ],200); 
    
    }
    /**
     * melakukan reset password mahasiswa
     */
    public function resetpassword(Request $request)
    {
        $this->hasPermissionTo('KEMAHASISWAAN-PROFIL-MHS_UPDATE');

        $this->validate($request,[
            'user_id'=>'required|exists:pe3_register_mahasiswa,user_id'        
        ]);
        
        $user=User::find($request->input('user_id'));

        $user->password=Hash::make(12345678);
        $user->save();

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',                                        
                                'message'=>'Reset password Mahasiswa '.$user->name.'berhasil diperoleh.'
                            ],200);
    }
}