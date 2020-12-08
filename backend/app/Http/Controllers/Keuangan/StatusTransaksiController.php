<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Keuangan\StatusTransaksiModel;

class StatusTransaksiController extends Controller {  
    /**
     * daftar status transaksi
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-STATUS-TRANSAKSI_BROWSE');

        $status_transaksi=StatusTransaksiModel::All();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'status'=>$status_transaksi,                                                                                                                                   
                                    'message'=>'Fetch data status transaksi berhasil.'
                                ],200);     
    } 
    /**
     * digunakan untuk merubah style komponen
     */
    public function update (Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-STATUS-TRANSAKSI_STORE');
        
        $this->validate($request, [           
            'id_status'=>'required|exists:pe3_status_transaksi,id_status',
            'style'=>'required'
        ]);
        $id_status=$request->input('id_status');
        $style=$request->input('style');
        
        $status=StatusTransaksiModel::find($id_status);
        $status->style=$style;
        $status->save();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',     
                                    'status'=>$status,                                                                                                                                                               
                                    'message'=>'Mengubah status transaksi '.$status->nama_status.' berhasil.'
                                ],200);     
    } 
}