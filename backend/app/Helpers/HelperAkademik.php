<?php

namespace App\Helpers;
class HelperAkademik {      
    /**
     * daftar semester
     */
    public static $semester=[
        1=>'GANJIL',
        2=>'GENAP',
        3=>'PENDEK'
    ];
    /**
     * daftar semester matakuliah
     * @var type 
     */
    public static $SemesterMatakuliah = [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8];
    /**
     * daftar semester matakuliah bentuk romawi
     * @var type 
     */
    public static $SemesterMatakuliahRomawi = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII'];
    /**
     * daftar sks matakuliah
     * @var type 
     */
    public static $sks = [0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',11=>'11',12=>'12'];   

    public static $skala_penilaian=[
        'A',
        'A-',
        'A/B',
        'B+',        
        'B',
        'B-',
        'B/C',
        'C+',
        'C',
        'C-',
        'C/D',
        'D+',
        'D',
        'E'
    ];

    public static $nilai_mutu=[
        'A'=>40,
        'A-'=>38,
        'A/B'=>36,
        'B+'=>34,        
        'B'=>30,
        'B-'=>28,
        'B/C'=>26,
        'C+'=>24,
        'C'=>20,
        'C-'=>18,
        'C/D'=>16,
        'D+'=>14,
        'D'=>10,
        'E'=>0
    ];
    public static function getSemester($id=null)
    {
        if ($id===null)
        {
			return HelperAkademik::$semester;
        }
        else if (isset(HelperAkademik::$semester[$id]))
        {
            return HelperAkademik::$semester[$id];
        }
        else
        {
            return null;
        }
    }

    public static function getNilaiMutu ($n_kual)
    {
        if (isset(HelperAkademik::$nilai_mutu[$n_kual]))
        {
            return HelperAkademik::$nilai_mutu[$n_kual];
        }
        else
        {
            return null;
        }
    }
    /**
	* digunakan untuk mem-format persentase
	*/
	public static function formatIPK ($m,$sks) {
        $result=0.00;
		if ($m > 0 && $sks > 0) {

            $temp=($m/$sks);
            if ($temp == 40)
            {
                $result = '4.00';
            }
            else if ($temp == 30)
            {
                $result = '3.00';
            }
            else if ($temp == 20)
            {
                $result = '2.00';
            }
            else if ($temp == 10)
            {
                $result = '1.00';
            }
            else 
            {
                $result = number_format($temp/10,2);
            }            
        }                     
        return $result;
	}
}