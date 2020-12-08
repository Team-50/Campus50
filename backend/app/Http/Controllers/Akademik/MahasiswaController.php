<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Akademik\RegisterMahasiswaModel;

class MahasiswaController extends Controller
{
    /**
     * digunakan untuk mendapatkan data mahasiswa [nim,nirm,nama_prodi,nama_kelas,dosen_wali]
     */
    public function biodatamhs1 (Request $request,$id)
    {
        $mahasiswa=RegisterMahasiswaModel::select(\DB::raw('
                                    pe3_register_mahasiswa.user_id,
                                    pe3_register_mahasiswa.nim,
                                    pe3_register_mahasiswa.nirm,
                                    pe3_register_mahasiswa.kjur,
                                    pe3_prodi.nama_prodi,
                                    pe3_register_mahasiswa.idkelas,
                                    pe3_kelas.nkelas AS nama_kelas,
                                    pe3_register_mahasiswa.k_status,
                                    pe3_status_mahasiswa.n_status,
                                    pe3_register_mahasiswa.dosen_id,
                                    pe3_dosen.nama_dosen AS dosen_wali,
                                    pe3_register_mahasiswa.tahun,
                                    pe3_register_mahasiswa.idsmt                                    
                                '))
                                ->join('pe3_prodi','pe3_prodi.id','pe3_register_mahasiswa.kjur')
                                ->join('pe3_kelas','pe3_kelas.idkelas','pe3_register_mahasiswa.idkelas')
                                ->join('pe3_status_mahasiswa','pe3_status_mahasiswa.k_status','pe3_register_mahasiswa.k_status')
                                ->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_register_mahasiswa.dosen_id')
                                ->find($id);

        if (is_null($mahasiswa))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Data Mahasiswa dengan id ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                
                                        'mahasiswa'=>$mahasiswa,                
                                        'message'=>"Data mahasiswa dengan id ($id) berhasil diperoleh"
                                    ],200);
        }
    }
    /**
     * digunakan untuk mendapatkan data mahasiswa [nim,nirm,nama_prodi,nama_kelas,dosen_wali]
     */
    public function biodatamhs2 (Request $request,$id)
    {
        $mahasiswa=RegisterMahasiswaModel::select(\DB::raw('
                                    pe3_register_mahasiswa.user_id,
                                    pe3_register_mahasiswa.nim,
                                    pe3_register_mahasiswa.nirm,
                                    pe3_register_mahasiswa.kjur,
                                    pe3_prodi.nama_prodi,
                                    pe3_register_mahasiswa.idkelas,
                                    pe3_kelas.nkelas AS nama_kelas,
                                    pe3_register_mahasiswa.k_status,
                                    pe3_status_mahasiswa.n_status,
                                    pe3_register_mahasiswa.dosen_id,
                                    pe3_dosen.nama_dosen AS dosen_wali,
                                    pe3_register_mahasiswa.tahun,
                                    pe3_register_mahasiswa.idsmt                                    
                                '))
                                ->join('pe3_prodi','pe3_prodi.id','pe3_register_mahasiswa.kjur')
                                ->join('pe3_kelas','pe3_kelas.idkelas','pe3_register_mahasiswa.idkelas')
                                ->join('pe3_status_mahasiswa','pe3_status_mahasiswa.k_status','pe3_register_mahasiswa.k_status')
                                ->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_register_mahasiswa.dosen_id')
                                ->find($id);

        if (is_null($mahasiswa))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Data Mahasiswa dengan id ($id) gagal diperoleh"]
                                ],200); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                
                                        'mahasiswa'=>$mahasiswa,                
                                        'message'=>"Data mahasiswa dengan id ($id) berhasil diperoleh"
                                    ],200);
        }
    }
}
