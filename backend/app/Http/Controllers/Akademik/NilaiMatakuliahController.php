<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Akademik\PembagianKelasModel;
use App\Models\Akademik\PembagianKelasPesertaModel;
use App\Models\Akademik\KRSModel;
use App\Models\Akademik\KRSMatkulModel;

use App\Models\Akademik\NilaiMatakuliahModel;

use Ramsey\Uuid\Uuid;

class NilaiMatakuliahController extends Controller
{
    /**
     * daftar penyelenggaraan
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-NILAI-MATAKULIAH_BROWSE');

        $this->validate($request, [
            'ta'=>'required',
            'semester_akademik'=>'required',
            'prodi_id'=>'required'
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        $semester_akademik=$request->input('semester_akademik');
        
        $daftar_nilai=[];

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'daftar_nilai'=>$daftar_nilai,                                                                                                                                   
                                    'message'=>'Fetch data penyelenggaraan matakuliah berhasil.'
                                ],200); 
    }
    public function pesertakelas (Request $request,$id)
    {
        $pembagian = PembagianKelasModel::find($id); 
        
        if (is_null($pembagian))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Kelas Mahasiswa dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $peserta=PembagianKelasPesertaModel::select(\DB::raw('
                                        pe3_kelas_mhs_peserta.id,
                                        pe3_krs.nim,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.idkelas,
                                        pe3_register_mahasiswa.tahun,
                                        pe3_register_mahasiswa.kjur,
                                        pe3_nilai_matakuliah.n_kuan,
                                        pe3_nilai_matakuliah.n_kual,
                                        pe3_nilai_matakuliah.created_at,
                                        pe3_nilai_matakuliah.updated_at
                                    '))
                                    ->join('pe3_krsmatkul','pe3_krsmatkul.id','pe3_kelas_mhs_peserta.krsmatkul_id')
                                    ->join('pe3_krs','pe3_krs.id','pe3_krsmatkul.krs_id')                            
                                    ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_krs.user_id')
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_register_mahasiswa.user_id')
                                    ->leftJoin('pe3_nilai_matakuliah','pe3_nilai_matakuliah.krsmatkul_id','pe3_kelas_mhs_peserta.krsmatkul_id')
                                    ->where('pe3_kelas_mhs_peserta.kelas_mhs_id',$id)
                                    ->get();
            return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',                                                                                         
                                'peserta'=>$peserta,                                            
                                'message'=>"Daftar Peserta MHS dari Kelas MHS dengan id ($id) berhasil diperoleh."
                            ],200);
        }
    }
    public function perkrs (Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_SHOW');

        $pembagian = PembagianKelasModel::find($id); 
        
        $krs=KRSModel::select(\DB::raw('
                        pe3_krs.id,
                        pe3_krs.nim,
                        pe3_formulir_pendaftaran.nama_mhs,
                        pe3_krs.kjur,
                        pe3_krs.tahun,
                        pe3_krs.idsmt,
                        pe3_krs.tasmt,
                        pe3_krs.sah,
                        pe3_krs.created_at,
                        pe3_krs.updated_at
                    '))
                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_krs.user_id')
                    ->find($id);

        $daftar_matkul=[];

        if (is_null($krs))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["KRS dengan ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $daftar_matkul=KRSMatkulModel::select(\DB::raw('
                                            pe3_krsmatkul.id,
                                            pe3_penyelenggaraan.kmatkul,
                                            pe3_penyelenggaraan.nmatkul,
                                            pe3_penyelenggaraan.sks,
                                            pe3_penyelenggaraan.semester,
                                            COALESCE(pe3_kelas_mhs.nmatkul,\'N.A\') AS nama_kelas,
                                            pe3_nilai_matakuliah.n_kuan,
                                            pe3_nilai_matakuliah.n_kual,
                                            pe3_nilai_matakuliah.created_at,
                                            pe3_nilai_matakuliah.updated_at
                                        '))
                                        ->join('pe3_penyelenggaraan','pe3_penyelenggaraan.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->leftJoin('pe3_kelas_mhs_peserta','pe3_kelas_mhs_peserta.krsmatkul_id','pe3_krsmatkul.id')
                                        ->leftJoin('pe3_kelas_mhs','pe3_kelas_mhs.id','pe3_kelas_mhs_peserta.kelas_mhs_id')
                                        ->leftJoin('pe3_nilai_matakuliah','pe3_nilai_matakuliah.krsmatkul_id','pe3_krsmatkul.id')
                                        ->where('pe3_krsmatkul.krs_id',$krs->id)
                                        ->orderBy('semester','asc')
                                        ->orderBy('kmatkul','asc')
                                        ->get();
            
        }
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'krs'=>$krs,                                                                                                                                   
                                    'krsmatkul'=>$daftar_matkul,                                                                                                                                   
                                    'jumlah_matkul'=>$daftar_matkul->count(),                                                                                                                                   
                                    'jumlah_sks'=>$daftar_matkul->sum('sks'),                                                                                                                                   
                                    'message'=>'Fetch data krs dan detail krs mahasiswa berhasil diperoleh' 
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    }
    public function storeperkrs(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-NILAI-MATAKULIAH_STORE');
        
        $daftar_nilai=json_decode($request->input('daftar_nilai'),true);
        $request->merge(['daftar_nilai'=>$daftar_nilai]);        

        $this->validate($request, [      
            'krs_id'=>'required|exists:pe3_krs,id',     
            'daftar_nilai.*'=>'required',            
        ]);
        $krs_id=$request->input('krs_id');
        
        $krs=KRSModel::find($krs_id);

        $jumlah_matkul=0;
        foreach ($daftar_nilai as $v)
        {
            $krsmatkul_id=$v['krsmatkul_id'];
            $n_kuan=(float)$v['n_kuan'];
            $n_kual=$v['n_kual'];

            if ($n_kuan != '' && $n_kual != '')
            {
                $nilai=NilaiMatakuliahModel::where('krsmatkul_id',$krsmatkul_id)->first();

                if (is_null($nilai) )
                {
                    $krsmatkul=KRSMatkulModel::select(\DB::raw('
                                            pe3_krsmatkul.penyelenggaraan_id,
                                            pe3_kelas_mhs_penyelenggaraan.penyelenggaraan_dosen_id,                                            
                                            pe3_kelas_mhs_peserta.kelas_mhs_id                                            
                                        '))
                                        ->join('pe3_penyelenggaraan','pe3_penyelenggaraan.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->leftJoin('pe3_kelas_mhs_peserta','pe3_kelas_mhs_peserta.krsmatkul_id','pe3_krsmatkul.id')
                                        ->leftJoin('pe3_kelas_mhs_penyelenggaraan','pe3_kelas_mhs_penyelenggaraan.kelas_mhs_id','pe3_kelas_mhs_peserta.kelas_mhs_id')
                                        ->leftJoin('pe3_nilai_matakuliah','pe3_nilai_matakuliah.krsmatkul_id','pe3_krsmatkul.id')
                                        ->where('pe3_krsmatkul.id',$krsmatkul_id)                                        
                                        ->first();
                    
                    
                    $nilai=NilaiMatakuliahModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'krsmatkul_id'=>$krsmatkul_id,
                        'penyelenggaraan_id'=>$krsmatkul->penyelenggaraan_id,
                        'penyelenggaraan_dosen_id'=>$krsmatkul->penyelenggaraan_dosen_id,
                        'kelas_mhs_id'=>$krsmatkul->kelas_mhs_id, 
                        'user_id_mhs'=>$krs->user_id, 
                        'user_id_created'=>$this->getUserid(), 
                        'user_id_updated'=>$this->getUserid(),
                        'krs_id'=>$krs_id,
                        
                        'persentase_absen'=>0,
                        'persentase_quiz'=>0,
                        'persentase_tugas_individu'=>0,
                        'persentase_tugas_kelompok'=>0,
                        'persentase_uts'=>0,
                        'persentase_uas'=>0,

                        'nilai_absen'=>0,
                        'nilai_quiz'=>0,
                        'nilai_tugas_individu'=>0,
                        'nilai_tugas_kelompok'=>0,
                        'nilai_uts'=>0,
                        'nilai_uas'=>0,
                        'n_kuan'=>$n_kuan,
                        'n_kual'=>$n_kual,
                        'n_mutu'=>\App\Helpers\HelperAkademik::getNilaiMutu($n_kual),
                        'created_at'=>\Carbon\Carbon::now(),
                        'updated_at'=>\Carbon\Carbon::now()
                    ]);
                    \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $nilai, 
                                                                'object_id' => $nilai->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menyimpan Nilai Huruf ('.$nilai->n_kual.') dan Nilai Angka '.$nilai->n_kuan.' untuk Matakuliah dengan krsmatkul_id ('.$nilai->id.') berhasil dilakukan'
                                                            ]);
                    $jumlah_matkul+=1;
                }                
                else
                {
                    $n_kuan_lama=$nilai->n_kuan;
                    $n_kual_lama=$nilai->n_kual;
                    $nilai->n_kuan=$n_kuan;
                    $nilai->n_kual=$n_kual;
                    $nilai->n_mutu=\App\Helpers\HelperAkademik::getNilaiMutu($n_kual);
                    
                    $nilai->user_id_updated=$this->getUserid();
                    $nilai->save();                

                    \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $nilai, 
                                                                'object_id' => $nilai->id, 
                                                                'user_id' => $this->getUserid(),                                                                 
                                                                'message' => 'Mengubah Nilai Matakuliah yang lama dengan nilai huruf ('.$n_kual_lama.') dan Nilai Angka '.$n_kuan_lama.' menjadi Nilai Huruf ('.$nilai->n_kual.') dan Nilai Angka '.$nilai->n_kuan.' untuk Matakuliah dengan krsmatkul_id ('.$nilai->id.') berhasil dilakukan'
                                                            ]);
                    $jumlah_matkul+=1;
                }
            }
        }       
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store', 
                                    'daftar_nilai'=>$daftar_nilai,                                     
                                    'message'=>"Nilai ($jumlah_matkul) matakuliah telah tersimpan dengan berhasil" 
                                ],200);     
    }
}
