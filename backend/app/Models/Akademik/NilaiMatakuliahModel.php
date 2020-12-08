<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;

class NilaiMatakuliahModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_nilai_matakuliah';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'krsmatkul_id',
        'penyelenggaraan_id',
        'penyelenggaraan_dosen_id',
        'kelas_mhs_id', 
        'user_id_mhs', 
        'user_id_created', 
        'user_id_updated',
        'krs_id',
        
        'persentase_absen',
        'persentase_quiz',
        'persentase_tugas_individu',
        'persentase_tugas_kelompok',
        'persentase_uts',
        'persentase_uas',

        'nilai_absen',
        'nilai_quiz',
        'nilai_tugas_individu',
        'nilai_tugas_kelompok',
        'nilai_uts',
        'nilai_uas',
        'n_kuan',
        'n_kual',
        'n_mutu',

        'telah_isi_kuesioner',
        'tanggal_isi_kuesioner',
    ];
    /**
     * enable auto_increment.
     *
     * @var string
     */
    public $incrementing = false;
    /**
     * activated timestamps.
     *
     * @var string
     */
    public $timestamps = true;
}