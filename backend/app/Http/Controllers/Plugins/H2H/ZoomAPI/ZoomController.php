<?php

namespace App\Http\Controllers\Plugins\H2H\ZoomAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use \Firebase\JWT\JWT;
use GuzzleHttp\Client;

use App\Models\Plugins\H2H\ZoomAPI\ZoomModel;

use Ramsey\Uuid\Uuid;

class ZoomController extends Controller {  
     /**
     * daftar account zoom
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_BROWSE');
        $zoom=ZoomModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'zoom'=>$zoom,                                                                                                                                   
                                    'message'=>'Fetch data account zoom berhasil.'
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
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_STORE');
        $rule=[            
            'email'=>'required|email|unique:plugins_h2h_zoom',
            'api_key'=>'required|string|unique:plugins_h2h_zoom',            
            'api_secret'=>'required|string|unique:plugins_h2h_zoom',            
            'im_token'=>'required|string|unique:plugins_h2h_zoom',                                    
        ];   
        
        $this->validate($request, $rule);
             
        $zoom=ZoomModel::create([
            'id'=>Uuid::uuid4()->toString(),
            'email'=>$request->input('email'),
            'api_key'=>$request->input('api_key'),
            'api_secret'=>$request->input('api_secret'),            
            'im_token'=>$request->input('im_token'),                        
        ]);                      
        
        \App\Models\System\ActivityLog::log($request,[
                                        'object' => $zoom,
                                        'object_id'=>$zoom->id, 
                                        'user_id' => $this->getUserid(), 
                                        'message' => 'Menambah account zoom baru berhasil'
                                    ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'zoom'=>$zoom,                                    
                                    'message'=>'Data account zoom berhasil disimpan.'
                                ],200); 

    }   
    /**
     * digunakan untuk testing
     */
    public function testing(Request $request,$id)
    {
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_UPDATE');

        $zoom = ZoomModel::find($id);
        if (is_null($zoom))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["account zoom ($id) gagal di testing"]
                                ],422); 
        }
        else
        {
            $email = $zoom->email;
            $api_key = $zoom->api_key;
            $api_secret = $zoom->api_secret;

            $payload = array(
                'iss' => $api_key,
                'exp' => (time() + 60)
            );

            try
            {
                $jwt = JWT::encode($payload, $api_secret);                
                $client = new Client ();
                $response = $client->get(
                    'https://api.zoom.us/v2/users/'.$zoom->email,                    
                    [
                        'debug' => FALSE,
                        'headers'=>[
                            'Authorization' => 'Bearer ' . $jwt,                        
                            'content-type' => 'application/json'
                        ]
                    ]
                );    
                $result=json_decode($response->getBody(),true); 
                if (isset($result['id']))
                {
                    $zoom->zoom_id=$result['id'];
                    $zoom->jwt_token=$jwt;
                    $zoom->status=$result['type'];   
                    $desc=$result['status']=='active'?'AKTIF - ':'TIDAK AKTIF - ';
                    switch ($zoom->status)
                    {
                        case 1 :
                            $desc.='Basic';
                        break;
                        case 2 :
                            $desc.='Lincesed';
                        break;
                        case 3 :
                            $desc.='On-Prem';
                        break;
                    }                 
                    $zoom->desc=$desc;
                    $zoom->save();
                }
                else
                {
                    $zoom->zoom_id=null;
                    $zoom->jwt_token=null;
                    $zoom->status=0; 
                    $zoom->desc=null;
                    $zoom->save();
                }
                return Response()->json([
                    'status'=>1,
                    'pid'=>'store',
                    'zoom'=>$zoom,                                    
                    'message'=>'Data account zoom berhasil di synchronize.'
                ],200); 
            }
            catch(\GuzzleHttp\Exception\ClientException $e)
            {
                return Response()->json([
                    'status'=>0,
                    'pid'=>'update',             
                    'request'=> \GuzzleHttp\Psr7\Message::toString($e->getRequest()),  
                    'response'=> \GuzzleHttp\Psr7\Message::toString($e->getResponse()),  
                    'message'=>["account zoom ($id) gagal di sync (check response untuk lebih detail)"]
                ],422);                 
            }
        }
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
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_UPDATE');

        $zoom = ZoomModel::find($id);
        if (is_null($zoom))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["account zoom ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            
            $this->validate($request, [          
                                        
                                        'email'=>[
                                                        'required',
                                                        'string',
                                                        Rule::unique('plugins_h2h_zoom')->ignore($zoom->email,'email')
                                                    ],           
                                        'api_key'=>[
                                                        'required',
                                                        'string',
                                                        Rule::unique('plugins_h2h_zoom')->ignore($zoom->api_key,'api_key')
                                                    ],  
                                        'api_secret'=>[
                                                        'required',
                                                        'string',
                                                        Rule::unique('plugins_h2h_zoom')->ignore($zoom->api_secret,'api_secret')
                                                    ],  
                                        'im_token'=>[
                                                        'required',
                                                        'string',
                                                        Rule::unique('plugins_h2h_zoom')->ignore($zoom->im_token,'im_token')
                                                    ],  
                                                    
                                        
                                        
                                    ]);             
            
            $zoom->email = $request->input('email');
            $zoom->api_key = $request->input('api_key');
            $zoom->api_secret = $request->input('api_secret');            
            $zoom->im_token = $request->input('im_token');            
            
            $zoom->save();

            \App\Models\System\ActivityLog::log($request,[
                                                        'object' => $zoom,
                                                        'object_id'=>$zoom->id, 
                                                        'user_id' => $this->getUserid(), 
                                                        'message' => 'Mengubah data account zoom ('.$zoom->email.') berhasil'
                                                    ]);

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'zoom'=>$zoom,      
                                    'message'=>'Data account zoom '.$zoom->email.' berhasil diubah.'
                                ],200); 
        }
    }   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_SHOW');

        $zoom = ZoomModel::find($id);
        if (is_null($zoom))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["account zoom ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'zoom'=>$zoom,      
                                    'message'=>'Data account zoom '.$zoom->email.' berhasil diperoleh.'
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
        $this->hasPermissionTo('PLUGINS-H2H-ZOOMAPI_DESTROY');

        $zoom = ZoomModel::find($id); 
        
        if (is_null($zoom))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Kode account zoom ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $zoom, 
                                                                'object_id' => $zoom->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus account zoom ('.$id.') berhasil'
                                                            ]);
            $zoom->delete();
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Account zoom dengan id ($id) berhasil dihapus"
                                    ],200);         
        }
                  
    }
}
