<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\System\ConfigurationModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

use App\Helpers\Helper;
use App\Mail\MahasiswaBaruRegistered;
use App\Mail\VerifyEmailAddress;

use Ramsey\Uuid\Uuid;

class PMBController extends Controller {         
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang baru mendaftar di halaman pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SPMB-PMB_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');

        $data = User::where('default_role','mahasiswabaru')
                    ->select(\DB::raw('
                                    users.id,
                                    users.username,
                                    users.name,
                                    users.email,
                                    users.nomor_hp,
                                    users.active,
                                    users.foto,
                                    pe3_formulir_pendaftaran.kjur1 AS prodi_id,
                                    pe3_formulir_pendaftaran.ta,
                                    users.created_at,
                                    users.updated_at'
                                ))
                    ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','users.id')
                    ->where('users.ta',$ta)
                    ->where('kjur1',$prodi_id)
                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'pmb'=>$data,
                                'message'=>'Fetch data calon mahasiswa baru berhasil diperoleh'
                            ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);;  
    }    
    /**
     * digunakan untuk mendapatkan calon mahasiswa baru yang telah mengisi formulir pendaftaran
     *
     * @return \Illuminate\Http\Response
     */
    public function formulirpendaftaran(Request $request)
    {   
        $this->hasAnyPermission(['SPMB-PMB-FORMULIR-PENDAFTARAN_BROWSE','SPMB-PMB-LAPORAN-PRODI_BROWSE']);

        $this->validate($request, [           
            'TA'=>'required',
            'prodi_id'=>'required'
        ]);
        
        $ta=$request->input('TA');
        $prodi_id=$request->input('prodi_id');

        $data = FormulirPendaftaranModel::select(\DB::raw('
                                            users.id,
                                            users.name,
                                            pe3_formulir_pendaftaran.jk,                                            
                                            users.nomor_hp,
                                            pe3_kelas.nkelas,
                                            users.active,
                                            users.foto,
                                            users.created_at,
                                            users.updated_at
                                        '))
                                        ->join('users','pe3_formulir_pendaftaran.user_id','users.id')                    
                                        ->join('pe3_kelas','pe3_formulir_pendaftaran.idkelas','pe3_kelas.idkelas')                    
                                        ->where('users.ta',$ta)
                                        ->where('kjur1',$prodi_id)            
                                        ->whereNotNull('pe3_formulir_pendaftaran.idkelas')   
                                        ->where('users.active',1)    
                                        ->orderBy('users.name','ASC') 
                                        ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'pmb'=>$data,
                                'message'=>'Fetch data calon mahasiswa baru berhasil diperoleh'
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
        $this->validate($request, [
            'name'=>'required',            
            'email'=>'required|string|email|unique:users',
            'nomor_hp'=>'required|unique:users',            
            'prodi_id'=>'required|numeric|exists:pe3_prodi,id',            
            'username'=>'required|string|unique:users',
            'password'=>'required',
            'captcha_response'=>[
                                'required',
                                function ($attribute, $value, $fail) 
                                {
                                    $client = new Client ();
                                    $response = $client->post(
                                        'https://www.google.com/recaptcha/api/siteverify',
                                        ['form_params'=>
                                            [
                                                'secret'=>ConfigurationModel::getCache('CAPTCHA_PRIVATE_KEY'),
                                                'response'=>$value
                                            ]
                                        ]);    
                                    $body = json_decode((string)$response->getBody());
                                    if (!$body->success)
                                    {
                                        $fail('Token Google Captcha, salah !!!.');
                                    }
                                }
                            ]
        ]);
        $user = \DB::transaction(function () use ($request){
            $now = \Carbon\Carbon::now()->toDateTimeString();                   
            $code=mt_rand(1000,9999);
            $ta=ConfigurationModel::getCache('DEFAULT_TAHUN_PENDAFTARAN');
            $user=User::create([
                'id'=>Uuid::uuid4()->toString(),
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'username'=> $request->input('username'),
                'password'=>Hash::make($request->input('password')),
                'nomor_hp'=>$request->input('nomor_hp'),
                'ta'=>$ta,
                'email_verified_at'=>'',
                'theme'=>'default',  
                'foto'=> 'storage/images/users/no_photo.png',
                'code'=>$code,          
                'active'=>0,          
                'default_role'=>'mahasiswabaru',          
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);            
            $role='mahasiswabaru';   
            $user->assignRole($role);
            $permission=Role::findByName('mahasiswabaru')->permissions;
            $user->givePermissionTo($permission->pluck('name'));             
            
            FormulirPendaftaranModel::create([
                'user_id'=>$user->id,
                'nama_mhs'=>$request->input('name'),                
                'telp_hp'=>$request->input('nomor_hp'),
                'kjur1'=>$request->input('prodi_id'),
                'ta'=>$ta,
            ]);

            return $user;
        });
        $config_kirim_email = ConfigurationModel::getCache('EMAIL_MHS_ISVALID');        
        if (!is_null($user) && $config_kirim_email==1)
        {
            $code='';
            app()->mailer->to($request->input('email'))->send(new VerifyEmailAddress($user->code));            
        }
        else
        {
            $code=$user->code;
        }       

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'email'=>$user->email,                              
                                    'code'=>$code,                                                                    
                                    'message'=>'Data Mahasiswa baru berhasil disimpan.'
                                ],200); 

    }      
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storependaftar(Request $request)
    {
        $this->hasPermissionTo('SPMB-PMB_STORE');

        $this->validate($request, [
            'name'=>'required',            
            'email'=>'required|string|email|unique:users',
            'nomor_hp'=>'required|unique:users',            
            'prodi_id'=>'required|numeric|exists:pe3_prodi,id',
            'tahun_pendaftaran'=>'required|numeric',            
            'username'=>'required|string|unique:users',
            'password'=>'required',                        
        ]);
        $user = \DB::transaction(function () use ($request){
            $now = \Carbon\Carbon::now()->toDateTimeString();                   
            $code=mt_rand(1000,9999);
            $ta=$request->input('tahun_pendaftaran');
            $user=User::create([
                'id'=>Uuid::uuid4()->toString(),
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'username'=> $request->input('username'),
                'password'=>Hash::make($request->input('password')),
                'nomor_hp'=>$request->input('nomor_hp'),
                'ta'=>$ta,
                'email_verified_at'=>'',
                'theme'=>'default',  
                'code'=>$code,          
                'active'=>1,         
                'default_role'=>'mahasiswabaru',
                'foto'=>'storage/images/users/no_photo.png', 
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);            
            $role='mahasiswabaru';   
            $user->assignRole($role);
            $permission=Role::findByName('mahasiswabaru')->permissions;
            $user->givePermissionTo($permission->pluck('name'));             
            
            FormulirPendaftaranModel::create([
                'user_id'=>$user->id,
                'nama_mhs'=>$request->input('name'),                
                'telp_hp'=>$request->input('nomor_hp'),
                'kjur1'=>$request->input('prodi_id'),
                'ta'=>$ta,
            ]);

            return $user;
        });
        $config_kirim_email = ConfigurationModel::getCache('EMAIL_MHS_ISVALID');        
        if (!is_null($user) && $config_kirim_email==1)
        {
            $code='';            
            app()->mailer->to($request->input('email'))->send(new VerifyEmailAddress($user->code));            
        }       
        else
        {
            $code=$user->code;
        }
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'pendaftar'=>$user,
                                    'code'=>$code,                                                                                                    
                                    'message'=>'Data Mahasiswa baru berhasil disimpan.'
                                ],200); 

    }      
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatependaftar(Request $request,$id)
    {
        $this->hasPermissionTo('SPMB-PMB_UPDATE');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [
                'username'=>[
                    'required',
                    'unique:users,username,'.$user->id
                ],              
                'email'=>'required|string|email|unique:users,email,'.$user->id,
                'nomor_hp'=>'required|string|unique:users,nomor_hp,'.$user->id,
                'prodi_id'=>'required|numeric|exists:pe3_prodi,id',
                'tahun_pendaftaran'=>'required|numeric'            
            ]);
            
            $user = \DB::transaction(function () use ($request,$user){
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->nomor_hp = $request->input('nomor_hp');
                $user->username = $request->input('username');        
                if (!empty(trim($request->input('password')))) {
                    $user->password = Hash::make($request->input('password'));
                }
                $user->ta=$request->input('tahun_pendaftaran');
                $user->save();

                $formulir=FormulirPendaftaranModel::find($user->id);
                $formulir->nama_mhs=$request->input('name');
                $formulir->telp_hp=$request->input('nomor_hp');
                if (is_null(RegisterMahasiswaModel::find($user->id)))
                {
                    $formulir->kjur1=$request->input('prodi_id');
                    $formulir->ta=$request->input('tahun_pendaftaran');
                }
                $formulir->save();

                $nilai_ujian=\App\Models\SPMB\NilaiUjianModel::find($formulir->user_id);
                if (!is_null($nilai_ujian))
                {
                    $nilai_ujian->kjur=$formulir->kjur1;
                    $nilai_ujian->save();
                }                
                
                return $user;
            });
        }

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'pendaftar'=>$user,                                                                                                  
                                    'message'=>'Data Mahasiswa baru berhasil diubah.'
                                ],200); 

    }      
    /**
     * Detail formulir pendaftaran
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $formulir=FormulirPendaftaranModel::select(\DB::raw('
                                                                users.id,
                                                                user_id,
                                                                nama_mhs,
                                                                tempat_lahir,
                                                                tanggal_lahir,
                                                                jk,
                                                                nomor_hp,
                                                                email,
                                                                nama_ibu_kandung,
                                                                address1_desa_id,
                                                                address1_kelurahan,
                                                                address1_kecamatan_id,
                                                                address1_kecamatan,
                                                                address1_kabupaten_id,
                                                                address1_kabupaten,                                                                
                                                                address1_provinsi_id,
                                                                address1_provinsi,                                                                
                                                                alamat_rumah,
                                                                pe3_prodi.kode_fakultas,
                                                                kjur1,
                                                                idkelas,
                                                                users.ta,
                                                                idsmt
                                                            '))
                                            ->join('users','users.id','pe3_formulir_pendaftaran.user_id')
                                            ->leftJoin('pe3_prodi','pe3_prodi.id','pe3_formulir_pendaftaran.kjur1')
                                            ->find($id);
        if (is_null($formulir))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $transaksi_detail=TransaksiDetailModel::join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                                                    ->where('pe3_transaksi_detail.user_id',$formulir->user_id)
                                                    ->where('pe3_transaksi.status',0)
                                                    ->orWhere('pe3_transaksi.status',1)
                                                    ->where('kombi_id',101)
                                                    ->first(); 
            $no_transaksi='N.A';
            if (!is_null($transaksi_detail))
            {
                $no_transaksi=$transaksi_detail->no_transaksi;
            } 
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                
                                        'formulir'=>$formulir,                                        
                                        'no_transaksi'=>$no_transaksi,
                                        'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
                                    ],200);        
        }

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function konfirmasi(Request $request)
    {
        $this->validate($request, [            
            'email'=>'required|string|email',
            'code'=>'required|numeric',                        
        ]);
        $now = \Carbon\Carbon::now()->toDateTimeString();       
        $email= $request->input('email');
        $code= $request->input('code');  
        
        $user = \DB::table('users')->where('email',$email)->where('code',$code)->get();        
        if ($user->count()>0)
        {
            $user=User::find($user[0]->id);
            $user->code=0;
            $user->active=1;
            $user->save();   
            
            $config_kirim_email = ConfigurationModel::getCache('EMAIL_MHS_ISVALID');        
            if (!is_null($user) && $config_kirim_email==1)
            {
                app()->mailer->to($email)->send(new MahasiswaBaruRegistered($user));
            }

            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',                                                                                                                                        
                                        'message'=>'Email Mahasiswa berhasil diverifikasi.'
                                    ],200);
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',                                                                                                                                        
                                        'message'=>['Email Registrasi Mahasiswa gagal diverifikasi.']
                                    ],422);
        }

    }   
    /**
     * update formulir pendaftaran
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $formulir=FormulirPendaftaranModel::find($id);

        if (is_null($formulir))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
           
            $this->validate($request, [
                'nama_mhs'=>'required',            
                'tempat_lahir'=>'required',            
                'tanggal_lahir'=>'required',            
                'jk'=>'required',            
                'nomor_hp'=>'required|unique:users,nomor_hp,'.$formulir->user_id,
                'email'=>'required|string|email|unique:users,email,'.$formulir->user_id,
                'nama_ibu_kandung'=>'required',

                'address1_provinsi_id'=>'required',
                'address1_provinsi'=>'required',
                'address1_kabupaten_id'=>'required',
                'address1_kabupaten'=>'required',
                'address1_kecamatan_id'=>'required',
                'address1_kecamatan'=>'required',
                'address1_desa_id'=>'required',
                'address1_kelurahan'=>'required',
                'alamat_rumah'=>'required',
                
                'kjur1'=>'required',
                'idkelas'=>'required',            
            ]);

            $data_mhs = \DB::transaction(function () use ($request,$formulir){            
                $formulir->nama_mhs=$request->input('nama_mhs');           
                $formulir->tempat_lahir=$request->input('tempat_lahir');           
                $formulir->tanggal_lahir=$request->input('tanggal_lahir');           
                $formulir->jk=$request->input('jk');           
                $formulir->telp_hp=$request->input('nomor_hp');           
                  
                $formulir->nama_ibu_kandung=$request->input('nama_ibu_kandung');    
                $formulir->address1_provinsi_id=$request->input('address1_provinsi_id');
                $formulir->address1_provinsi=$request->input('address1_provinsi');
                $formulir->address1_kabupaten_id=$request->input('address1_kabupaten_id');
                $formulir->address1_kabupaten=$request->input('address1_kabupaten');
                $formulir->address1_kecamatan_id=$request->input('address1_kecamatan_id');
                $formulir->address1_kecamatan=$request->input('address1_kecamatan');
                $formulir->address1_desa_id=$request->input('address1_desa_id');
                $formulir->address1_kelurahan=$request->input('address1_kelurahan');
                $formulir->alamat_rumah=$request->input('alamat_rumah');    
                $formulir->kjur1=$request->input('kjur1');    
                $formulir->idkelas=$request->input('idkelas');  

                $formulir->save();

                $user=$formulir->User;
                $user->name = $request->input('nama_mhs');
                $user->email = $request->input('email');
                $user->nomor_hp = $request->input('nomor_hp');
                $user->save();    

                $role='mahasiswabaru';   
                $user->assignRole($role);
                $permission=Role::findByName('mahasiswabaru')->permissions;
                $user->givePermissionTo($permission->pluck('name'));             
                
                //buat transaksi keuangan pmb
                $no_transaksi='N.A';
                $transaksi_detail=TransaksiDetailModel::join('pe3_transaksi','pe3_transaksi.id','pe3_transaksi_detail.transaksi_id')
                                                        ->where('pe3_transaksi_detail.user_id',$formulir->user_id)
                                                        ->where('pe3_transaksi.status',0)
                                                        ->orWhere('pe3_transaksi.status',1)
                                                        ->where('pe3_transaksi_detail.kombi_id',101)
                                                        ->first();                
                if (is_null($transaksi_detail))
                {                  
                    $kombi=\App\Models\Keuangan\BiayaKomponenPeriodeModel::where('kombi_id',101)
                                                                        ->where('kjur',$formulir->kjur1)
                                                                        ->where('idkelas',$formulir->idkelas)
                                                                        ->where('tahun',$formulir->ta)
                                                                        ->first();
                    if (!is_null($kombi))
                    {
                        $no_transaksi='101'.date('YmdHms');
                        $transaksi=TransaksiModel::create([
                            'id'=>Uuid::uuid4()->toString(),
                            'user_id'=>$formulir->user_id,
                            'no_transaksi'=>$no_transaksi,
                            'no_faktur'=>'',
                            'kjur'=>$formulir->kjur1,
                            'ta'=>$formulir->ta,
                            'idsmt'=>$formulir->idsmt,
                            'idkelas'=>$formulir->idkelas,
                            'no_formulir'=>$formulir->no_formulir,
                            'nim'=>$formulir->nim,
                            'commited'=>0,
                            'total'=>0,
                            'tanggal'=>date('Y-m-d'),
                        ]);  
                        
                        $transaksi_detail=TransaksiDetailModel::create([
                            'id'=>Uuid::uuid4()->toString(),
                            'user_id'=>$formulir->user_id,
                            'transaksi_id'=>$transaksi->id,
                            'no_transaksi'=>$transaksi->no_transaksi,
                            'kombi_id'=>$kombi->kombi_id,
                            'nama_kombi'=>$kombi->nama_kombi,
                            'biaya'=>$kombi->biaya,
                            'jumlah'=>1,
                            'sub_total'=>$kombi->biaya    
                        ]);
                        $transaksi->total=$kombi->biaya;
                        $transaksi->desc=$kombi->nama_kombi;
                        $transaksi->save();
                    }                    
                }
                else
                {
                    $no_transaksi=$transaksi_detail->no_transaksi;
                }
                $formulir=FormulirPendaftaranModel::find($formulir->user_id);
                return [
                    'formulir'=>$formulir,
                    'no_transaksi'=>$no_transaksi
                ];
            });
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'store',
                                        'formulir'=>$data_mhs['formulir'],                                                                                                  
                                        'no_transaksi'=>$data_mhs['no_transaksi'],                                                                                                  
                                        'message'=>'Formulir Pendaftaran Mahasiswa baru berhasil diubah.'
                                    ],200); 
        }
    }           
    /**
     * Menghapus calon mahasiwa baru
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('SPMB-PMB_DESTROY');

        $user = User::where('isdeleted','1')
                    ->find($id); 
        
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Calon Mahasiswa Baru dengan ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $name=$user->name;
            $user->delete();

            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $this->guard()->user(), 
                                                                'object_id' => $this->guard()->user()->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus Mahasiswa Baru ('.$name.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"Mahasiswa Baru ($name) berhasil dihapus"
                                    ],200);         
        }
                  
    }  
    /**
     * digunakan untuk mengirimkan ulang emai konfirmasi pendaftaran
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'id'=>[
                'required',
                "exists:users,id"
            ],                                         
        ]);
        $user = User::find($request->input('id'));
        $name=$user->name;
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'resendemail',                
                                'message'=>"Kirim ulang data dan konfirmasi PMB ($name) berhasil dikirim"
                            ],200);         
    } 
}