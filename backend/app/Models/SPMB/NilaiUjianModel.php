<?php

namespace App\Models\SPMB;

use Illuminate\Database\Eloquent\Model;

class NilaiUjianModel extends Model {
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'pe3_nilai_ujian_pmb';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'jadwal_ujian_id',
        'jumlah_soal',
        'jawaban_benar',
        'jawaban_salah',
        'soal_tidak_terjawab',
        'passing_grade_1',
        'passing_grade_2',
        'nilai',
        'ket_lulus',
        'kjur',
        'desc',
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
