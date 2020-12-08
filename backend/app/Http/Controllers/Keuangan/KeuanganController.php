<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

class KeuanganController extends Controller {  
    /**
     * daftar channel pembayaran
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-RINGKASAN_BROWSE');
        
        $this->validate($request, [           
            'TA'=>'required',
        ]);
        $ta=$request->input('TA');
        
        if ($this->hasRole('mahasiswa') )
        {
            return $this->fetchKeuanganMHS($ta);
        }
        else
        {        
            return $this->fetchKeuanganAdmin($ta);
        }
    }   
    
    private function fetchKeuanganAdmin ($ta)
    {
        //paid
        $kombi_ganjil_paid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',1)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_paid=$kombi_ganjil_paid->sum('jumlah');

        $kombi_genap_paid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',1)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_paid=$kombi_genap_paid->sum('jumlah');

        //unpaid
        $kombi_ganjil_unpaid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',0)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_unpaid=$kombi_ganjil_unpaid->sum('jumlah');

        $kombi_genap_unpaid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',0)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_unpaid=$kombi_genap_unpaid->sum('jumlah');

        //cancelled
        $kombi_ganjil_cancelled=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',2)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_cancelled=$kombi_ganjil_cancelled->sum('jumlah');

        $kombi_genap_cancelled=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',2)                            
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_cancelled=$kombi_genap_cancelled->sum('jumlah');
        
        //perhitungan total
        $total_transaction_paid=$total_kombi_ganjil_paid+$total_kombi_genap_paid;
        $total_transaction_unpaid=$total_kombi_ganjil_unpaid+$total_kombi_genap_unpaid;
        $total_transaction_cancelled=$total_kombi_ganjil_cancelled+$total_kombi_genap_cancelled;

        $total_transaction=$total_transaction_paid+$total_transaction_unpaid+$total_transaction_cancelled;

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'total_transaction'=>$total_transaction,
                                    'total_transaction_paid'=>$total_transaction_paid,
                                    'total_transaction_unpaid'=>$total_transaction_unpaid,
                                    'total_transaction_cancelled'=>$total_transaction_cancelled,

                                    'total_kombi_ganjil_unpaid'=>$total_kombi_ganjil_unpaid,
                                    'total_kombi_genap_unpaid'=>$total_kombi_genap_unpaid,

                                    'total_kombi_ganjil_paid'=>$total_kombi_ganjil_paid,
                                    'total_kombi_genap_paid'=>$total_kombi_genap_paid,

                                    'total_kombi_ganjil_cancelled'=>$total_kombi_ganjil_cancelled,
                                    'total_kombi_genap_cancelled'=>$total_kombi_genap_cancelled,

                                    'kombi_ganjil_unpaid'=>$kombi_ganjil_unpaid,
                                    'kombi_genap_unpaid'=>$kombi_genap_unpaid,
                                    'kombi_ganjil_paid'=>$kombi_ganjil_paid,
                                    'kombi_genap_paid'=>$kombi_genap_paid,
                                    'kombi_ganjil_cancelled'=>$kombi_ganjil_cancelled,
                                    'kombi_genap_cancelled'=>$kombi_genap_cancelled,
                                    'message'=>'Fetch data rangkuman keuangan semester ganjil dan genap berhasil.'
                                ],200);     
    }
    private function fetchKeuanganMHS($ta)
    {
        //paid
        $kombi_ganjil_paid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',1)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_paid=$kombi_ganjil_paid->sum('jumlah');

        $kombi_genap_paid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',1)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_paid=$kombi_genap_paid->sum('jumlah');

        //unpaid
        $kombi_ganjil_unpaid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',0)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_unpaid=$kombi_ganjil_unpaid->sum('jumlah');

        $kombi_genap_unpaid=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',0)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_unpaid=$kombi_genap_unpaid->sum('jumlah');

        //cancelled
        $kombi_ganjil_cancelled=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',1)
                            ->where('status',2)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_ganjil_cancelled=$kombi_ganjil_cancelled->sum('jumlah');

        $kombi_genap_cancelled=\DB::table('pe3_transaksi_detail')
                            ->select(\DB::raw('kombi_id,nama_kombi,sum(sub_total) AS jumlah'))
                            ->join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                            ->where('ta',$ta)
                            ->where('idsmt',2)
                            ->where('status',2)
                            ->where('pe3_transaksi.user_id',$this->getUserid())
                            ->groupByRaw('kombi_id,nama_kombi')
                            ->orderBy('kombi_id','ASC')
                            ->get();
        $total_kombi_genap_cancelled=$kombi_genap_cancelled->sum('jumlah');
        
        //perhitungan total
        $total_transaction_paid=$total_kombi_ganjil_paid+$total_kombi_genap_paid;
        $total_transaction_unpaid=$total_kombi_ganjil_unpaid+$total_kombi_genap_unpaid;
        $total_transaction_cancelled=$total_kombi_ganjil_cancelled+$total_kombi_genap_cancelled;

        $total_transaction=$total_transaction_paid+$total_transaction_unpaid+$total_transaction_cancelled;

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'total_transaction'=>$total_transaction,
                                    'total_transaction_paid'=>$total_transaction_paid,
                                    'total_transaction_unpaid'=>$total_transaction_unpaid,
                                    'total_transaction_cancelled'=>$total_transaction_cancelled,

                                    'total_kombi_ganjil_unpaid'=>$total_kombi_ganjil_unpaid,
                                    'total_kombi_genap_unpaid'=>$total_kombi_genap_unpaid,

                                    'total_kombi_ganjil_paid'=>$total_kombi_ganjil_paid,
                                    'total_kombi_genap_paid'=>$total_kombi_genap_paid,

                                    'total_kombi_ganjil_cancelled'=>$total_kombi_ganjil_cancelled,
                                    'total_kombi_genap_cancelled'=>$total_kombi_genap_cancelled,

                                    'kombi_ganjil_unpaid'=>$kombi_ganjil_unpaid,
                                    'kombi_genap_unpaid'=>$kombi_genap_unpaid,
                                    'kombi_ganjil_paid'=>$kombi_ganjil_paid,
                                    'kombi_genap_paid'=>$kombi_genap_paid,
                                    'kombi_ganjil_cancelled'=>$kombi_ganjil_cancelled,
                                    'kombi_genap_cancelled'=>$kombi_genap_cancelled,
                                    'message'=>'Fetch data rangkuman keuangan semester ganjil dan genap berhasil.'
                                ],200);     
    }
}