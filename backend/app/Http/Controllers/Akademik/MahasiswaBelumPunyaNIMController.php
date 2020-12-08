<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Keuangan\TransaksiDetailModel;

use Ramsey\Uuid\Uuid;

class MahasiswaBelumPunyaNIMController extends Controller 
{
    /**
     * daftar mahasiswa
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-BARU_BROWSE');

        $this->validate($request, [           
            'ta'=>'required',
            'prodi_id'=>'required'
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');

        
        $data = TransaksiDetailModel::select(\DB::raw('
                                        pe3_formulir_pendaftaran.user_id,
                                        pe3_formulir_pendaftaran.no_formulir,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_formulir_pendaftaran.telp_hp,
                                        pe3_formulir_pendaftaran.idkelas                                      
                                    '))
                                    ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi_detail.user_id')
                                    ->leftJoin('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_formulir_pendaftaran.user_id')
                                    ->where('kombi_id',102)
                                    ->where('pe3_transaksi.status',1)
                                    ->where('pe3_formulir_pendaftaran.kjur1',$prodi_id)
                                    ->where('pe3_formulir_pendaftaran.ta',$ta)
                                    ->whereNull('pe3_register_mahasiswa.nim')
                                    ->orderBy('pe3_formulir_pendaftaran.idkelas','ASC')                      
                                    ->orderBy('pe3_formulir_pendaftaran.nama_mhs','ASC')                      
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'mahasiswa'=>$data,                                                                                                                                   
                                    'message'=>'Fetch data calon mahasiswa baru yang belum punya nim berhasil.'
                                ],200);     
    }
    public function store(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-BARU_STORE');

        $this->validate($request, [                       
            'user_id'=>[
                'required',
            ],
            'nim'=>'required|numeric|unique:pe3_register_mahasiswa,nim',
            'nirm'=>'required|numeric|unique:pe3_register_mahasiswa,nirm',
            'dosen_id'=>[
                'required',
                Rule::exists('pe3_dosen','user_id')->where(function($query){
                    return $query->where('is_dw',1);
                })
            ]
        ]);
        
        $mahasiswa = \DB::transaction(function () use ($request){
            $no_bulan=9;
            $user_id=$request->input('user_id');
            $formulir=\App\Models\SPMB\FormulirPendaftaranModel::find($user_id);

            $mahasiswa=RegisterMahasiswaModel::create([
                'user_id'=>$user_id,
                'dosen_id'=>$request->input('dosen_id'),
                'konsentrasi_id'=>null,
                'nim'=>$request->input('nim'),
                'nirm'=>$request->input('nirm'),
                'tahun'=>$formulir->ta,
                'idsmt'=>$formulir->idsmt,
                'kjur'=>$formulir->kjur1,
                'k_status'=>'A',
                'idkelas'=>$formulir->idkelas,
            ]);
            
            $user=$formulir->user;
            $user->username=$request->input('nim');
            $user->default_role='mahasiswa';
            $user->save();

            $user->syncRoles(['mahasiswa']); 
            $permission=Role::findByName('mahasiswa')->permissions;
            $permissions=$permission->pluck('name');
            $user->givePermissionTo($permissions);
            
            $spp=TransaksiDetailModel::select(\DB::raw('pe3_transaksi.status'))  
                                        ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                                        ->where('pe3_transaksi_detail.kombi_id',201)
                                        ->where('pe3_transaksi.ta',$formulir->ta)
                                        ->where('pe3_transaksi.idsmt',$formulir->idsmt)
                                        ->where('pe3_transaksi_detail.bulan',$no_bulan)
                                        ->where('pe3_transaksi.status',1)
                                        ->count();

            if ($spp > 0)
            {
                DulangModel::create([
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$user_id,
                    'nim'=>$mahasiswa->nim,
                    'tahun'=>$formulir->ta,
                    'idsmt'=>$formulir->idsmt,
                    'tasmt'=>$formulir->ta.$formulir->idsmt,
                    'idkelas'=>$formulir->idkelas,
                    'status_sebelumnya'=>'A',
                    'k_status'=>'A',
                ]);
            }
            return $mahasiswa;
        });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',      
                                    'mahasiswa'=>$mahasiswa,                                
                                    'message'=>'Daftar Ulang Mahasiswa telah berhasil.'
                                ],200);     
    }
}

