<?php

namespace App\Http\Controllers\SPMB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\SPMB\SoalPMBModel;
use App\Models\SPMB\JawabanSoalPMBModel;
use App\Helpers\Helper;

use Ramsey\Uuid\Uuid;

class SoalPMBController extends Controller {  
	/**
	 * daftar soal
	 */
	public function index(Request $request)
	{
		$this->hasPermissionTo('SPMB-PMB-SOAL_BROWSE');

		$this->validate($request, [                       
			'tahun_pendaftaran'=>'required',
			'semester_pendaftaran'=>'required'
		]);
		$tahun_pendaftaran=$request->input('tahun_pendaftaran');
		$semester_pendaftaran=$request->input('semester_pendaftaran');

		$soal=SoalPMBModel::where('ta',$tahun_pendaftaran)
							->where('semester',$semester_pendaftaran)
							->get();

		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',  
			'soal'=>$soal,                                                                                                                                   
			'message'=>'Fetch data soal pmb berhasil.'
		], 200);
	}  
	/**
	 * salin soal baru
	 */
	public function salin(Request $request, $id)
	{
		$this->hasPermissionTo('SPMB-PMB-SOAL_STORE');

		$this->validate($request, [           
			'dari_tahun_pendaftaran'=>'required',
		]);
		
		$dari_tahun_pendaftaran=$request->input('dari_tahun_pendaftaran');
		
		\DB::table('pe3_soal')
			->where('ta', $id)            
			->delete();

		$sql = "INSERT INTO pe3_soal(
			id,
			soal,    
			gambar, 
			prodi_id,         
			ta,         
			semester,  
			active,
			soal_id_src,
			created_at,
			updated_at
		)
		SELECT 
			UUID() AS id,
			soal,
			gambar,
			prodi_id,
			$id AS ta,
			1 AS semester,
			active,
			id AS soal_id_src,
			NOW(),
			NOW()
		FROM pe3_soal 
			WHERE ta='$dari_tahun_pendaftaran'
		";	
		\DB::statement($sql);

		$sql = "INSERT INTO pe3_jawaban_soal (
			id,                      
			soal_id,    
			jawaban, 
			status,         
			options,
			created_at,
			updated_at
		)SELECT
			UUID() AS id,
			B.id AS soal_id,
			A.jawaban,
			A.status,
			A.options,
			NOW(),
			NOW()
		FROM pe3_jawaban_soal A
		JOIN pe3_soal B ON A.soal_id=B.soal_id_src
		where 
			B.ta='$id'
		";
		\DB::statement($sql);

		return Response()->json([
				'status'=>1,
				'pid'=>'store',				                                                                                                                                 
				'message'=>'Data soal berhasil disalin.'
			], 200); 
	}
	/**
	 * simpan soal baru
	 */
	public function store(Request $request)
	{
		$this->hasPermissionTo('SPMB-PMB-SOAL_STORE');

		$this->validate($request, [           
			'soal'=>'required',            
			'gambar'=>'image',            
			'jawaban1'=>'required',
			'jawaban2'=>'required',
			'jawaban3'=>'required',
			'jawaban4'=>'required',
			'jawaban_benar'=>'required',
			'tahun_pendaftaran'=>'required',
			'semester_pendaftaran'=>'required',            
		]);
		
		$soal = \DB::transaction(function () use ($request){
			$now = \Carbon\Carbon::now()->toDateTimeString();                               
			$soal=SoalPMBModel::create([
				'id'=>Uuid::uuid4()->toString(),
				'gambar'=>null,
				'soal'=>$request->input('soal'),                
				'ta'=> $request->input('tahun_pendaftaran'),
				'semester'=>$request->input('semester_pendaftaran'),
				'active'=>1,                  
				'created_at'=>$now, 
				'updated_at'=>$now
			]);                        
			$soal_id=$soal->id;
			JawabanSoalPMBModel::create([
				'id'=>Uuid::uuid4()->toString(),
				'soal_id'=>$soal_id,
				'jawaban'=>$request->input('jawaban1'),                
				'options'=>'{}',
				'status'=>$request->input('jawaban_benar')==1?1:0,
				'created_at'=>$now, 
				'updated_at'=>$now
			]);
			JawabanSoalPMBModel::create([
				'id'=>Uuid::uuid4()->toString(),
				'soal_id'=>$soal_id,
				'jawaban'=>$request->input('jawaban2'),                
				'options'=>'{}',
				'status'=>$request->input('jawaban_benar')==2?1:0,
				'created_at'=>$now, 
				'updated_at'=>$now
			]);
			JawabanSoalPMBModel::create([
				'id'=>Uuid::uuid4()->toString(),
				'soal_id'=>$soal_id,
				'jawaban'=>$request->input('jawaban3'),                
				'options'=>'{}',
				'status'=>$request->input('jawaban_benar')==3?1:0,
				'created_at'=>$now, 
				'updated_at'=>$now
			]);
			JawabanSoalPMBModel::create([
				'id'=>Uuid::uuid4()->toString(),
				'soal_id'=>$soal_id,
				'jawaban'=>$request->input('jawaban4'),                
				'options'=>'{}',
				'status'=>$request->input('jawaban_benar')==4?1:0,
				'created_at'=>$now, 
				'updated_at'=>$now
			]);

			return $soal;
		});

		/*
		if ($request->filled('gambar'))
		{
			if ($request->file('gambar')->isValid())
			{
				$gambar=$request->file('gambar');
				$mime_type=$gambar->getMimeType();
				if ($mime_type=='image/png' || $mime_type=='image/jpeg')
				{
					$folder=Helper::public_path('images/banksoal/');
					$file_name=uniqid('img').".".$gambar->getClientOriginalExtension();
					$gambar->move($folder,$file_name);    


					$soal->gambar="storage/images/banksoal/$file_name";
					$soal->save();
				}
			}
		} */
		if ($request->file('gambar')){
			//$mime_type = $gambar->getMimeType();
			//if ($mime_type=='image/png' || $mime_type=='image/jpg'){
				$nama_gambar = $request->file('gambar')->getClientOriginalName();
				$request->file('gambar')->move('storage/images/banksoal',$nama_gambar);
				$soal->gambar="storage/images/banksoal/$nama_gambar";
				$soal->save();
			//}
		}
		
		
		return Response()->json([
									'status'=>1,
									'pid'=>'store',
									'soal'=>$soal,                                                                                                                                 
									'message'=>'Data soal berhasil disimpan.'
								],200); 
	}
	/**
	 * daftar soal
	 */
	public function show(Request $request,$id)
	{
		$this->hasPermissionTo('SPMB-PMB-SOAL_SHOW');

		$soal=SoalPMBModel::find($id);
		if (is_null($soal))
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'show',                
									'message'=>["Fetch data soal pmb dengan ID ($id) gagal diperoleh"]
								],422); 
		}
		else
		{
			$jawaban = $soal->jawaban;
			return Response()->json([
										'status'=>1,
										'pid'=>'fetchdata',  
										'soal'=>$soal,   
										'jawaban'=>$jawaban,                                                                                                                                                                                                                                                                                                           
										'message'=>"Fetch data soal pmb dengan id ($id) berhasil diperoleh."
									],200);     
		}
	} 
	/**
	 * update soal baru
	 */
	public function update(Request $request,$id)
	{
		$this->hasPermissionTo('SPMB-PMB-SOAL_UPDATE');

		$soal=SoalPMBModel::find($id);
		if (is_null($soal))
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'update',                
									'message'=>["Fetch data soal pmb dengan ID ($id) gagal diperoleh"]
								],422); 
		}
		else
		{
			$this->validate($request, [           
				'soal'=>'required',                
				'jawaban_benar'=>'required',
			]);
			$soal->soal=$request->input('soal');
			$soal->save();
			
			\DB::table('pe3_jawaban_soal')
				->where('soal_id',$soal->id)
				->update(['status'=>0]);
				
			$jawaban = JawabanSoalPMBModel::find($request->input('jawaban_benar'));
			if (!is_null($jawaban))
			{
				$jawaban->status=1;
				$jawaban->save();
			}                        
			if ($request->has('gambar'))
			{
				if ($request->file('gambar')->isValid())
				{
					$gambar=$request->file('gambar');
					$mime_type=$gambar->getMimeType();
					if ($mime_type=='image/png' || $mime_type=='image/jpeg')
					{
						$folder=Helper::public_path('images/banksoal/');
						$file_name=uniqid('img').".".$gambar->getClientOriginalExtension();
						$gambar->move($folder,$file_name);    


						$soal->gambar="storage/images/banksoal/$file_name";
						$soal->save();
					}
				}
			}  
			return Response()->json([
										'status'=>1,
										'pid'=>'update',  
										'soal'=>$soal,                                                                                                                                                                                                                                                                                                                                                    
										'message'=>"Mengubah data soal pmb dengan id ($id) berhasil."                                        
									],200);    
		}
	} 
	 /**
	 * Menghapus soal ujian pmb
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request,$id)
	{ 
		$this->hasPermissionTo('SPMB-PMB-SOAL_DESTROY');

		$soal=SoalPMBModel::find($id);
		
		if (is_null($soal))
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'destroy',                
									'message'=>["Soal PMB dengan ID ($id) gagal dihapus"]
								],422); 
		}
		else
		{
			$nama_soal=$soal->soal;
			if (!is_null($soal->gambar) )
			{
				Storage::delete('images/banksoal/' + $soal->gambar);

			}
			$soal->delete();

			\App\Models\System\ActivityLog::log($request,[
				'object' => $this->guard()->user(), 
				'object_id' => $this->guard()->user()->id, 
				'soal_id' => $soal->id, 
				'message' => 'Menghapus Soal PMB ('.$nama_soal.') berhasil'
			]);
		
			return Response()->json([
										'status'=>1,
										'pid'=>'destroy',                
										'message'=>"Soal Ujian PMB ($nama_soal) berhasil dihapus"
									],200);         
		}
				  
	} 
}