<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DMaster\TAModel;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;
use Illuminate\Validation\Rule;

use Exception;

use Ramsey\Uuid\Uuid;

class TransaksiSPPController extends Controller {  
    /**
     * daftar komponen biaya
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-TRANSAKSI-SPP_BROWSE');
        
        $this->validate($request, [           
            'TA'=>'required',
        ]);
        $ta=$request->input('TA');

        if ($this->hasRole(['mahasiswa','mahasiswabaru']))
        {
            $daftar_transaksi = TransaksiDetailModel::select(\DB::raw('
                                                        pe3_transaksi_detail.id,
                                                        pe3_transaksi_detail.user_id,
                                                        pe3_transaksi_detail.transaksi_id,
                                                        pe3_transaksi_detail.no_transaksi,
                                                        pe3_transaksi_detail.biaya,
                                                        pe3_transaksi_detail.jumlah,
                                                        pe3_transaksi_detail.bulan,
                                                        \'\' AS nama_bulan,
                                                        pe3_transaksi_detail.sub_total,

                                                        pe3_formulir_pendaftaran.nama_mhs,

                                                        pe3_transaksi.no_faktur,
                                                        pe3_transaksi.kjur,
                                                        pe3_transaksi.ta,
                                                        pe3_transaksi.idsmt,
                                                        pe3_transaksi.idkelas,
                                                        pe3_transaksi.no_formulir,                                                        
                                                        COALESCE(pe3_transaksi.nim,\'N.A\') AS nim,
                                                        pe3_transaksi.status,
                                                        pe3_status_transaksi.nama_status,
                                                        pe3_status_transaksi.style,
                                                        pe3_transaksi.total,
                                                        pe3_transaksi.tanggal,     
                                                        pe3_transaksi_detail.created_at,
                                                        pe3_transaksi_detail.updated_at
                                                    '))
                                                    ->join('pe3_transaksi','pe3_transaksi_detail.transaksi_id','pe3_transaksi.id')
                                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi_detail.user_id')
                                                    ->join('pe3_status_transaksi','pe3_transaksi.status','pe3_status_transaksi.id_status')
                                                    ->where('pe3_transaksi.ta',$ta)
                                                    ->where('pe3_transaksi_detail.user_id',$this->getUserid())
                                                    ->where('pe3_transaksi_detail.kombi_id',201)                                                    
                                                    ->orderBy('pe3_transaksi.tanggal','DESC')
                                                    ->get();
        }
        else
        {
            $daftar_transaksi = TransaksiDetailModel::select(\DB::raw('
                                                        pe3_transaksi_detail.id,
                                                        pe3_transaksi_detail.user_id,
                                                        pe3_transaksi_detail.transaksi_id,
                                                        pe3_transaksi_detail.no_transaksi,
                                                        pe3_transaksi_detail.biaya,
                                                        pe3_transaksi_detail.jumlah,
                                                        pe3_transaksi_detail.bulan,
                                                        \'\' AS nama_bulan,
                                                        pe3_transaksi_detail.sub_total,

                                                        pe3_formulir_pendaftaran.nama_mhs,

                                                        pe3_transaksi.no_faktur,
                                                        pe3_transaksi.kjur,
                                                        pe3_transaksi.ta,
                                                        pe3_transaksi.idsmt,
                                                        pe3_transaksi.idkelas,
                                                        pe3_transaksi.no_formulir,
                                                        pe3_transaksi.nim,
                                                        pe3_transaksi.status,
                                                        pe3_status_transaksi.nama_status,
                                                        pe3_status_transaksi.style,
                                                        pe3_transaksi.total,
                                                        pe3_transaksi.tanggal,     
                                                        pe3_transaksi_detail.created_at,
                                                        pe3_transaksi_detail.updated_at
                                                    '))
                                                    ->join('pe3_transaksi','pe3_transaksi_detail.transaksi_id','pe3_transaksi.id')
                                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi_detail.user_id')
                                                    ->join('pe3_status_transaksi','pe3_transaksi.status','pe3_status_transaksi.id_status')
                                                    ->where('pe3_transaksi.ta',$ta)                                                    
                                                    ->where('pe3_transaksi_detail.kombi_id',201)                                                    
                                                    ->orderBy('pe3_transaksi.tanggal','DESC')
                                                    ->get();
        }        
        $daftar_transaksi->transform(function ($item,$key){
            $item->nama_bulan=\App\Helpers\Helper::getNamaBulan($item->bulan);
            return $item;
        });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'transaksi'=>$daftar_transaksi,                                                                                                                                   
                                    'message'=>'Fetch data daftar transaksi berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);    
    }
    public function show (Request $request,$id)
    {
        try 
        {
            if ($this->hasRole(['mahasiswa','mahasiswabaru']))
            {
                $transaksi=TransaksiModel::select(\DB::raw('
                                        pe3_transaksi.*,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_kelas.nkelas,
                                        pe3_status_transaksi.nama_status,
                                        pe3_status_transaksi.style
                                    '))
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi.user_id')
                                    ->join('pe3_status_transaksi','pe3_transaksi.status','pe3_status_transaksi.id_status')
                                    ->join('pe3_kelas','pe3_kelas.idkelas','pe3_transaksi.idkelas')
                                    ->where('pe3_transaksi.user_id',$this->getUserid())
                                    ->find($id);
            }
            else
            {
                $transaksi=TransaksiModel::select(\DB::raw('
                                        pe3_transaksi.*,
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        pe3_kelas.nkelas,
                                        pe3_status_transaksi.nama_status,
                                        pe3_status_transaksi.style
                                    '))
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi.user_id')
                                    ->join('pe3_status_transaksi','pe3_transaksi.status','pe3_status_transaksi.id_status')
                                    ->join('pe3_kelas','pe3_kelas.idkelas','pe3_transaksi.idkelas')
                                    ->find($id);
            }
            if (is_null($transaksi))        
            {
                // throw new Exception ("Fetch data transaksi dengan id ($id) gagal diperoleh.");                
            }
            $biaya_kombi=BiayaKomponenPeriodeModel::where('tahun',$transaksi->ta)
                                                    ->where('idkelas',$transaksi->idkelas)
                                                    ->where('kjur',$transaksi->kjur)
                                                    ->where('kombi_id',201)
                                                    ->value('biaya');
            
            if (!($biaya_kombi > 0))
            {
                // throw new Exception ("Komponen Biaya SPP (201) belum disetting pada TA ".$transaksi->ta);  
            }
            $ta=TAModel::find($transaksi->ta); 
            $awal_semester = $ta->awal_semester;
            $transaksi_detail=[];
            for($i=$awal_semester;$i<= 12;$i++)
            {
                $status=$this->checkPembayaranSPP($i,$transaksi->ta,$transaksi->user_id);
                $transaksi_detail[]=[
                                    'id'=>$i,
                                    'no_bulan'=>$i,
                                    'nama_bulan'=>\App\Helpers\Helper::getNamaBulan($i),
                                    'tahun'=>$transaksi->ta,
                                    'biaya_kombi'=>$biaya_kombi,
                                    'isSelectable'=>$status,
                                    'status'=>$status
                                ];
            }
            for($i=1;$i<$awal_semester;$i++)
            {
                $status=$this->checkPembayaranSPP($i,$transaksi->ta,$transaksi->user_id);
                $transaksi_detail[]=[
                                    'id'=>$i,
                                    'no_bulan'=>$i,
                                    'nama_bulan'=>\App\Helpers\Helper::getNamaBulan($i),
                                    'tahun'=>$transaksi->ta+1,
                                    'biaya_kombi'=>$biaya_kombi,
                                    'isSelectable'=>$status,
                                    'status'=>$status
                                ];
            }            
            $item_selected = TransaksiDetailModel::select(\DB::raw('
                                id,
                                bulan AS no_bulan,
                                \'\' AS nama_bulan,
                                '.$transaksi->ta.' AS tahun,
                                biaya AS biaya_kombi,
                                true AS status,
                                true AS isSelectable
                            '))
                            ->where('transaksi_id',$transaksi->id)
                            ->get();
            $item_selected->transform(function ($item,$key) {                
                $item->nama_bulan=\App\Helpers\Helper::getNamaBulan($item->no_bulan);                
                return $item;
            });
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'transaksi'=>$transaksi,                                                                                                                                   
                                        'transaksi_detail'=>$transaksi_detail,                                                                                                                                   
                                        'item_selected'=>$item_selected,                                                                                                                                   
                                        'message'=>"Fetch data transaksi dengan id ($id) berhasil diperoleh."
                                    ],200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
        }
        catch (Exception $e)
        {
            return Response()->json([
                'status'=>0,
                'pid'=>'fetchdata',                                                                                                                                                  
                'message'=>[$e->getMessage()]
            ],422); 
        }      
    }
    public function newtransaction (Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-TRANSAKSI-SPP_STORE');

        $this->validate($request, [           
            'nim'=>'required|exists:pe3_register_mahasiswa,nim',                 
            'semester_akademik'=>'required',
            'TA'=>'required'
        ]);

        try 
        {
            $nim=$request->input('nim');
            $semester_akademik=$request->input('semester_akademik');
            $ta=$request->input('TA');
            
            $mahasiswa=RegisterMahasiswaModel::select(\DB::raw('
                                    pe3_formulir_pendaftaran.user_id,
                                    pe3_formulir_pendaftaran.no_formulir,
                                    pe3_formulir_pendaftaran.nama_mhs,
                                    pe3_register_mahasiswa.nim,
                                    pe3_register_mahasiswa.tahun,
                                    pe3_register_mahasiswa.idkelas,
                                    pe3_register_mahasiswa.kjur,
                                    pe3_register_mahasiswa.k_status,
                                    pe3_status_mahasiswa.n_status                                    
                                '))                             
                                ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_register_mahasiswa.user_id')   
                                ->join('pe3_status_mahasiswa','pe3_status_mahasiswa.k_status','pe3_register_mahasiswa.k_status')                                
                                ->where('nim',$nim)
                                ->first();

            if (is_null($mahasiswa))
            {                
                throw new Exception ("Mahasiswa dengan NIM ($nim) tidak terdaftar.");  
            }
            if ($mahasiswa->k_status == 'D' || $mahasiswa->k_status == 'K' || $mahasiswa->k_status == 'L')
            {                
                throw new Exception ("Mahasiswa dengan NIM ($nim) telah ".$mahasiswa->n_status.' jadi tidak bisa membuat transaksi baru');  
            }
            
            $tahun=$mahasiswa->tahun;
            $idkelas=$mahasiswa->idkelas;
            $kjur=$mahasiswa->kjur;

            $biaya_kombi=BiayaKomponenPeriodeModel::where('tahun',$tahun)
                                                    ->where('idkelas',$idkelas)
                                                    ->where('kjur',$kjur)
                                                    ->where('kombi_id',201)
                                                    ->value('biaya');
            
            if (!($biaya_kombi > 0))
            {
                throw new Exception ("Komponen Biaya SPP (201) belum disetting pada TA $tahun");  
            }

            //hapus seluruh transaksi yang tidak memiliki detail transaksi
            \DB::table('pe3_transaksi')
                ->where ('user_id',$mahasiswa->user_id)
                ->whereNotIn('id',function ($query) use ($mahasiswa)
                {
                    $query->select(\DB::raw('transaksi_id'))
                        ->from('pe3_transaksi_detail')
                        ->where('user_id',$mahasiswa->user_id);                        
                })
                ->delete();
            $no_transaksi='201'.date('YmdHms');
            $transaksi=TransaksiModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$mahasiswa->user_id,
                'no_transaksi'=>$no_transaksi,
                'no_faktur'=>'',
                'kjur'=>$mahasiswa->kjur,
                'ta'=>$ta,
                'idsmt'=>$semester_akademik,
                'idkelas'=>$mahasiswa->idkelas,
                'no_formulir'=>$mahasiswa->no_formulir,
                'nim'=>$mahasiswa->nim,
                'commited'=>0,
                'total'=>0,
                'tanggal'=>date('Y-m-d'),
            ]);  

            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'store',                   
                                        'transaksi'=>$transaksi,                                                                                                                                   
                                        'message'=>'Membuat Transaksi SPP baru berhasil.'
                                    ],200); 
        }
        catch (Exception $e)
        {
            return Response()->json([
                'status'=>0,
                'pid'=>'store',                                                                                                                                                  
                'message'=>[$e->getMessage()]
            ],422); 
        }  
    }
    public function store(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-TRANSAKSI-SPP_STORE');

        $bulan_selected=json_decode($request->input('bulan_selected'),true);
        $request->merge(['bulan_selected'=>$bulan_selected]);

        if ($this->hasRole(['mahasiswa','mahasiswabaru']))
        {
            $this->validate($request, [      
                'id'=>[
                        'required',
                        Rule::exists('pe3_transaksi')->where(function ($query) {                                    
                            $query->where('user_id',$this->getUserid());
                            $query->where('status',0);
                        })
                    ],
                'bulan_selected.*'=>'required',            
            ]);
        }
        else
        {
            $this->validate($request, [      
                'id'=>[
                    'required',
                    Rule::exists('pe3_transaksi')->where(function ($query) {                                                            
                        $query->where('status',0);
                    })
                ],     
                'bulan_selected.*'=>'required',            
            ]);
        }
        $transaksi = \DB::transaction(function () use ($request){

            $transaksi_id=$request->input('id');
            $transaksi=TransaksiModel::find($transaksi_id);

            $bulan_selected=$request->input('bulan_selected');
        
            $bulan_spp=[];
            $total_spp=0;
            $nama_bulan=' ';
            foreach ($bulan_selected as $v)
            {
                $bulan_spp[]=[
                    'id'=>Uuid::uuid4()->toString(),
                    'user_id'=>$transaksi->user_id,
                    'transaksi_id'=>$transaksi->id,
                    'no_transaksi'=>$transaksi->no_transaksi,
                    'kombi_id'=>201,
                    'nama_kombi'=>'SPP',
                    'biaya'=>$v['biaya_kombi'],                
                    'jumlah'=>1,                
                    'bulan'=>$v['no_bulan'],                
                    'sub_total'=>$v['biaya_kombi'],                
                    'created_at'=>\Carbon\Carbon::now(),
                    'updated_at'=>\Carbon\Carbon::now()
                ];
                $nama_bulan.=$v['no_bulan'].' ';
                $total_spp+=$v['biaya_kombi'];
            }
            \DB::table('pe3_transaksi_detail')
                ->where ('transaksi_id',$transaksi_id)            
                ->delete();
                
            TransaksiDetailModel::insert($bulan_spp);
            $transaksi->total=$total_spp;
            $transaksi->desc="BAYAR SPP BULAN $nama_bulan";
            $transaksi->save();
        });

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',                                                                                                                                                                         
                                    'bulan_selected'=>$bulan_selected,                                                                                                                                                                         
                                    'bulan_spp'=>$bulan_spp,                                                                                                                                                                         
                                    'message'=>(count($bulan_spp)).' Bulan SPP telah berhasil ditambahkan'
                                ],200);  
    }
    public function destroy (Request $request,$id)
    {
        $this->hasPermissionTo('KEUANGAN-TRANSAKSI-SPP_DESTROY');

        if ($this->hasRole(['mahasiswa','mahasiswabaru']))
        {
            $detail = TransaksiDetailModel::where('user_id',$this->getUserid())
                                            ->find($id); 
        }
        else 
        {
            $detail = TransaksiDetailModel::find($id); 
        }
        $transaksi=$detail->transaksi;
        if (is_null($detail))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Transaksi Detail dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else if ($transaksi->status==1 || $transaksi->status==2)
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>["Transaksi dengan ID ($id) sudah tidak bisa dihapus"]
                                ],422); 
        }
        else
        {
            $sub_total=$detail->sub_total;
            $detail->delete();
            $transaksi->total=$transaksi->total-$sub_total;
            $transaksi->save();

            \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $detail, 
                                                            'object_id' => $detail->id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message' => 'Menghapus Transaksi Detail dengan ID ('.$id.') berhasil'
                                                        ]);
    
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',          
                                        'message' => 'Menghapus Transaksi Detail dengan ID ('.$id.') berhasil'
                                    ],200);         
        }
    }
    private function checkPembayaranSPP ($no_bulan,$tahun,$user_id)
    {
        return \DB::table('pe3_transaksi')
                    ->join('pe3_transaksi_detail','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                    ->where('pe3_transaksi.ta',$tahun)
                    ->where('pe3_transaksi.user_id',$user_id)
                    ->where('pe3_transaksi_detail.bulan',$no_bulan)
                    ->exists();
    }
}