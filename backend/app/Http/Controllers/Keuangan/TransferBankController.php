<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Keuangan\TransferBankModel;

use Ramsey\Uuid\Uuid;
class TransferBankController extends Controller {  
    /**
     * daftar bank
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-METODE-TRANSFER-BANK_BROWSE');

        $bank=TransferBankModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'bank'=>$bank,                                                                                                                                   
                                    'message'=>'Fetch data bank berhasil.'
                                ],200);     
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('KEUANGAN-METODE-TRANSFER-BANK_STORE');

        $rule=[            
            'nama_bank'=>'required',                     
            'nama_cabang'=>'required',                     
            'nomor_rekening'=>'required|numeric|unique:pe3_transfer_bank,nomor_rekening',
            'pemilik_rekening'=>'required',                  
        ];
    
        $this->validate($request, $rule);
             
        $bank=TransferBankModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'nama_bank'=>strtoupper($request->input('nama_bank')),                        
            'nama_cabang'=>strtoupper($request->input('nama_cabang')),                        
            'nomor_rekening'=>$request->input('nomor_rekening'),                        
            'pemilik_rekening'=>strtoupper($request->input('pemilik_rekening')),                        
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $bank,
                                        'object_id'=>$bank->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah bank baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'bank'=>$bank,                                    
                                    'message'=>'Data bank berhasil disimpan.'
                                ],200); 

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('KEUANGAN-METODE-TRANSFER-BANK_UPDATE');

        $bank = TransferBankModel::find($id);
        if (is_null($bank))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Kode Kelas ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            
            $this->validate($request, [
                                        'nama_bank'=>'required',                     
                                        'nama_cabang'=>'required',   
                                        'nomor_rekening'=>[
                                                        'required',                                                        
                                                        'numeric',                                                        
                                                        Rule::unique('pe3_transfer_bank')->ignore($bank->nomor_rekening,'nomor_rekening')                                                       
                                                    ],     
                                        'pemilik_rekening'=>'required',   
                                    ]); 
                                   
            $bank->nama_bank = $request->input('nama_bank');
            $bank->nama_cabang = $request->input('nama_cabang');
            $bank->nomor_rekening = $request->input('nomor_rekening');
            $bank->pemilik_rekening = $request->input('pemilik_rekening');
                     
            $bank->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $bank,
                                                        'object_id'=>$bank->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data bank ('.$bank->nama_bank.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'bank'=>$bank,      
                                    'message'=>'Data bank '.$bank->nama_bank.' berhasil diubah.'
                                ],200); 
        }
    }    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('KEUANGAN-METODE-TRANSFER-BANK_DESTROY');

        $bank = TransferBankModel::find($id); 
        
        if (is_null($bank))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode bank ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $bank, 
                                                                'object_id' => $bank->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Kode Kelas ('.$id.') berhasil'
                                                            ]);
            $bank->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Kelas dengan kode ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}