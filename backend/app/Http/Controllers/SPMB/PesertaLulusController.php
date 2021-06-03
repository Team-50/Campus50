<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\SPMB\NilaiUjianPMBModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

use Ramsey\Uuid\Uuid;

class PesertaLulusController extends Controller {             
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang telah mengisi formulir pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasAnyPermission(['SPMB-PMB-NILAI-UJIAN_BROWSE']);

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');

        $data = FormulirPendaftaranModel::select(\DB::raw('
                        users.id,
                        pe3_formulir_pendaftaran.no_formulir,
                        users.username,
                        users.name,
                        users.nomor_hp,
                        COALESCE(pe3_nilai_ujian_pmb.nilai,\'N.A\') AS nilai,
                        pe3_nilai_ujian_pmb.ket_lulus,
                        CASE
                            WHEN pe3_nilai_ujian_pmb.ket_lulus IS NULL THEN \'N.A\'
                            WHEN pe3_nilai_ujian_pmb.ket_lulus=0 THEN \'TIDAK LULUS\'
                            WHEN pe3_nilai_ujian_pmb.ket_lulus=1 THEN \'LULUS\'
                        END AS status,
                        pe3_kelas.nkelas,
                        users.active,
                        users.foto,
                        users.created_at,
                        users.updated_at
                    '))
                    ->join('users','pe3_formulir_pendaftaran.user_id','users.id')                    
                    ->join('pe3_kelas','pe3_formulir_pendaftaran.idkelas','pe3_kelas.idkelas')                    
                    ->leftJoin('pe3_nilai_ujian_pmb','pe3_formulir_pendaftaran.user_id','pe3_nilai_ujian_pmb.user_id')                    
                    ->where('users.ta',$ta)
                    ->where('kjur1',$prodi_id)            
                    ->where('pe3_nilai_ujian_pmb.ket_lulus',1)            
                    ->whereNotNull('pe3_formulir_pendaftaran.idkelas')   
                    ->where('users.active',1)    
                    ->orderBy('users.name','ASC') 
                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'pmb'=>$data,
                                'message'=>'Fetch data calon mahasiswa baru berhasil diperoleh'
                            ],200);  
    }    
}