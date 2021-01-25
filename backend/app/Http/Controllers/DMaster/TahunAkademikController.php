<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\TAModel;
use App\Models\System\ConfigurationModel;
use App\Helpers\Helper;

class TahunAkademikController extends Controller {
    /**
     * daftar tahun akademik
     */
    public function index(Request $request)
    {
        $ta=TAModel::select(\DB::raw('
                        tahun, 
                        tahun_akademik, 
                        awal_ganjil,
                        akhir_ganjil,
                        awal_genap,
                        akhir_genap,
                        COALESCE(awal_pendek,\'N.A\') AS awal_pendek,
                        COALESCE(akhir_pendek,\'N.A\') AS akhir_pendek
                    '))
                    ->get();

        $ta->transform(function ($item,$key) {                
                $item->awal_ganjil=Helper::checkformattanggal($item->awal_ganjil)?$item->awal_ganjil:null;
                $item->akhir_ganjil=Helper::checkformattanggal($item->akhir_ganjil)?$item->akhir_ganjil:null;
                $item->awal_genap=Helper::checkformattanggal($item->awal_genap)?$item->awal_genap:null;
                $item->akhir_genap=Helper::checkformattanggal($item->akhir_genap)?$item->akhir_genap:null;
                return $item;
            });
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'ta'=>$ta,
                                    'message'=>'Fetch data tahun akademik berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * digunakan untuk mendapatkan daftar bulan berdasarkan awal semester
     */
    public function daftarbulan(Request $request,$id)
    {
        $ta=TAModel::find($id);         
        if (is_null($ta))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Tahun Akademik ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $daftar_bulan=[];
            if (Helper::checkformattanggal($ta->awal_ganjil))
            {
                $awal_ganjil=Helper::getNomorBulan($ta->awal_ganjil);
                $tahun=$ta->tahun;
                for($i=$awal_ganjil;$i<= 12;$i++)
                {
                    $daftar_bulan[]=[
                                        'value'=>$i,
                                        'text'=>\App\Helpers\Helper::getNamaBulan($i)." $tahun"
                                    ];
                }
                $tahun+=1;
                for($i=1;$i<$awal_ganjil;$i++)
                {
                    $daftar_bulan[]=[
                                        'value'=>$i,
                                        'text'=>\App\Helpers\Helper::getNamaBulan($i)." $tahun"
                                    ];
                }
            }
            else
            {
                return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Awal bulan semester ganjil Tahun Akademik ($id) belum disetting"]
                                ],422);
            }
      
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',
                                        'ta'=>$ta,
                                        'daftar_bulan'=>$daftar_bulan,
                                        'message'=>'Fetch data daftar bulan berhasil.'
                                    ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('DMASTER-TA_STORE');

        $rule=[
            'tahun'=>'required|numeric|unique:pe3_ta,tahun',
            'tahun_akademik'=>'required|string|unique:pe3_ta,tahun_akademik',
            'awal_ganjil'=>'required',
            'akhir_ganjil'=>'required',
            'awal_genap'=>'required',
            'akhir_genap'=>'required',
        ];

        $this->validate($request, $rule);

        $awal_pendek=null;
        $akhir_pendek=null;
        $ta=TAModel::create([
            'tahun'=>$request->input('tahun'),
            'tahun_akademik'=>strtoupper($request->input('tahun_akademik')),         
            'awal_ganjil'=>$request->input('awal_ganjil'),
            'akhir_ganjil'=>$request->input('akhir_ganjil'),
            'awal_genap'=>$request->input('awal_genap'),
            'akhir_genap'=>$request->input('akhir_genap'),
            'awal_pendek'=>$awal_pendek,
            'akhir_pendek'=>$akhir_pendek,
        ]);

        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $ta,
                                        'object_id'=>$ta->tahun,
                                        'user_id' => $this->guard()->user()->id,
                                        'message' => 'Menambah tahun akademik baru berhasil'
                                    ]);
        
        $ta->awal_pendek = 'N.A';
        $ta->akhir_pendek = 'N.A';    
           
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'ta'=>$ta,
                                    'message'=>'Data tahun akademik berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-TA_UPDATE');

        $ta = TAModel::find($id);
        if (is_null($ta))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',
                                    'message'=>["Tahun Akademik ($id) gagal diupdate"]
                                ],422);
        }
        else
        {
            $this->validate($request, [
                                        'tahun'=>[
                                                    'required',
                                                    'numeric',
                                                    Rule::unique('pe3_ta')->ignore($ta->tahun,'tahun')
                                                ],
                                        'tahun_akademik'=>[
                                                        'required',
                                                        'string',
                                                        Rule::unique('pe3_ta')->ignore($ta->tahun_akademik,'tahun_akademik')
                                                    ],

                                        'awal_ganjil'=>'required',
                                        'akhir_ganjil'=>'required',
                                        'awal_genap'=>'required',
                                        'akhir_genap'=>'required',

                                    ]);

            $awal_pendek=null;
            $akhir_pendek=null;
          
            $ta->tahun = $request->input('tahun');
            $ta->tahun_akademik = $request->input('tahun_akademik');
            $ta->awal_ganjil = $request->input('awal_ganjil');
            $ta->akhir_ganjil = $request->input('akhir_ganjil');
            $ta->awal_genap = $request->input('awal_genap');
            $ta->akhir_genap = $request->input('akhir_genap');
            $ta->awal_pendek = $awal_pendek;
            $ta->akhir_pendek = $akhir_pendek;

            $ta->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $ta,
                                                        'object_id'=>$ta->tahun,
                                                        'user_id' => $this->guard()->user()->tahun,
                                                        'message' => 'Mengubah data tahun akademik ('.$ta->tahun_akademik.') berhasil'
                                                    ]);
            
            $ta->awal_pendek = 'N.A';
            $ta->akhir_pendek = 'N.A';
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'ta'=>$ta,
                                    'message'=>'Data tahun akademik '.$ta->tahun_akademik.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-TA_DESTROY');

        $ta = TAModel::find($id);

        if (is_null($ta))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',
                                    'message'=>["Kode tahun akademik ($id) gagal dihapus"]
                                ],422);
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $ta,
                                                            'object_id' => $ta->tahun,
                                                            'user_id' => $this->guard()->user()->id,
                                                            'message' => 'Menghapus Tahun Akademik ('.$id.') berhasil'
                                                        ]);
            $ta->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',
                                        'message'=>"Tahun Akademik dengan kode ($id) berhasil dihapus"
                                    ],200);
        }

    }
}