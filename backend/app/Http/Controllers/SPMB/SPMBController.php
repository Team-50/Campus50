<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\ProgramStudiModel;

class SPMBController extends Controller 
{  
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
        ]);

        $ta=$request->input('TA');
        
        $daftar_registrasi=[];
        $total_registrasi=0;

        $daftar_isi_formulir=[];
        $total_isi_formulir=0;

        $daftar_lulus=[];
        $total_lulus=0;

        $daftar_tidak_lulus=[];
        $total_tidak_lulus=0;

        $subquery = \DB::table('pe3_formulir_pendaftaran')
                        ->select(\DB::raw('kjur1,COUNT(user_id) AS jumlah'))
                        ->groupBy('kjur1')
                        ->where('ta',$ta);
        
        if ($this->hasRole('superadmin'))
        {
            $daftar_registrasi=ProgramStudiModel::select(\DB::raw('id AS prodi_id,nama_prodi,nama_prodi_alias,nama_jenjang,COALESCE(jumlah,0) AS jumlah'))
                                        ->leftJoinSub($subquery,'pe3_formulir_pendaftaran',function($join){
                                            $join->on('pe3_formulir_pendaftaran.kjur1','=','pe3_prodi.id');
                                        })
                                        ->get();
                                        
            $subquery_isi_formulir=$subquery->whereNotNull('idkelas');
            $daftar_isi_formulir=ProgramStudiModel::select(\DB::raw('id AS prodi_id,nama_prodi,nama_prodi_alias,nama_jenjang,COALESCE(jumlah,0) AS jumlah'))
                                        ->leftJoinSub($subquery_isi_formulir,'pe3_formulir_pendaftaran',function($join){
                                            $join->on('pe3_formulir_pendaftaran.kjur1','=','pe3_prodi.id');
                                        })
                                        ->get();
            
            $subquery_kelulusan=\DB::table('pe3_nilai_ujian_pmb')
                            ->select(\DB::raw('kjur,COUNT(pe3_nilai_ujian_pmb.user_id) AS jumlah'))
                            ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_nilai_ujian_pmb.user_id')
                            ->groupBy('kjur')
                            ->where('ta',$ta);
                            
            $daftar_lulus=ProgramStudiModel::select(\DB::raw('
                            id AS prodi_id,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->joinSub($subquery_kelulusan->where('ket_lulus',1),'pe3_nilai_ujian_pmb',function($join){
                            $join->on('pe3_nilai_ujian_pmb.kjur','=','pe3_prodi.id');
                        })                        
                        ->get();

            $daftar_tidak_lulus=ProgramStudiModel::select(\DB::raw('
                            id AS prodi_id,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->joinSub($subquery_kelulusan->where('ket_lulus',0),'pe3_nilai_ujian_pmb',function($join){
                            $join->on('pe3_nilai_ujian_pmb.kjur','=','pe3_prodi.id');
                        })                        
                        ->get();
                        
            $total_registrasi=$daftar_registrasi->sum('jumlah');
            $total_isi_formulir=$daftar_isi_formulir->sum('jumlah');
            $total_lulus=$daftar_lulus->sum('jumlah');
            $total_tidak_lulus=$daftar_tidak_lulus->sum('jumlah');
        }
        else if ($this->hasRole('pmb'))
        {
            $daftar_registrasi=\DB::table('usersprodi')
                        ->select(\DB::raw('
                            prodi_id,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->leftJoinSub($subquery,'pe3_formulir_pendaftaran',function($join){
                            $join->on('pe3_formulir_pendaftaran.kjur1','=','usersprodi.prodi_id');
                        })
                        ->where('user_id',$this->getUserid())
                        ->get();

            $subquery_isi_formulir=$subquery->whereNotNull('idkelas');
            $daftar_isi_formulir=\DB::table('usersprodi')
                                ->select(\DB::raw('
                                    prodi_id,
                                    nama_prodi,
                                    nama_prodi_alias,
                                    nama_jenjang,
                                    COALESCE(jumlah,0) AS jumlah'
                                ))
                                ->leftJoinSub($subquery_isi_formulir,'pe3_formulir_pendaftaran',function($join){
                                    $join->on('pe3_formulir_pendaftaran.kjur1','=','usersprodi.id');
                                })
                                ->where('user_id',$this->getUserid())
                                ->get();

            $subquery_kelulusan=\DB::table('pe3_nilai_ujian_pmb')
                            ->select(\DB::raw('kjur,COUNT(pe3_nilai_ujian_pmb.user_id) AS jumlah'))
                            ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_nilai_ujian_pmb.user_id')
                            ->groupBy('kjur')
                            ->where('ta',$ta);

            $daftar_lulus=\DB::table('usersprodi')
                            ->select(\DB::raw('
                                prodi_id,
                                nama_prodi,
                                nama_prodi_alias,
                                nama_jenjang,
                                COALESCE(jumlah,0) AS jumlah'
                            ))
                            ->joinSub($subquery_kelulusan->where('ket_lulus',1),'pe3_nilai_ujian_pmb',function($join){
                                $join->on('pe3_nilai_ujian_pmb.kjur','=','usersprodi.id');
                            })
                            ->where('user_id',$this->getUserid())
                            ->get();

            $daftar_tidak_lulus=\DB::table('usersprodi')
                            ->select(\DB::raw('
                                prodi_id,
                                nama_prodi,
                                nama_prodi_alias,
                                nama_jenjang,
                                COALESCE(jumlah,0) AS jumlah'
                            ))
                            ->joinSub($subquery_kelulusan->where('ket_lulus',0),'pe3_nilai_ujian_pmb',function($join){
                                $join->on('pe3_nilai_ujian_pmb.kjur','=','usersprodi.id');
                            })
                            ->where('user_id',$this->getUserid())
                            ->get();

            $total_registrasi=$daftar_registrasi->sum('jumlah');
            $total_isi_formulir=$daftar_isi_formulir->sum('jumlah');
            $total_lulus=$daftar_lulus->sum('jumlah');
            $total_tidak_lulus=$daftar_tidak_lulus->sum('jumlah');
        }

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata', 
                                                                                                                          
                                'daftar_registrasi'=>$daftar_registrasi,
                                'total_registrasi'=>$total_registrasi,       

                                'daftar_isi_formulir'=>$daftar_isi_formulir,
                                'total_isi_formulir'=>$total_isi_formulir,

                                'daftar_lulus'=>$daftar_lulus,
                                'total_lulus'=>$total_lulus,
                                
                                'daftar_tidak_lulus'=>$daftar_tidak_lulus,
                                'total_tidak_lulus'=>$total_tidak_lulus,

                                'message'=>'Fetch data dashboard pmb berhasil diperoleh'
                            ],200);    
        
    }
}