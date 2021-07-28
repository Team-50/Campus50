<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\RegisterMahasiswaModel;
use App\Models\Akademik\DulangModel;

use Illuminate\Http\Request;

use App\Models\Keuangan\TransaksiDetailModel;

use Exception;
use Ramsey\Uuid\Uuid;

class DulangMahasiswaDOController extends Controller 
{
    /**
     * daftar mahasiswa
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('AKADEMIK-DULANG-DO_BROWSE');

        $this->validate($request, [           
            'ta'=>'required',
            'prodi_id'=>'required',
            'idsmt'=>'required',
        ]);

        $ta=$request->input('ta');
        $prodi_id=$request->input('prodi_id');
        $idsmt=$request->input('idsmt');
        
        if ($this->hasRole('mahasiswa'))
        {
            $data = DulangModel::select(\DB::raw('
                pe3_dulang.id,
                pe3_dulang.user_id,
                pe3_formulir_pendaftaran.no_formulir,
                pe3_dulang.nim,
                pe3_register_mahasiswa.nirm,
                pe3_formulir_pendaftaran.nama_mhs,
                pe3_dulang.idkelas,
                CONCAT(COALESCE(pe3_dosen.gelar_depan,\'\'),\'\',pe3_dosen.nama_dosen,\' \',COALESCE(pe3_dosen.gelar_belakang,\'\')) AS dosen_wali,            
                pe3_dulang.created_at,      
                pe3_dulang.updated_at      
            '))
            ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_dulang.user_id')
            ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_dulang.user_id')
            ->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_register_mahasiswa.dosen_id')
            ->where('pe3_dulang.tahun',$ta)   
            ->where('pe3_dulang.idsmt',$idsmt)   
            ->where('pe3_register_mahasiswa.kjur',$prodi_id)
            ->where('pe3_dulang.user_id', $this->getUserid())
            ->where('pe3_dulang.k_status','D')
            ->orderBy('pe3_dulang.idsmt','desc')
            ->orderBy('nama_mhs','asc')
            ->get();
        }
        else
        {
            $data = DulangModel::select(\DB::raw('
                pe3_dulang.id,
                pe3_dulang.user_id,
                pe3_formulir_pendaftaran.no_formulir,
                pe3_dulang.nim,
                pe3_register_mahasiswa.nirm,
                pe3_formulir_pendaftaran.nama_mhs,
                pe3_dulang.idkelas,
                pe3_dulang.k_status,    
                CONCAT(COALESCE(pe3_dosen.gelar_depan,\'\'),\'\',pe3_dosen.nama_dosen,\' \',COALESCE(pe3_dosen.gelar_belakang,\'\')) AS dosen_wali,          
                pe3_dulang.created_at,      
                pe3_dulang.updated_at      
            '))
            ->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_dulang.user_id')
            ->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_dulang.user_id')
            ->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_register_mahasiswa.dosen_id')
            ->where('pe3_dulang.tahun',$ta)   
            ->where('pe3_dulang.idsmt',$idsmt)   
            ->where('pe3_register_mahasiswa.kjur',$prodi_id)
            ->where('pe3_dulang.k_status','D')
            ->orderBy('pe3_dulang.idsmt','desc')
            ->orderBy('nama_mhs','asc')
            ->get();
        }

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'mahasiswa'=>$data,                 
                                    'message'=>'Fetch data daftar ulang mahasiswa Dropout / Putus berhasil.'
                                ], 200);
    }
	/**
	 * cek nim mahasiswa yang keluar
	 */
	public function cek(Request $request)
	{
		$this->hasPermissionTo('KEMAHASISWAAN-PROFIL-MHS_SHOW');

		$this->validate($request,[
			'nim'=>'required|numeric'
		]);
		
		try
		{
			$nim = $request->input('nim');
			$profil = \DB::table('pe3_register_mahasiswa AS A')
							->select(\DB::raw('
								A.user_id,
								A.nim,
								B.nama_mhs,
								A.k_status,
								C.n_status
							'))
							->join('pe3_formulir_pendaftaran AS B','A.user_id','B.user_id')
							->join('pe3_status_mahasiswa AS C','A.k_status','C.k_status')
							->where('A.nim', $nim)        
							->first();

			if (is_null($profil))
			{
				throw new Exception ("Data Mahasiswa dengan NIM ($nim) gagal diperoleh");
			}
			if ($profil->k_status == 'L' || $profil->k_status == 'K' || $profil->k_status == 'D')
			{
				$status = $profil->n_status;
				throw new Exception ("Tidak bisa diproses karena status Mahasiswa dengan NIM ($nim) sudah $status ");
			}			
			return Response()->json([
									'status'=>1,
									'pid'=>'fetchdata',
									'profil'=>$profil,
									'message'=>'Profil Mahasiswa berhasil diperoleh.'
								], 200); 
		}
		catch (Exception $e)
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'fetchdata',               
				'message'=>[$e->getMessage()]
			], 422); 
		}  
	
	}
	/**
   * simpan data
   */
  public function store(Request $request)
  {
		$this->hasPermissionTo('KEMAHASISWAAN-PINDAH-KELAS_STORE');

		$this->validate($request, [
			'user_id'=>'required|exists:pe3_register_mahasiswa,user_id',	  
			'idsmt'=>'required',     
			'tahun'=>'required',     
		]);
		$user_id = $request->input('user_id');
		$idsmt = $request->input('idsmt');
		$tahun = $request->input('tahun');
		try 
		{
			$data_mhs = \DB::table('pe3_register_mahasiswa AS A')
								->select(\DB::raw('
									A.user_id,																                                
									A.nim,																                                
									A.k_status,
									A.idkelas,
									C.n_status
								'))							
								->join('pe3_status_mahasiswa AS C','A.k_status','C.k_status')
								->where('A.user_id', $user_id)        
								->first();

			if (is_null($data_mhs))
			{
				throw new Exception ("Data Mahasiswa dengan NIM ($nim) gagal diperoleh");
			}
			if ($data_mhs->k_status == 'L' || $data_mhs->k_status == 'K' || $data_mhs->k_status == 'D')
			{
				$status = $data_mhs->n_status;
				throw new Exception ("Tidak bisa diproses karena status Mahasiswa dengan NIM ($nim) sudah $status ");
			}
			$dulang = \DB::transaction(function () use ($request,$data_mhs) {
				$dulang = \DB::table('pe3_dulang')
					->where('user_id', $data_mhs->user_id)
					->where('idsmt', $request->input('idsmt'))
					->where('tahun', $request->input('tahun'))
					->first();

				if (is_null($dulang))	
				{
					$dulang = DulangModel::create([
						'id'=>Uuid::uuid4()->toString(),
						'user_id'=>$request->input('user_id'),
						'nim'=>$data_mhs->nim,		  
						'idkelas'=>$data_mhs->idkelas,
						'status_sebelumnya'=>$data_mhs->k_status,
						'k_status'=>'D',
						'idsmt'=>$request->input('idsmt'),
						'tahun'=>$request->input('tahun'),
						'update_info'=>0,
						'descr'=>$request->input('descr'),
					]);
				}
				else
				{
					\DB::table('pe3_dulang')
					->where('id', $dulang->id)
						->update([
							'status_sebelumnya'=>$dulang->k_status,
							'k_status'=>'D'
						]);					
				}
				\DB::table('pe3_register_mahasiswa')
					->where('user_id', $dulang->user_id)
						->update([						
							'k_status'=>'D'
						]);					
				return $dulang;
			}); 
	  
			$data = DulangModel::select(\DB::raw('
				pe3_dulang.id,
				pe3_dulang.user_id,
				pe3_formulir_pendaftaran.no_formulir,
				pe3_dulang.nim,
				pe3_register_mahasiswa.nirm,
				pe3_formulir_pendaftaran.nama_mhs,
				pe3_dulang.idkelas,
				pe3_dulang.k_status,    
				CONCAT(COALESCE(pe3_dosen.gelar_depan,\'\'),\'\',pe3_dosen.nama_dosen,\' \',COALESCE(pe3_dosen.gelar_belakang,\'\')) AS dosen_wali,          
				pe3_dulang.created_at,      
				pe3_dulang.updated_at      
			'))
			->join('pe3_register_mahasiswa','pe3_register_mahasiswa.user_id','pe3_dulang.user_id')
			->join('pe3_formulir_pendaftaran','pe3_formulir_pendaftaran.user_id','pe3_dulang.user_id')
			->leftJoin('pe3_dosen','pe3_dosen.user_id','pe3_register_mahasiswa.dosen_id')
			->where('pe3_dulang.id',$dulang->id)		
			->first();
			
					
			return Response()->json([
									'status'=>1,
									'pid'=>'store',  
									'mahasiswa'=>$data,
									'message'=>'Status Mahasiswa berhasil di ubah menjadi lulus.'
								], 200);
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('AKADEMIK-DULANG-DO_DESTROY');

        $dulang = DulangModel::find($id); 
        
        if (is_null($dulang))
        {
            return Response()->json([
                'status'=>0,
                'pid'=>'destroy',    
                'message'=>["Daftar Ulang Mahasiswa Dropout / Putus ($id) gagal dihapus"]
            ], 422); 
        }
        else
        {
            \App\Models\System\ActivityLog::log($request,[
                'object' => $dulang, 
                'object_id' => $dulang->id, 
                'user_id' => $this->getUserid(), 
                'message' => 'Menghapus daftar ulang mahasiswa dropout / putus dengan id ('.$dulang->id.') berhasil'
            ]);        
            $register_mahasiswa=$dulang->register_mahasiswa;
            $register_mahasiswa->k_status = $dulang->status_sebelumnya;
            $register_mahasiswa->save();
            
            $dulang->delete();
            
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',    
                                    'message'=>"Daftar Ulang dengan kode ($id) berhasil dihapus"
                                ], 200);    
        }
                  
    }
}