<?php

namespace App\Http\Controllers\Plugins\H2H\BankRiauKepriSyariah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;
use App\Models\Keuangan\KonfirmasiPembayaranModel;

use App\Models\System\ConfigurationModel;

use Exception;

use Ramsey\Uuid\Uuid;

class TransaksiController extends Controller {  
    public function inquiryTagihan(Request $request)
    {
        $kode_billing=$request->input('kode_billing');
        
        $config = ConfigurationModel::getCache();

        $transaksi=TransaksiModel::select(\DB::raw('
                                        pe3_transaksi.no_transaksi AS kode_billing,
                                        pe3_formulir_pendaftaran.no_formulir,
                                        pe3_transaksi.nim,                                        
                                        pe3_formulir_pendaftaran.nama_mhs,
                                        \''.addslashes($config['NAMA_PT']).'\' AS universitas,                                        
                                        \'\' AS fakultas, 
                                        pe3_prodi.nama_prodi AS prodi,
                                        pe3_transaksi.desc AS jenis_pembayaran,
                                        pe3_transaksi.idsmt,
                                        pe3_transaksi.ta,
                                        pe3_transaksi.ta AS periode,
                                        pe3_transaksi.total AS nominal,
                                        0 AS denda,
                                        pe3_transaksi.status,
                                        COALESCE(pe3_konfirmasi_pembayaran.updated_at,"N.A") AS updated_at_konfirm
                                    '))
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi.user_id')
                                    ->leftJoin('pe3_konfirmasi_pembayaran','pe3_konfirmasi_pembayaran.transaksi_id','pe3_transaksi.id')
                                    ->leftJoin('pe3_prodi','pe3_prodi.id','pe3_transaksi.kjur')
                                    ->where('pe3_transaksi.no_transaksi',$kode_billing)
                                    ->first();
        
        if (is_null($transaksi))        {
            return Response()->json(['Result'=>[
                                            'status'=>'14',                                        
                                            'message'=>"request KODE_BILLING ($kode_billing) tidak sesuai"
                                        ]
                                    ],200); 
        }
        else if ($transaksi->status==1)
        {
            return Response()->json(['Result'=>[
                                        'status'=>'88',                                        
                                        'message'=>"Tagihan sudah dibayarkan tanggal: ".$transaksi->updated_at_konfirm
                                    ]
                                ],200); 
        }
        else if ($transaksi->status==2)
            {
                return Response()->json(['Result'=>[
                                            'status'=>'88',                                        
                                            'message'=>"status kode billing ini dibatalkan"
                                        ]
                                    ],200); 
            }
        else
        {     
            $transaksi->periode=\App\Helpers\HelperAkademik::getSemester($transaksi->idsmt).' '.$transaksi->ta;
            return response()->json([
                'Result' => [
                    'status'=>true,
                    'message'=>'Request Data Berhasil',
                    'data'=>$transaksi
                ]
            ], 200); 
        }
    }
    public function payment(Request $request)
    {
        $config = ConfigurationModel::getCache();

        $messages=[
            'kode_billing.required'=>'kode billing diperlukan mohon di isi',
            'kode_billing.exists'=>'kode billing tidak terdaftar didatabase',            
            'noref.required'=>'Nomor referensi dibutuhkan',
            'noref.numeric'=>'Nomor referensi harus numeric',
            
            'amount”.required'=>'amount diperlukan mohon di isi',
            'amount”.numeric'=>'Nilai amount harus numeric',
            
            'tanggal_terima.required'=>'tanggal terima dibutuhkan',
            'tanggal_kirim.required'=>'tanggal kirim dibutuhkan',
        ];
        $validator = Validator::make($request->all(),[
            'kode_billing'=>'required|exists:pe3_transaksi,no_transaksi',
            'noref'=>'required|numeric',
            'amount'=>'required|numeric',
            'tanggal_terima'=>'required',
            'tanggal_kirim'=>'required',
        ],$messages);

        if ($validator->fails())
        {
            return Response()->json(['Result'=>[
                    'status'=>'11',                                        
                    'message'=>"Format request tidak sesuai",
                    'errors'=>$validator->customMessages
                ]
            ],200); 
        }
        else
        {
            $kode_billing=$request->input('kode_billing');
            $transaksi=TransaksiModel::select(\DB::raw('
                                        pe3_transaksi.id,
                                        pe3_transaksi.no_transaksi,
                                        pe3_transaksi.no_faktur,
                                        pe3_formulir_pendaftaran.no_formulir,
                                        pe3_transaksi.nim,                                        
                                        pe3_formulir_pendaftaran.nama_mhs,    
                                        \''.addslashes($config['NAMA_PT']).'\' AS universitas,                                        
                                        \'\' AS fakultas,                                   
                                        pe3_prodi.nama_prodi AS prodi,
                                        pe3_transaksi.kjur,
                                        pe3_transaksi.idsmt,
                                        pe3_transaksi.ta,
                                        pe3_transaksi.idkelas,
                                        pe3_transaksi.total,
                                        0 AS denda,
                                        pe3_transaksi.status,
                                        COALESCE(pe3_konfirmasi_pembayaran.updated_at,"N.A") AS updated_at_konfirm
                                    '))
                                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi.user_id')
                                    ->leftJoin('pe3_konfirmasi_pembayaran','pe3_konfirmasi_pembayaran.transaksi_id','pe3_transaksi.id')
                                    ->leftJoin('pe3_prodi','pe3_prodi.id','pe3_transaksi.kjur')
                                    ->where('pe3_transaksi.no_transaksi',$kode_billing)
                                    ->first();
            
            if ($transaksi->status==1)
            {
                return Response()->json(['Result'=>[
                                                'status'=>'88',                                        
                                                'message'=>"Tagihan sudah dibayarkan tanggal: ".\App\Helpers\Helper::tanggal('d/m/Y H:i:s',$transaksi->updated_at_konfirm),
                                                'noref'=>$transaksi->no_faktur
                                            ]
                                        ],200); 
            }
            else if ($transaksi->status==2)
            {
                return Response()->json(['Result'=>[
                                                'status'=>'88',                                        
                                                'message'=>"status kode billing ini dibatalkan"
                                            ]
                                        ],200); 
            }
            else if ($transaksi->total!=$request->input('amount'))
            {     
                return Response()->json(['Result'=>[
                                            'status'=>'11',                                        
                                            'message'=>'Nilai nominal salah ('.\App\Helpers\Helper::formatUang($request->input('amount')).') karena  tidak sama dengan dengan transaksi '.\App\Helpers\Helper::formatUang($transaksi->total)
                                        ]
                                    ],200); 
            }
            else
            {
                $result = \DB::transaction(function () use ($request,$transaksi){
                    $no_ref=$request->input('noref');
                    $konfirmasi=KonfirmasiPembayaranModel::find($transaksi->id);
                    if (is_null($konfirmasi))
                    {
                        $konfirmasi_insert=KonfirmasiPembayaranModel::create([
                            'transaksi_id'=>$transaksi->id,                
                            'user_id'=>$this->getUserid(),                
                            'no_transaksi'=>$transaksi->no_transaksi,
                            'id_channel'=>4,
                            'total_bayar'=>$transaksi->total,
                            'nomor_rekening_pengirim'=>'HOST TO HOST',
                            'nama_rekening_pengirim'=>'BANK RIAU KEPRI SYARIAH',
                            'nama_bank_pengirim'=>'BANK RIAU KEPRI SYARIAH',
                            'desc'=>'',
                            'tanggal_bayar'=>date ('Y-m-d H:m:s'),                
                            'bukti_bayar'=>"storage/images/buktibayar/paid.png",  
                            'verified'=>true
                        ]);                        
                        $transaksi=$konfirmasi_insert->transaksi;                        
                    }
                    else
                    {
                        $transaksi=$konfirmasi->transaksi;                        
                    }
                    $transaksi->no_faktur=$no_ref;
                    $transaksi->status=1;
                    $transaksi->save();
                    
                    //aksi setelah PAID

                    $detail = TransaksiDetailModel::select(\DB::raw('
                                                        kombi_id
                                                    '))
                                                    ->where('transaksi_id',$transaksi->id)
                                                    ->get();
                    foreach ($detail as $v)
                    {
                        switch ($v->kombi_id)
                        {
                            case 101: //biaya formulir pendaftaran
                                $formulir=\App\Models\SPMB\FormulirPendaftaranModel::find($transaksi->user_id);                        
                                $no_formulir=$formulir->idsmt.mt_rand();
                                $formulir->no_formulir=$no_formulir;
                                $formulir->save();
                            break;
                            case 202:
                                if (!(\App\Models\Akademik\DulangModel::where('tahun',$transaksi->ta)
                                                                        ->where('idsmt',$transaksi->idsmt)
                                                                        ->where('idkelas',$transaksi->idkelas)
                                                                        ->where('nim',$transaksi->nim)
                                                                        ->exists()))
                                {
                                    \App\Models\Akademik\DulangModel::create([
                                                                                'id'=>Uuid::uuid4()->toString(),
                                                                                'user_id'=>$transaksi->user_id,
                                                                                'nim'=>$transaksi->nim,
                                                                                'tahun'=>$transaksi->ta,
                                                                                'idsmt'=>$transaksi->idsmt,
                                                                                'tasmt'=>$transaksi->ta.$transaksi->idsmt,
                                                                                'idkelas'=>$transaksi->idkelas,
                                                                                'status_sebelumnya'=>'A',
                                                                                'k_status'=>'A',
                                                                            ]);
                                }
                            break;
                        }
                    }
                    
                    \App\Models\System\ActivityLog::log($request,[
                                                                    'object' => $transaksi, 
                                                                    'object_id' => $transaksi->id, 
                                                                    'user_id' => $this->getUserid(), 
                                                                    'message' => 'Transaksi berhasil.'
                                                                ]);
                    $result=[
                        'status'=>'00',
                        'kode_billing'=>$transaksi->no_transaksi,
                        'message'=>'Pembayaran Berhasil',
                        'noref'=>$no_ref,
                    ];

                    return $result;
                });
                return response()->json([
                                            'Result' => $result
                                        ], 200);
            }
        
        }        
    }
}
