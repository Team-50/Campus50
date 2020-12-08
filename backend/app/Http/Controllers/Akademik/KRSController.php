<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Akademik\PenyelenggaraanMatakuliahModel;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;
use App\Models\Akademik\KRSModel;
use App\Models\Akademik\KRSMatkulModel;

use App\Models\System\ConfigurationModel;

use Ramsey\Uuid\Uuid;

class KRSController extends Controller
{
    /**
     * daftar krs
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_BROWSE');
        $daftar_krs=[];

        if ($this->hasRole(['superadmin','akademik','programstudi','puslahta']))
        {
            $this->validate($request, [
                'ta'=>'required',
                'semester_akademik'=>'required',
                'prodi_id'=>'required'
            ]);

            $ta=$request->input('ta');
            $prodi_id=$request->input('prodi_id');
            $semester_akademik=$request->input('semester_akademik');

            $daftar_krs = KRSModel::select(\DB::raw('
                                    pe3_krs.id,
                                    pe3_krs.nim,
                                    pe3_formulir_pendaftaran.nama_mhs,
                                    pe3_krs.tasmt,
                                    pe3_krs.sah,
                                    pe3_formulir_pendaftaran.ta AS tahun_masuk,
                                    0 AS jumlah_matkul,
                                    0 AS jumlah_sks,
                                    pe3_krs.created_at,
                                    pe3_krs.updated_at
                                '))
                                ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_krs.user_id')
                                ->where('pe3_krs.kjur',$prodi_id)
                                ->where('pe3_krs.tahun',$ta)
                                ->where('pe3_krs.idsmt',$semester_akademik)
                                ->orderBy('nama_mhs','ASC')
                                ->get();

            $daftar_krs->transform(function ($item,$key) {                
                $item->jumlah_matkul=\DB::table('pe3_krsmatkul')->where('krs_id',$item->id)->count();
                $item->jumlah_sks=\DB::table('pe3_krsmatkul')
                                        ->join('pe3_penyelenggaraan','pe3_penyelenggaraan.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->where('krs_id',$item->id)->sum('pe3_penyelenggaraan.sks');
                return $item;
            });
        }
        else if ($this->hasRole('mahasiswa'))
        {
            $daftar_krs = KRSModel::select(\DB::raw('
                                    pe3_krs.id,
                                    pe3_krs.nim,
                                    pe3_formulir_pendaftaran.nama_mhs,
                                    pe3_krs.tasmt,
                                    pe3_krs.sah,
                                    pe3_formulir_pendaftaran.ta AS tahun_masuk,
                                    0 AS jumlah_matkul,
                                    0 AS jumlah_sks,
                                    pe3_krs.created_at,
                                    pe3_krs.updated_at
                                '))
                                ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_krs.user_id')
                                ->where('nim',$this->getUsername())
                                ->orderBy('tasmt','DESC')
                                ->get();

            $daftar_krs->transform(function ($item,$key){
                
                $item->jumlah_matkul=\DB::table('pe3_krsmatkul')->where('krs_id',$item->id)->count();
                $item->jumlah_sks=\DB::table('pe3_krsmatkul')
                                        ->join('pe3_penyelenggaraan','pe3_penyelenggaraan.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->where('krs_id',$item->id)->sum('pe3_penyelenggaraan.sks');
                return $item;
            });
        }
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'daftar_krs'=>$daftar_krs,                                                                                                                                   
                                    'message'=>'Daftar krs mahasiswa berhasil diperoleh' 
                                ],200);  
        
    }
    public function show (Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_SHOW');

        $krs=KRSModel::select(\DB::raw('
                        pe3_krs.id,
                        pe3_krs.nim,
                        pe3_formulir_pendaftaran.nama_mhs,
                        pe3_krs.jumlah_matkul_1,
                        pe3_krs.jumlah_sks_1,
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
                                            A.kmatkul,
                                            A.nmatkul,
                                            A.sks,
                                            A.semester,
                                            B.nama_dosen AS nama_dosen_penyelenggaraan,
                                            F.nama_dosen AS nama_dosen_kelas,
                                            \'\' AS nama_dosen,
                                            COALESCE(pe3_kelas_mhs.nmatkul,\'N.A\') AS nama_kelas,
                                            pe3_krsmatkul.created_at,
                                            pe3_krsmatkul.updated_at
                                        '))
                                        ->join('pe3_penyelenggaraan AS A','A.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->leftJoin('pe3_dosen AS B','A.user_id','B.user_id')                                        
                                        ->leftJoin('pe3_kelas_mhs_peserta AS C','pe3_krsmatkul.id','C.krsmatkul_id') 
                                        ->leftJoin('pe3_kelas_mhs','pe3_kelas_mhs.id','C.kelas_mhs_id')                                       
                                        ->leftJoin('pe3_kelas_mhs_penyelenggaraan AS D','D.kelas_mhs_id','C.kelas_mhs_id')                                        
                                        ->leftJoin('pe3_penyelenggaraan_dosen AS E','E.id','D.penyelenggaraan_dosen_id')                                        
                                        ->leftJoin('pe3_dosen AS F','F.user_id','E.user_id')                                        
                                        ->where('krs_id',$krs->id)
                                        ->orderBy('semester','asc')
                                        ->orderBy('kmatkul','asc')
                                        ->get();
            
            $daftar_matkul->transform(function ($item,$key) {            
                if (is_null($item->nama_dosen_kelas) && is_null($item->nama_dosen_penyelenggaraan))
                {
                    $item->nama_dosen='N.A';
                }     
                else
                {
                    $item->nama_dosen=is_null($item->nama_dosen_kelas) ? $item->nama_dosen_penyelenggaraan:$item->nama_dosen_kelas;                
                }
                return $item;
            });
            $krs->jumlah_matkul_1=$daftar_matkul->count();
            $krs->jumlah_sks_1=$daftar_matkul->sum('sks');
            $krs->save();
            
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'krs'=>$krs,                                                                                                                                   
                                    'krsmatkul'=>$daftar_matkul,                                                                                                                                   
                                    'jumlah_matkul'=>$krs->jumlah_matkul_1,                                                                                                                                   
                                    'jumlah_sks'=>$krs->jumlah_sks_1,                                                                                                                                   
                                    'message'=>'Fetch data krs dan detail krs mahasiswa berhasil diperoleh' 
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
        }        
    }
    public function penyelenggaraan (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_SHOW');

        $this->validate($request, [
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',     
            'prodi_id'=>'required',     
            'ta'=>'required',     
            'semester_akademik'=>'required',     
            'pid'=>'required',     
        ]);
        
        $prodi_id=$request->input('prodi_id');
        $ta=$request->input('ta');
        $nim=$request->input('nim');
        $semester_akademik=$request->input('semester_akademik');
        
        $datamhs=RegisterMahasiswaModel::select(\DB::raw('tahun'))
                                        ->where('nim',$nim)
                                        ->first();
                                        
        $penyelenggaraan=PenyelenggaraanMatakuliahModel::select(\DB::raw('
                                    id,
                                    kmatkul,                                    
                                    nmatkul,
                                    sks,
                                    semester,
                                    ta_matkul
                                '))       
                                ->where('tahun',$ta)                                  
                                ->where('idsmt',$semester_akademik)                                  
                                ->where('kjur',$prodi_id)                                  
                                ->where('ta_matkul',$datamhs->tahun)
                                ->whereNotIn('id',function($query) use ($nim,$ta,$semester_akademik){
                                    $query->select('penyelenggaraan_id')
                                        ->from('pe3_krsmatkul')
                                        ->where('nim',$nim)                                        
                                        ->where('tahun',$ta)                                        
                                        ->where('idsmt',$semester_akademik);                                        
                                })
                                ->orderBy('semester','ASC')                                                      
                                ->orderBy('kmatkul','ASC')                                                      
                                ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'penyelenggaraan'=>$penyelenggaraan,                                                                                                                                                                       
                                    'message'=>'Fetch data matakuliah yang diselenggarakan dan belum terdaftar di KRS berhasil diperoleh' 
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * digunakan untul menyimpan krs mahasiswa
     */
    public function store (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_STORE');
        
        $this->validate($request, [
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',     
            'dulang_id'=>'required|exists:pe3_dulang,id',     
        ]);
        
        $nim=$request->input('nim');
        $dulang_id=$request->input('dulang_id');

        $dulang = DulangModel::find($dulang_id);

        $krs=KRSModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'user_id'=>$dulang->user_id,
            'dulang_id'=>$dulang_id,
            'nim'=>$nim,
            'kjur'=>$dulang->register_mahasiswa->kjur, 
            'idsmt'=>$dulang->idsmt, 
            'tahun'=>$dulang->tahun, 
            'tasmt'=>$dulang->tasmt,         
            'sah'=>0,        
        ]);
        \App\Models\System\ActivityLog::log($request,[
                                                    'object' => $krs, 
                                                    'object_id' => $krs->id, 
                                                    'user_id' => $this->getUserid(), 
                                                    'message'=>"Menyimpan krs mahasiswa berhasil dilakukan."
                                                ]);
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',  
                                    'krs'=>$krs,                                                                                                                                   
                                    'message'=>'menyimpan krs mahasiswa berhasil'
                                ],200);  
    }
    public function storematkul (Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_STORE');

        $matkul_selected=json_decode($request->input('matkul_selected'),true);
        $request->merge(['matkul_selected'=>$matkul_selected]);        

        $this->validate($request, [      
            'krs_id'=>'required|exists:pe3_krs,id',     
            'matkul_selected.*'=>'required',            
        ]);
        $krs_id=$request->input('krs_id');
        
        $krs=KRSModel::find($krs_id);

        $daftar_matkul=[];
        foreach ($matkul_selected as $v)
        {
            $daftar_matkul[]=[
                'id'=>Uuid::uuid4()->toString(),
                'krs_id'=>$krs_id,
                'nim'=>$krs->nim,
                'penyelenggaraan_id'=>$v['id'],
                'batal'=>0,
                'kjur'=>$krs->kjur,
                'idsmt'=>$krs->idsmt,
                'tahun'=>$krs->tahun,                
                'created_at'=>\Carbon\Carbon::now(),
                'updated_at'=>\Carbon\Carbon::now()
            ];
        }
        KRSMatkulModel::insert($daftar_matkul);
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',                                                                                                                                                                         
                                    'message'=>(count($daftar_matkul)).' Matakuliah baru telah berhasil ditambahkan'
                                ],200);  
    }
    public function cekkrs ($request)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_SHOW');

        $this->validate($request, [      
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',     
            'ta'=>'required',
            'idsmt'=>'required'
        ]);
        
        $nim=$request->input('nim');
        $ta=$request->input('ta');
        $idsmt=$request->input('idsmt');

        $isdulang = KRSModel::where('nim',$nim)
                                ->where('tahun',$ta)
                                ->where('idsmt',$idsmt)                                
                                ->exists();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'iskrs'=>$iskrs,                                                                                                                                   
                                    'message'=>'Cek krs mahasiswa'
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
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_DESTROY');

        $krs = KRSModel::find($id); 
        
        if (is_null($krs))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["KRS dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $krs, 
                                                                'object_id' => $krs->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus KRS dengan id ('.$id.') berhasil'
                                                            ]);
            $krs->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"KRS dengan ID ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroymatkul(Request $request,$id)
    { 
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_DESTROY');

        $krsmatkul = KRSMatkulModel::find($id); 
        
        if (is_null($krsmatkul))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Matakuliah dalam KRS dengan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $krsmatkul, 
                                                            'object_id' => $krsmatkul->id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message' => 'Menghapus matakuliah KRS dengan id ('.$id.') berhasil'
                                                        ]);
            $krsmatkul->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message' => 'Menghapus matakuliah KRS dengan id ('.$id.') berhasil'
                                    ],200);         
        }
                  
    }
    public function printpdf(Request $request,$id)
    {
        $this->hasPermissionTo('AKADEMIK-PERKULIAHAN-KRS_SHOW');

        $krs=KRSModel::select(\DB::raw('
                        pe3_krs.id,
                        pe3_krs.nim,
                        pe3_register_mahasiswa.nirm,
                        pe3_formulir_pendaftaran.nama_mhs,
                        pe3_formulir_pendaftaran.jk,
                        pe3_krs.kjur,
                        pe3_prodi.nama_prodi,
                        pe3_krs.tahun,
                        pe3_krs.idsmt,
                        \'\' AS nama_semester,
                        pe3_krs.tasmt,
                        pe3_krs.sah,
                        pe3_krs.created_at,
                        pe3_krs.updated_at
                    '))
                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_krs.user_id')
                    ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_krs.user_id')
                    ->join('pe3_prodi','pe3_register_mahasiswa.kjur','pe3_prodi.id')
                    ->find($id);

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
            $krs->nama_semester=\App\Helpers\HelperAkademik::getSemester($krs->idsmt);

            $daftar_matkul=KRSMatkulModel::select(\DB::raw('
                                            pe3_krsmatkul.id,
                                            A.kmatkul,
                                            A.nmatkul,
                                            A.sks,
                                            A.semester,
                                            B.nama_dosen AS nama_dosen_penyelenggaraan,
                                            F.nama_dosen AS nama_dosen_kelas,
                                            \'\' AS nama_dosen,
                                            pe3_krsmatkul.created_at,
                                            pe3_krsmatkul.updated_at
                                        '))
                                        ->join('pe3_penyelenggaraan AS A','A.id','pe3_krsmatkul.penyelenggaraan_id')
                                        ->leftJoin('pe3_dosen AS B','A.user_id','B.user_id')                                        
                                        ->leftJoin('pe3_kelas_mhs_peserta AS C','pe3_krsmatkul.id','C.krsmatkul_id')                                        
                                        ->leftJoin('pe3_kelas_mhs_penyelenggaraan AS D','D.kelas_mhs_id','C.kelas_mhs_id')                                        
                                        ->leftJoin('pe3_penyelenggaraan_dosen AS E','E.id','D.penyelenggaraan_dosen_id')                                        
                                        ->leftJoin('pe3_dosen AS F','F.user_id','E.user_id')                                        
                                        ->where('krs_id',$krs->id)
                                        ->orderBy('semester','asc')
                                        ->orderBy('kmatkul','asc')
                                        ->get();
            
            $daftar_matkul->transform(function ($item,$key) {                 
                if (is_null($item->nama_dosen_kelas) && is_null($item->nama_dosen_penyelenggaraan))
                {
                    $item->nama_dosen='N.A';
                }     
                else
                {
                    $item->nama_dosen=is_null($item->nama_dosen_kelas) ? $item->nama_dosen_penyelenggaraan:$item->nama_dosen_kelas;                
                }
                return $item;
            });
            $config = ConfigurationModel::getCache();
            $headers=[
                'HEADER_1'=>$config['HEADER_1'],
                'HEADER_2'=>$config['HEADER_2'],
                'HEADER_3'=>$config['HEADER_3'],
                'HEADER_4'=>$config['HEADER_4'],
                'HEADER_LOGO'=>\App\Helpers\Helper::public_path("images/logo.png")
            ];
            $pdf = \Meneses\LaravelMpdf\Facades\LaravelMpdf::loadView('report.ReportKRS',
                                                                    [
                                                                        'headers'=>$headers,
                                                                        'data_krs'=>$krs,
                                                                        'daftar_matkul'=>$daftar_matkul,                                                                        
                                                                        'jumlah_sks'=>$daftar_matkul->sum('sks'),
                                                                    ],
                                                                    [],
                                                                    [
                                                                        'title' => 'KRS',
                                                                    ]);

            $file_pdf=\App\Helpers\Helper::public_path("exported/pdf/krs_".$krs->id.'.pdf');
            $pdf->save($file_pdf);

            $pdf_file="storage/exported/pdf/krs_".$krs->id.".pdf";

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'krs'=>$krs,
                                    'pdf_file'=>$pdf_file                                    
                                ],200);
        }
    }
}
