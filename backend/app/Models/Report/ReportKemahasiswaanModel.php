<?php

namespace App\Models\Report;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use App\Models\SPMB\FormulirPendaftaranModel;
use App\Helpers\Helper;

class ReportKemahasiswaanModel extends ReportModel
{   
    public function __construct($dataReport)
    {
        parent::__construct($dataReport); 
    }    
    public function daftarmahasiswa()  
    {
        $ta=$this->dataReport['TA'];
        $prodi_id=$this->dataReport['prodi_id'];
        $nama_prodi=$this->dataReport['nama_prodi'];

        $this->spreadsheet->getProperties()->setTitle("Report Daftar Mahasiswa");
        $this->spreadsheet->getProperties()->setSubject("Report Daftar Mahasiswa");

        $sheet = $this->spreadsheet->getActiveSheet();        
        $sheet->setTitle ('LAPORAN DAFTAR MAHASISWA');

        $sheet->getParent()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => '9',
            ],
        ]);

        $row=2;
        $sheet->mergeCells("A$row:L$row");				                
        $sheet->setCellValue("A$row","LAPORAN DAFTAR MAHASISWA PROGRAM STUDI $nama_prodi");

        $row+=1;
        $sheet->mergeCells("A$row:K$row");		
        $sheet->setCellValue("A$row","TAHUN MASUK $ta"); 
        
        $styleArray=array( 
            'font' => array('bold' => true,'size'=>'11'),
            'alignment' => array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                               'vertical'=>Alignment::HORIZONTAL_CENTER),								
        );               
        
        $sheet->getStyle("A1:A$row")->applyFromArray($styleArray);

        $sheet->getRowDimension(26)->setRowHeight(22);
        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(60);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(15);        
        $sheet->getColumnDimension('L')->setWidth(15);        
        
        $row+=2;        
        $sheet->setCellValue("A$row",'NO');        
        $sheet->setCellValue("B$row",'NIM');
        $sheet->setCellValue("C$row",'NIRM');        
        $sheet->setCellValue("D$row",'NAMA MAHASISWA');
        $sheet->setCellValue("E$row",'TEMPAT LAHIR');
        $sheet->setCellValue("F$row",'TANGGAL LAHIR');
        $sheet->setCellValue("G$row",'JK');
        $sheet->setCellValue("H$row",'ALAMAT');
        $sheet->setCellValue("I$row",'TELEPON HP');
        $sheet->setCellValue("J$row",'TELEPON RUMAH');
        $sheet->setCellValue("K$row",'KELAS');
        $sheet->setCellValue("L$row",'STATUS');

        $styleArray=array(
                'font' => array('bold' => true),
                'alignment' => array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                                    'vertical'=>Alignment::HORIZONTAL_CENTER),
                'borders' => array('allBorders' => array('borderStyle' =>Border::BORDER_THIN))
            );
        $sheet->getStyle("A$row:L$row")->applyFromArray($styleArray);
        $sheet->getStyle("A$row:L$row")->getAlignment()->setWrapText(true);

        $data=\DB::table('pe3_register_mahasiswa')
                ->select(\DB::raw('
                                    pe3_register_mahasiswa.nim,
                                    pe3_register_mahasiswa.nirm,
                                    pe3_formulir_pendaftaran.nama_mhs, 
                                    pe3_formulir_pendaftaran.tempat_lahir, 
                                    pe3_formulir_pendaftaran.tanggal_lahir, 
                                    pe3_formulir_pendaftaran.jk,
                                    CONCAT(pe3_formulir_pendaftaran.alamat_rumah,\' \',pe3_formulir_pendaftaran.address1_kelurahan,\' \',pe3_formulir_pendaftaran.address1_kecamatan,\' \',pe3_formulir_pendaftaran.address1_kabupaten,\' \',pe3_formulir_pendaftaran.address1_provinsi) AS alamat,
                                    pe3_formulir_pendaftaran.telp_hp,
                                    pe3_formulir_pendaftaran.telp_rumah,
                                    pe3_kelas.nkelas,
                                    pe3_status_mahasiswa.n_status
                                '))
                ->join('pe3_formulir_pendaftaran','pe3_register_mahasiswa.user_id','pe3_formulir_pendaftaran.user_id')
                ->join('pe3_kelas','pe3_register_mahasiswa.idkelas','pe3_kelas.idkelas')                
                ->join('pe3_status_mahasiswa','pe3_register_mahasiswa.k_status','pe3_status_mahasiswa.k_status')                
                ->where('pe3_register_mahasiswa.tahun',$ta)
                ->where('pe3_register_mahasiswa.kjur',$prodi_id)                                     
                ->orderBy('pe3_register_mahasiswa.idkelas','ASC')               
                ->orderBy('pe3_formulir_pendaftaran.nama_mhs','ASC')               
                ->get();

        $row+=1;
        $row_awal=$row; 
        $no=1;
        foreach ($data as $v)
        {
            $sheet->setCellValue("A$row",$no);
            $sheet->setCellValueExplicit("B$row",$v->nim,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C$row",$v->nirm,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D$row",$v->nama_mhs);
            $sheet->setCellValue("E$row",$v->tempat_lahir);
            $sheet->setCellValue("F$row",Helper::tanggal('d F Y',$v->tanggal_lahir));
            $sheet->setCellValue("G$row",$v->jk);
            $sheet->setCellValue("H$row",$v->alamat);            
            $sheet->setCellValueExplicit("I$row",$v->telp_hp,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);        
            $sheet->setCellValue("J$row",$v->telp_rumah);
            $sheet->setCellValue("K$row",$v->nkelas);
            $sheet->setCellValue("L$row",$v->n_status);

            $row+=1;
            $no+=1;
        }
        $row-=1;
        $styleArray=array(								
            'alignment' => array('horizontal'=>Alignment::HORIZONTAL_CENTER,
                               'vertical'=>Alignment::HORIZONTAL_CENTER),
            'borders' => array('allBorders' => array('borderStyle' =>Border::BORDER_THIN))
        );   																					 
        $sheet->getStyle("A$row_awal:L$row")->applyFromArray($styleArray);
        $sheet->getStyle("A$row_awal:L$row")->getAlignment()->setWrapText(true);

        $styleArray=array(								
            'alignment' => array('horizontal'=>Alignment::HORIZONTAL_LEFT)
        );																					 
        $sheet->getStyle("D$row_awal:E$row")->applyFromArray($styleArray);
        $sheet->getStyle("H$row_awal:H$row")->applyFromArray($styleArray);

        $generate_date=date('Y-m-d_H_m_s');
        return $this->download("daftar_mahasiswa_$generate_date.xlsx");
    }    
}