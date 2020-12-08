<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\ConfigurationModel;

class VariablesController extends Controller {    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SYSTEM-SETTING-VARIABLES_BROWSE'); 
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'setting'=>ConfigurationModel::getCache(),
                                'message'=>'Fetch data seluruh setting variabel'
                            ],200); 


    }       
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->hasPermissionTo('SYSTEM-SETTING-VARIABLES_UPDATE');
        $this->validate($request, [ 
            'pid'=>'required',                               
            'setting'=>[
                'required',                
            ],                     
        ],[
            'name.required'=>'Setting mohon untuk di isi',
        ]);        
        $pid = $request->input('pid');
        $config=json_decode($request->input('setting'),true);
        
        foreach($config as $k=>$v)
        {
            \DB::table('pe3_configuration')->where('config_id',$k)->update(['config_value'=>$v]);
        }

        ConfigurationModel::clear();
        ConfigurationModel::toCache();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',       
                                    'config'=>$config,                                                               
                                    'message'=>"Data setting $pid berhasil diubah."
                                ],200); 
    }
    public function clear(Request $request)
    {
        ConfigurationModel::clear();
        ConfigurationModel::toCache();
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',                                                                    
                                    'message'=>"Cache sudah dikosongkan dan direload ulang setting berhasil."
                                ],200); 
    }
}