<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\TAModel;
use App\Models\System\ConfigurationModel;

class TahunAkademikController extends Controller {
    /**
     * daftar tahun akademik
     */
    public function index(Request $request)
    {
        $ta=TAModel::all();

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
                                    'pid'=>'update',                
                                    'message'=>["Tahun Akademik ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $awal_semester = $ta->awal_semester;
            $daftar_bulan=[];
            for($i=$awal_semester;$i<= 12;$i++)
            {
                $daftar_bulan[]=[
                                    'value'=>$i,
                                    'text'=>\App\Helpers\Helper::getNamaBulan($i)
                                ];
            }
            for($i=1;$i<$awal_semester;$i++)
            {
                $daftar_bulan[]=[
                                    'value'=>$i,
                                    'text'=>\App\Helpers\Helper::getNamaBulan($i)
                                ];
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
        ];

        $this->validate($request, $rule);

        $ta=TAModel::create([
            'tahun'=>$request->input('tahun'),
            'tahun_akademik'=>strtoupper($request->input('tahun_akademik')),
        ]);

        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $ta,
                                        'object_id'=>$ta->tahun,
                                        'user_id' => $this->guard()->user()->id,
                                        'message' => 'Menambah tahun akademik baru berhasil'
                                    ]);

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

                                    ]);

            $ta->tahun = $request->input('tahun');
            $ta->tahun_akademik = $request->input('tahun_akademik');

            $ta->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $ta,
                                                        'object_id'=>$ta->tahun,
                                                        'user_id' => $this->guard()->user()->tahun,
                                                        'message' => 'Mengubah data tahun akademik ('.$ta->tahun_akademik.') berhasil'
                                                    ]);

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
