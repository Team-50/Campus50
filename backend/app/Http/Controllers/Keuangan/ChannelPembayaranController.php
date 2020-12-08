<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Keuangan\ChannelPembayaranModel;

class ChannelPembayaranController extends Controller {  
    /**
     * daftar channel pembayaran
     */
    public function index(Request $request)
    {
        $channel=ChannelPembayaranModel::All();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'channel'=>$channel,                                                                                                                                   
                                    'message'=>'Fetch data channel pembayaran berhasil.'
                                ],200);     
    }    
}