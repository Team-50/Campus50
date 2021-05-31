<?php

namespace App\Http\Controllers\SPMB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\Helpers\Helper;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Models\System\ConfigurationModel;
use App\Models\Surat\SuratKeluarModel;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;

use App\Models\SPMB\NilaiUjianPMBModel;

use Ramsey\Uuid\Uuid;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SKKelulusanController extends Controller {
	public function show (Request $request,$id)
	{
		$surat_keluar = SuratKeluarModel::find($id);

		if (is_null($surat_keluar))
		{
			return Response()->json([
																'status'=>0,
																'pid'=>'fetchdata',                
																'message'=>["SK Kelulusan tidak dikenali"]
														], 422); 
		}
		else
		{
			return Response()->json([
																'status'=>1,
																'pid'=>'fetchdata',															
																'surat_keluar'=>$surat_keluar                        
														],200);
		}
	}
	/**
	 * digunakan untuk mencetak sk kelulusan mahasiswa baru
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function printtopdf1(Request $request,$id)
	{
		$formulir = FormulirPendaftaranModel::select(\DB::raw('
									user_id,
									no_formulir,
									nama_mhs,
									kjur1,
									(CASE WHEN pe3_prodi.konsentrasi IS NULL OR pe3_prodi.konsentrasi = \'N.A\' THEN
										CONCAT(pe3_prodi.nama_prodi,\' (\',pe3_prodi.nama_jenjang, \')\') 
									ELSE
										CONCAT(pe3_prodi.nama_prodi,\' Kons. \',pe3_prodi.konsentrasi,\' (\',pe3_prodi.nama_jenjang, \')\') 
									END) AS nama_prodi,
									ta
							'))
							->join('pe3_prodi', 'pe3_prodi.id','pe3_formulir_pendaftaran.kjur1')
							->find($id);

		if (is_null($formulir))
		{
				return Response()->json([
																'status'=>0,
																'pid'=>'fetchdata',                
																'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
														], 422); 
		}
		else
		{
			$nilai_ujian=NilaiUjianPMBModel::find($formulir->user_id);
			if ($nilai_ujian->ket_lulus == 1 && $nilai_ujian->kjur > 0)
			{
				$config = ConfigurationModel::getCache();

				$surat_keluar = SuratKeluarModel::where('user_id_kepada',$formulir->user_id)
															->first();

				if (is_null($surat_keluar))
				{
					$id=Uuid::uuid4()->toString();

					$no_urut=SuratKeluarModel::where('ta',$formulir->ta)
																		->max('no_urut') + 1;	

					$nomor_urut=Helper::formatNomorUrut($no_urut,4);
					$bulan_surat = date('n');
					$bulan_romawi = Helper::getNamaBulanRomawi($bulan_surat);
					$tahun_surat = date('Y');

					$nomor_surat = "$nomor_urut/USM/PMB-STTI.TPI/$bulan_romawi/$tahun_surat";

					$isi_surat = '';

					$sign_qrcode = Helper::public_path('images/signature/'.$id.'.png');
					QrCode::format('png');
					QrCode::generate('Make me into a QrCode!',$sign_qrcode);	

					$surat_keluar=SuratKeluarModel::create([
						'id'=>$id,
						'user_id_created'=>$this->getUserid(),
						'nama_user_created'=>$this->getName(),
						// 'user_id_ttd'=>$config['DEFAULT_USER_ID_TTD_SK_KELULUSAN'],
						// 'nama_user_ttd'=>$config['DEFAULT_USER_NAME_TTD_SK_KELULUSAN'],
						'user_id_ttd'=>'f7d76053-b811-4624-8e8e-03475c6439ed',
						'nama_user_ttd'=>'HENDI SETIAWAN, M.Kom',
						'nomor_surat'=>$nomor_surat,
						'no_urut'=>$no_urut,
						'bulan_surat'=>$bulan_surat,
						'tahun_surat'=>$tahun_surat,
						'perihal'=>'Surat Keterangan Lulus Tes Calon Mahasiswa',
						'kepada'=>$formulir->nama_mhs,
						'user_id_kepada'=>$formulir->user_id,
						'isi_surat'=>'Telah mengikuti tes masuk sebagai calon mahasiswa STT Indonesia Tanjungpinang dan dinyatakan :',
						'keterangan'=>'LULUS',					
						'tanggal_surat'=>date('Y-m-d'),
						'qr_code'=>'storage/images/signature/'.$id.'.svg',
						'path_scanan'=>null,
						'klasifikasi_surat'=>'sklulus',
						'ta'=>$formulir->ta
					]);
				}
				else
				{
					$surat_keluar->user_id_kepada=$formulir->user_id;

					$no_urut=$surat_keluar->no_urut;	
																			
					$nomor_urut=Helper::formatNomorUrut($no_urut,4);
					$bulan_surat = $surat_keluar->bulan_surat;
					$bulan_romawi = Helper::getNamaBulanRomawi($bulan_surat);
					$tahun_surat = $surat_keluar->tahun_surat;

					$nomor_surat = "$nomor_urut/USM/PMB/$bulan_romawi/$tahun_surat";

					$surat_keluar->nomor_surat=$nomor_surat;
					$sign_qrcode = Helper::public_path('images/signature/'.$surat_keluar->id.'.svg');
					QrCode::format('svg');
					QrCode::size(100);
					QrCode::generate('https://campus50.sttindonesia.ac.id/verifikasi/'.$surat_keluar->id.'/suratkelulusan',$sign_qrcode);	
					$surat_keluar->qr_code='storage/images/signature/'.$surat_keluar->id.'.svg';
					$surat_keluar->save();
				}
				$headers=[					
					'HEADER_KOP_SURAT'=>Helper::public_path("images/headers/headerreport.png")
				];		
				$pdf = \Meneses\LaravelMpdf\Facades\Laravel•••••••Mpdf::loadView('spmb.ReportSKKelulusan',
																																[
																																	'headers'=>$headers,
																																	'nomor_surat'=>$nomor_surat,
																																	'formulir'=>$formulir,
																																	'tanggal_lulus'=>Helper::tanggal('d F Y',$nilai_ujian->updated_at),
																																	'next_day'=>Helper::nextDay('d F Y',$nilai_ujian->updated_at,7),																																	
																																	'sign_qrcode'=>$sign_qrcode,
																																	'signature'=>json_decode($config['DEFAULT_TTD_SK_KELULUSAN'],false),
																																],
																																[],
																																[
																																	'format'=>'A4',
																																	'title'=>'SK Kelulusan',
																																	'margin_left'=> 20,
																																	'margin_right'=> 20,
																																	'margin_footer'=> 10,
																																]);

				$file_pdf=Helper::public_path("exported/pdf/sklulus_".$surat_keluar->id.'.pdf');
				$pdf->save($file_pdf);

				$pdf_file="storage/exported/pdf/sklulus_".$surat_keluar->id.".pdf";

				return Response()->json([
																'status'=>1,
																'pid'=>'fetchdata',															
																'pdf_file'=>$pdf_file                        
														],200);
			}
			else
			{
				return Response()->json([
																'status'=>0,
																'pid'=>'fetchdata',															
																'SK Kelulusan tidak bisa dicetak disebakan belum lulus atau belum diterima di prodi'                       
														], 422);
			}
		}			
	}
}