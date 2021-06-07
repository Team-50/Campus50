<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

use Exception;

use Ramsey\Uuid\Uuid;

class TransaksiPendaftaranMHSBaruController extends Controller {  
	/**
	 * daftar komponen biaya
	 */
	public function index(Request $request)
	{
		$this->hasPermissionTo('KEUANGAN-TRANSAKSI-BIAYA-PENDAFTARAN_BROWSE');        
			
		$this->validate($request, [           
			'TA'=>'required',        
			'PRODI_ID'=>'required',
		]);
	
		$ta=$request->input('TA');      
		$prodi_id=$request->input('PRODI_ID');
					
		$daftar_transaksi = TransaksiDetailModel::select(\DB::raw('
																								pe3_transaksi_detail.id,
																								pe3_transaksi_detail.user_id,
																								pe3_transaksi_detail.transaksi_id,
																								CONCAT(pe3_transaksi.no_transaksi,\' \') AS no_transaksi,
																								pe3_transaksi_detail.biaya,
																								pe3_transaksi_detail.jumlah,
																								pe3_transaksi_detail.bulan,
																								pe3_transaksi_detail.promovalue,
																								pe3_transaksi_detail.sub_total,

																								pe3_formulir_pendaftaran.nama_mhs,

																								pe3_transaksi.no_faktur,
																								pe3_transaksi.kjur,
																								pe3_transaksi.ta,
																								pe3_transaksi.idsmt,
																								pe3_transaksi.idkelas,
																								
																								COALESCE(pe3_transaksi.no_formulir,"N.A") AS no_formulir,
																								pe3_transaksi.nim,
																								pe3_transaksi.status,
																								pe3_status_transaksi.nama_status,
																								pe3_status_transaksi.style,
																								pe3_transaksi.total,
																								pe3_transaksi.tanggal,
																								pe3_transaksi.desc,
																								pe3_transaksi_detail.created_at,
																								pe3_transaksi_detail.updated_at
																						'))
																						->join('pe3_transaksi','pe3_transaksi_detail.transaksi_id','pe3_transaksi.id')
																						->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_transaksi_detail.user_id')
																						->join('pe3_status_transaksi','pe3_transaksi.status','pe3_status_transaksi.id_status')                                                    
																						->where('pe3_transaksi_detail.kombi_id',101)                                                    
																						->orderBy('pe3_transaksi.tanggal','DESC');                                                    

		if ($request->has('SEARCH'))
		{
				$daftar_transaksi=$daftar_transaksi->whereRaw('(pe3_transaksi.nim LIKE \''.$request->input('SEARCH').'%\' OR pe3_formulir_pendaftaran.nama_mhs LIKE \'%'.$request->input('SEARCH').'%\')')                                                    
																						->get();
		}            
		else
		{
				$daftar_transaksi=$daftar_transaksi->where('pe3_transaksi.ta',$ta)                                                                                              
																						->where('pe3_transaksi.kjur',$prodi_id)                                               
																						->get();
		} 
		return Response()->json([
																'status'=>1,
																'pid'=>'fetchdata',  
																'transaksi'=>$daftar_transaksi,                                                                                                                                   
																'message'=>'Fetch data daftar transaksi berhasil.'
														],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
	}
	/**
	 * buat transaksi baru
	 */
	public function store (Request $request)
	{
			$this->hasPermissionTo('KEUANGAN-TRANSAKSI-BIAYA-PENDAFTARAN_STORE');

			$this->validate($request, [           
					'user_id'=>'required|exists:pe3_formulir_pendaftaran,user_id',                        
			]);
			
			try 
			{
					$user_id=$request->input('user_id');           
					$formulir = FormulirPendaftaranModel::find($user_id);
					$ta = $formulir->ta;
					$transaksi=TransaksiDetailModel::select(\DB::raw('
																							1
																					'))
																					->join('pe3_transaksi','pe3_transaksi_detail.transaksi_id','pe3_transaksi.id')
																					->where('pe3_transaksi.ta',$formulir->ta)
																					->where('pe3_transaksi.idsmt',$formulir->idsmt)
																					->where('pe3_transaksi.user_id',$user_id)
																					->where('pe3_transaksi_detail.kombi_id',101)                                                                                      
																					->where(function($query) {
																							$query->where('pe3_transaksi.status',0)
																									->orWhere('pe3_transaksi.status',1);
																					})                                        
																					->first();

					if (!is_null($transaksi))
					{
							$nama_mhs = $formulir->nama_mhs;
							throw new Exception ("Transaksi tidak bisa dibuat karena ($nama_mhs) sudah melakukan transaksi pada $ta.");  
					}

					$biaya_kombi=BiayaKomponenPeriodeModel::where('tahun',$formulir->ta)
																									->where('idkelas',$formulir->idkelas)
																									->where('kjur',$formulir->kjur1)
																									->where('kombi_id',101)
																									->value('biaya');
					
					if (!($biaya_kombi > 0))
					{
							throw new Exception ("Komponen Biaya Pendaftaran (101) belum disetting pada TA $ta");  
					}

					$transaksi = \DB::transaction(function () use ($request,$formulir,$biaya_kombi){
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
									'commited'=>0,
									'total'=>0,
									'tanggal'=>date('Y-m-d'),
									'desc'=>null
							]); 
							
							$transaksi_detail=TransaksiDetailModel::create([
									'id'=>Uuid::uuid4()->toString(),
									'user_id'=>$formulir->user_id,
									'transaksi_id'=>$transaksi->id,
									'no_transaksi'=>$transaksi->no_transaksi,
									'kombi_id'=>101,
									'nama_kombi'=>'BIAYA PENDAFTARAN',
									'biaya'=>$biaya_kombi,
									'jumlah'=>1,
									'sub_total'=>$biaya_kombi    
							]);

							$transaksi->total=$biaya_kombi;
							$transaksi->desc='BIAYA PENDAFTARAN '.$formulir->ta.$formulir->idsmst;
							$transaksi->save();

							return $transaksi;

					});

					return Response()->json([
																			'status'=>1,
																			'pid'=>'store',                   
																			'transaksi'=>$transaksi,                                                                                                                                   
																			'message'=>'Transaksi Biaya Pendaftaran berhasil di input.'
																	],200); 
			}
			catch (Exception $e)
			{
					return Response()->json([
							'status'=>0,
							'pid'=>'store',                                                                                                                                                 
							'message'=>[$e->getMessage()]
					], 422); 
			}        
	}
	public function update(Request $request,$id)
	{
			$this->hasPermissionTo('KEUANGAN-TRANSAKSI-BIAYA-PENDAFTARAN_UPDATE');

			$transaksi_detail = TransaksiDetailModel::find($id);
			if (is_null($transaksi_detail))
			{
					return Response()->json([
																	'status'=>0,
																	'pid'=>'update',                
																	'message'=>["Detail Transaksi dengan ($id) gagal diupdate"]
															], 422); 
			}
			else
			{
				$this->validate($request, [
					'promovalue'=>'required'
				]);
				
				$transaksi_detail = \DB::transaction(function () use ($request, $transaksi_detail) {
					$promovalue = $request->input('promovalue');
					$transaksi_detail->promovalue = $promovalue;
					$transaksi_detail->sub_total = $transaksi_detail->sub_total - $promovalue;
					$transaksi_detail->save();

					$total = \DB::table('pe3_transaksi_detail')
									->where('transaksi_id', $transaksi_detail->transaksi_id)
									->sum('sub_total');

					\DB::table('pe3_transaksi')
							->where('id', $transaksi_detail->transaksi_id)
							->update([
								'total'=>$total
							]);
					
					$transaksi = $transaksi_detail->transaksi;
					$transaksi->desc = $request->input('desc');
					$transaksi->save();
					return $transaksi_detail;
				});

				return Response()->json([
																'status'=>1,
																'pid'=>'store',                   
																'transaksi_detail'=>$transaksi_detail,                                                                                                                                   
																'message'=>'Transaksi Biaya Pendaftaran berhasil diubah.'
														], 200); 
			}
	}
	public function destroy(Request $request,$id)
	{
			$this->hasPermissionTo('KEUANGAN-TRANSAKSI-BIAYA-PENDAFTARAN_DESTROY');

			if ($this->hasRole('mahasiswa'))
			{
					$transaksi = TransaksiModel::where('user_id',$this->getUserid())
																			->find($id); 
			}
			else
			{
					$transaksi = TransaksiModel::find($id); 
			}        

			if (is_null($transaksi))
			{
					return Response()->json([
																	'status'=>0,
																	'pid'=>'destroy',           
																	'transaksi'=>$transaksi,     
																	'message'=>["Transaksi registrasi krs dengan ($id) gagal dihapus"]
															], 422); 
			}
			else if ($transaksi->status==0)
			{
					\App\Models\System\ActivityLog::log($request,[
																													'object' => $transaksi, 
																													'object_id' => $transaksi->id, 
																													'user_id' => $this->getUserid(), 
																													'message' => 'Menghapus transaksi registrasi krs dengan id ('.$id.') berhasil'
																											]);
					$transaksi->delete();
					return Response()->json([
																			'status'=>1,
																			'pid'=>'destroy',                
																			'message'=>"transaksi registrasi dengan id ($id) berhasil dihapus"
																	],200);         
			}
			else
			{
					return Response()->json([
																	'status'=>1,
																	'pid'=>'destroy',           
																	'transaksi'=>$transaksi,     
																	'message'=>["Transaksi registrasi krs dengan ($id) gagal dihapus"]
															], 422); 
			}
	}
}