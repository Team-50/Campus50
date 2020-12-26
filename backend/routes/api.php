<?php
$router->get('/', function () use ($router) {
    return 'PortalEkampus API';
});

$router->group(['prefix'=>'v3'], function () use ($router)
{

    //dmaster - provinsi
    $router->get('/datamaster/provinsi',['uses'=>'DMaster\ProvinsiController@index','as'=>'provinsi.index']);
    $router->get('/datamaster/provinsi/{id}/kabupaten',['uses'=>'DMaster\ProvinsiController@kabupaten','as'=>'provinsi.kabupaten']);

    //dmaster - kabupaten
    $router->get('/datamaster/kabupaten',['uses'=>'DMaster\KabupatenController@index','as'=>'kabupaten.index']);
    $router->get('/datamaster/kabupaten/{id}/kecamatan',['uses'=>'DMaster\KabupatenController@kecamatan','as'=>'kabupaten.kecamatan']);

    //dmaster - kecamatan
    $router->get('/datamaster/kecamatan',['uses'=>'DMaster\KecamatanController@index','as'=>'kecamatan.index']);
    $router->get('/datamaster/kecamatan/{id}/desa',['uses'=>'DMaster\KecamatanController@desa','as'=>'kecamatan.desa']);

    //dmaster - tahun akademik
    $router->get('/datamaster/tahunakademik',['uses'=>'DMaster\TahunAkademikController@index','as'=>'tahunakademik.index']);
    $router->get('/datamaster/tahunakademik/{id}/daftarbulan',['uses'=>'DMaster\TahunAkademikController@daftarbulan','as'=>'tahunakademik.daftarbulan']);

    //data master - persyaratan
    $router->post('/datamaster/persyaratan',['uses'=>'DMaster\PersyaratanController@index','as'=>'persyaratan.index']);
    $router->post('/datamaster/persyaratan/{id}/proses',['uses'=>'DMaster\PersyaratanController@proses','as'=>'persyaratan.proses']);

    //data master - fakultas
    $router->get('/datamaster/fakultas',['uses'=>'DMaster\FakultasController@index','as'=>'fakultas.index']);
    $router->get('/datamaster/fakultas/{id}/programstudi',['uses'=>'DMaster\FakultasController@programstudi','as'=>'fakultas.programstudi']);

    //data master - program studi
    $router->get('/datamaster/programstudi',['uses'=>'DMaster\ProgramStudiController@index','as'=>'programstudi.index']);
    $router->get('/datamaster/programstudi/jenjangstudi',['uses'=>'DMaster\ProgramStudiController@jenjangstudi','as'=>'programstudi.jenjangstudi']);

    //data master - kelas
    $router->get('/datamaster/kelas',['uses'=>'DMaster\KelasController@index','as'=>'kelas.index']);

    //pendaftaran mahasiswa baru
    $router->post('/spmb/pmb/store',['uses'=>'SPMB\PMBController@store','as'=>'pmb.store']);
    $router->post('/spmb/pmb/konfirmasi',['uses'=>'SPMB\PMBController@konfirmasi','as'=>'pmb.konfirmasi']);

    //keuangan - channel pembayaran
    $router->get('/keuangan/channelpembayaran',['uses'=>'Keuangan\ChannelPembayaranController@index','as'=>'channelpembayaran.index']);

    //akademik - matakuliah
    $router->post('/akademik/matakuliah/',['uses'=>'Akademik\MatakuliahController@index','as'=>'matakuliah.index']);

    //system - daftar dosen
    $router->get('/system/usersdosen/pengampu',['uses'=>'System\UsersDosenController@pengampu','as'=>'usersdosen.pengampu']);

    //untuk uifront
    $router->get('/system/setting/uifront',['uses'=>'System\UIController@frontend','as'=>'uifront.frontend']);

    //auth login
    $router->post('/auth/login',['uses'=>'AuthController@login','as'=>'auth.login']);
});

$router->group(['prefix'=>'v3','middleware'=>'auth:api'], function () use ($router)
{
    //authentication
    $router->post('/auth/logout',['uses'=>'AuthController@logout','as'=>'auth.logout']);
    $router->get('/auth/refresh',['uses'=>'AuthController@refresh','as'=>'auth.refresh']);
    $router->get('/auth/me',['uses'=>'AuthController@me','as'=>'auth.me']);

    // dashboard
    $router->post('/dashboard/pmb',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\SPMBController@index','as'=>'dashboardspmb.index']);
    $router->post('/dashboard/keuangan',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\KeuanganController@index','as'=>'dashboardkeuangan.index']);

    //data master - kelas
    $router->post('/datamaster/kelas/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KelasController@store','as'=>'kelas.store']);
    $router->put('/datamaster/kelas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KelasController@update','as'=>'kelas.update']);
    $router->delete('/datamaster/kelas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KelasController@destroy','as'=>'`kelas`.destroy']);

    //data master - ruangan kelas
    $router->get('/datamaster/ruangankelas',['middleware'=>['role:superadmin|pmb'],'uses'=>'DMaster\RuanganKelasController@index','as'=>'ruangankelas.index']);
    $router->post('/datamaster/ruangankelas/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\RuanganKelasController@store','as'=>'ruangankelas.store']);
    $router->get('/datamaster/ruangankelas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\RuanganKelasController@show','as'=>'ruangankelas.show']);
    $router->put('/datamaster/ruangankelas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\RuanganKelasController@update','as'=>'ruangankelas.update']);
    $router->delete('/datamaster/ruangankelas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\RuanganKelasController@destroy','as'=>'ruangankelas.destroy']);

    //data master - persyaratan
    $router->post('/datamaster/persyaratan/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\PersyaratanController@store','as'=>'persyaratan.store']);
    $router->put('/datamaster/persyaratan/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\PersyaratanController@update','as'=>'persyaratan.update']);
    $router->delete('/datamaster/persyaratan/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\PersyaratanController@destroy','as'=>'`persyaratan.destroy']);

    //data master - tahun akademik
    $router->post('/datamaster/tahunakademik/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAkademikController@store','as'=>'tahunakademik.store']);
    $router->put('/datamaster/tahunakademik/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAkademikController@update','as'=>'tahunakademik.update']);
    $router->delete('/datamaster/tahunakademik/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAkademikController@destroy','as'=>'`tahunakademik.destroy']);

    //data master - jabatan akademik
    $router->get('/datamaster/jabatanakademik',['uses'=>'DMaster\JabatanAkademikController@index','as'=>'jabatanakademik.index']);

    //data master - jenjang studi
    $router->get('/datamaster/jenjangstudi',['uses'=>'DMaster\JenjangStudiController@index','as'=>'jenjangstudi.index']);

    //data master - status mahasiswa
    $router->get('/datamaster/statusmahasiswa',['uses'=>'DMaster\StatusMahasiswaController@index','as'=>'statusmahasiswa.index']);

    //data master - fakultas
    $router->post('/datamaster/fakultas/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\FakultasController@store','as'=>'fakultas.store']);
    $router->put('/datamaster/fakultas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\FakultasController@update','as'=>'fakultas.update']);
    $router->delete('/datamaster/fakultas/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\FakultasController@destroy','as'=>'`fakultas.destroy']);

    //data master - program studi
    $router->post('/datamaster/programstudi/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\ProgramStudiController@store','as'=>'programstudi.store']);
    $router->put('/datamaster/programstudi/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\ProgramStudiController@update','as'=>'programstudi.update']);
    $router->delete('/datamaster/programstudi/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\ProgramStudiController@destroy','as'=>'`programstudi`.destroy']);

    //spmb - soal pmb
    $router->post('/spmb/soalpmb',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\SoalPMBController@index','as'=>'soalpmb.index']);
    $router->post('/spmb/soalpmb/store',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\SoalPMBController@store','as'=>'soalpmb.store']);
    $router->get('/spmb/soalpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\SoalPMBController@show','as'=>'soalpmb.show']);
    $router->put('/spmb/soalpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\SoalPMBController@update','as'=>'soalpmb.update']);
    $router->delete('/spmb/soalpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\SoalPMBController@destroy','as'=>'soalpmb.destroy']);

    //spmb - pendaftaran mahasiswa baru
    $router->post('/spmb/pmb',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\PMBController@index','as'=>'pmb.index']);
    $router->post('/spmb/pmb/storependaftar',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBController@storependaftar','as'=>'pmb.storependaftar']);
    $router->post('/spmb/pmb/resend',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBController@resend','as'=>'pmb.resend']);
    $router->put('/spmb/pmb/updatependaftar/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBController@updatependaftar','as'=>'pmb.updatependaftar']);
    $router->delete('/spmb/pmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBController@destroy','as'=>'pmb.destroy']);

    //spmb - formulir pendaftaran
    $router->post('/spmb/formulirpendaftaran',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\PMBController@formulirpendaftaran','as'=>'formulirpendaftaran.index']);
    $router->get('/spmb/formulirpendaftaran/{id}',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBController@show','as'=>'formulirpendaftaran.show']);
    $router->put('/spmb/formulirpendaftaran/{id}',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBController@update','as'=>'formulirpendaftaran.update']);

    //spmb - jadwal ujian pmb
    $router->post('/spmb/jadwalujianpmb',['middleware'=>['role:superadmin|pmb|mahasiswabaru|keuangan'],'uses'=>'SPMB\JadwalUjianPMBController@index','as'=>'jadwalujianpmb.index']);
    $router->post('/spmb/jadwalujianpmb/store',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\JadwalUjianPMBController@store','as'=>'jadwalujianpmb.store']);
    $router->get('/spmb/jadwalujianpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\JadwalUjianPMBController@show','as'=>'jadwalujianpmb.show']);
    $router->put('/spmb/jadwalujianpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\JadwalUjianPMBController@update','as'=>'jadwalujianpmb.update']);
    $router->put('/spmb/jadwalujianpmb/updatestatusujian/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\JadwalUjianPMBController@updatestatusujian','as'=>'jadwalujianpmb.updatestatusujian']);
    $router->delete('/spmb/jadwalujianpmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\JadwalUjianPMBController@destroy','as'=>'jadwalujianpmb.destroy']);

    //spmb - passing grade
    $router->post('/spmb/passinggrade',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBPassingGradeController@index','as'=>'passinggrade.index']);
    $router->post('/spmb/passinggrade/loadprodi',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBPassingGradeController@loadprodi','as'=>'passinggrade.loadprodi']);
    $router->put('/spmb/passinggrade/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBPassingGradeController@update','as'=>'passinggrade.update']);

    //spmb - ujianonline
    //spmb/ujianonline/jadwal, digunakan untuk mendapatkan daftar jadwal ujian dengan berbagai macam kriteria
    $router->post('/spmb/ujianonline/jadwal',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@jadwal','as'=>'spmbujianonline.jadwal']);
    //spmb/ujianonline/soal/{id}, id disini di isi dengan user_id. digunakan untuk mendapatkan daftar soal ujian
    $router->get('/spmb/ujianonline/soal/{id}',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@soal','as'=>'spmbujianonline.soal']);
    //spmb/ujianonline/peserta/{id}, id disini di isi dengan user_id. digunakan untuk mendapatkan data kepersertaan dalam satu ujian
    $router->get('/spmb/ujianonline/peserta/{id}',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@peserta','as'=>'spmbujianonline.peserta']);
    //spmb/ujianonline/daftar, id disini di isi dengan user_id. digunakan untuk mendaftarkan calon mahasiswa ke jadwal ujian
    $router->post('/spmb/ujianonline/daftar',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@daftarujian','as'=>'spmbujianonline.daftar']);
    //spmb/ujianonline/mulaiujian, digunakan untuk mendaftarkan memulai ujian
    $router->put('/spmb/ujianonline/mulaiujian',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@mulaiujian','as'=>'spmbujianonline.mulaiujian']);
    //spmb/ujianonline/store, digunakan untuk menyimpan jawaban soal ujian online
    $router->post('/spmb/ujianonline/store',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@store','as'=>'spmbujianonline.store']);
    //spmb/ujianonline/selesaiujian, digunakan untuk selesai ujian
    $router->put('/spmb/ujianonline/selesaiujian',['middleware'=>['role:mahasiswabaru'],'uses'=>'SPMB\PMBUjianOnlineController@selesaiujian','as'=>'spmbujianonline.selesaiujian']);

    //spmb - nilai ujian
    $router->post('/spmb/nilaiujian',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\NilaiUjianController@index','as'=>'nilaiujian.index']);
    //spmb/nilaiujian/{id}, id disini di is dengan user_id
    $router->get('/spmb/nilaiujian/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\NilaiUjianController@show','as'=>'nilaiujian.show']);
    $router->put('/spmb/nilaiujian/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\NilaiUjianController@update','as'=>'nilaiujian.update']);
    $router->delete('/spmb/nilaiujian/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\NilaiUjianController@destroy','as'=>'nilaiujian.destroy']);

    $router->post('/spmb/nilaiujian/store',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\NilaiUjianController@store','as'=>'nilaiujian.store']);

    //spmb - report fakultas
    $router->post('/spmb/reportspmbfakultas',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\ReportSPMBFakultasController@index','as'=>'reportspmbfakultas.index']);
    $router->post('/spmb/reportspmbfakultas/printtoexcel',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\ReportSPMBFakultasController@printtoexcel','as'=>'reportspmbfakultas.printtoexcel']);

    //spmb - report prodi
    $router->post('/spmb/reportspmbprodi/printtoexcel',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\ReportSPMBProdiController@printtoexcel','as'=>'reportspmbprodi.printtoexcel']);

    //spmb - report report kelulusan
    $router->post('/spmb/reportspmbkelulusan',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\ReportKelulusanController@index','as'=>'reportspmbkelulusan.index']);
    $router->post('/spmb/reportspmbkelulusan/printtoexcel',['middleware'=>['role:superadmin|pmb|keuangan'],'uses'=>'SPMB\ReportKelulusanController@printtoexcel','as'=>'reportspmbkelulusan.printtoexcel']);

    //spmb - persyaratan
    $router->post('/spmb/pmbpersyaratan',['middleware'=>['role:superadmin|pmb|mahasiswabaru|keuangan'],'uses'=>'SPMB\PMBPersyaratanController@index','as'=>'pmbpersyaratan.index']);
    $router->get('/spmb/pmbpersyaratan/{id}',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBPersyaratanController@show','as'=>'pmbpersyaratan.show']);
    $router->post('/spmb/pmbpersyaratan/upload/{id}',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBPersyaratanController@upload','as'=>'pmbpersyaratan.upload']);
    $router->post('/spmb/pmbpersyaratan/verifikasipersyaratan/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'SPMB\PMBPersyaratanController@verifikasipersyaratan','as'=>'pmbpersyaratan.verifikasipersyaratan']);
    $router->delete('/spmb/pmbpersyaratan/hapusfilepersyaratan/{id}',['middleware'=>['role:superadmin|pmb|mahasiswabaru'],'uses'=>'SPMB\PMBPersyaratanController@hapusfilepersyaratan','as'=>'pmbpersyaratan.hapusfilepersyaratan']);

    //keuangan - status transaksi
    $router->get('/keuangan/statustransaksi',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\StatusTransaksiController@index','as'=>'statustransaksi.index']);
    $router->put('/keuangan/statustransaksi/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\StatusTransaksiController@update','as'=>'statustransaksi.update']);

    //keuangan - komponen biaya
    $router->get('/keuangan/komponenbiaya',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\KomponenBiayaController@index','as'=>'keuangan.index']);

    //keuangan - biaya komponen periode
    $router->post('/keuangan/biayakomponenperiode',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@index','as'=>'biayakomponenperiode.index']);
    $router->post('/keuangan/biayakomponenperiode/loadkombiperiode',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@loadkombiperiode','as'=>'biayakomponenperiode.loadkombiperiode']);
    $router->post('/keuangan/biayakomponenperiode/updatebiaya',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@updatebiaya','as'=>'biayakomponenperiode.updatebiaya']);

    //keuangan - metode pembayaran [transfer bank]
    $router->get('/keuangan/transferbank',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransferBankController@index','as'=>'transferbank.index']);
    $router->post('/keuangan/transferbank/store',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransferBankController@store','as'=>'transferbank.store']);
    $router->get('/keuangan/transferbank/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransferBankController@show','as'=>'transferbank.show']);
    $router->put('/keuangan/transferbank/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransferBankController@update','as'=>'transferbank.update']);
    $router->delete('/keuangan/transferbank/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransferBankController@destroy','as'=>'transferbank.destroy']);

    //keuangan - transaksi
    $router->post('/keuangan/transaksi',['middleware'=>['role:superadmin|keuangan|mahasiswabaru|mahasiswa'],'uses'=>'Keuangan\TransaksiController@index','as'=>'transaksi.index']);
    $router->get('/keuangan/transaksi/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransaksiController@show','as'=>'transaksi.show']);
    $router->post('/keuangan/transaksi/cancel',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransaksiController@cancel','as'=>'transaksi.cancel']);
    $router->put('/keuangan/transaksi/verifikasi/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\TransaksiController@verifikasi','as'=>'transaksi.verifikasi']);
    //digunakan untuk mendapatkan spp milik user_id mhs baru dengan status sudah bayar
    $router->post('/keuangan/transaksi/{id}/sppmhsbaru',['uses'=>'Keuangan\TransaksiController@sppmhsbaru','as'=>'transaksi.sppmhsbaru']);
    
    //keuangan - transaksi spp
    $router->post('/keuangan/transaksi-spp',['middleware'=>['role:superadmin|keuangan|mahasiswabaru|mahasiswa'],'uses'=>'Keuangan\TransaksiSPPController@index','as'=>'transaksi-spp.index']);
    $router->get('/keuangan/transaksi-spp/{id}',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiSPPController@show','as'=>'transaksi-spp.show']);        
    $router->post('/keuangan/transaksi-spp/new',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiSPPController@newtransaction','as'=>'transaksi-spp.new']);        
    $router->post('/keuangan/transaksi-spp/store',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiSPPController@store','as'=>'transaksi-spp.store']);        
    // id delete detail id
    $router->delete('/keuangan/transaksi-spp/{id}',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiSPPController@destroy','as'=>'transaksi-spp.destroy']);        
    
    //keuangan - transaksi regisrasikrs
    $router->post('/keuangan/transaksi-registrasikrs',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiRegistrasiKRSController@index','as'=>'transaksi-registrasikrs.index']);    
    $router->post('/keuangan/transaksi-registrasikrs/store',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiRegistrasiKRSController@store','as'=>'transaksi-registrasikrs.store']);    
    $router->delete('/keuangan/transaksi-registrasikrs/{id}',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\TransaksiRegistrasiKRSController@destroy','as'=>'transaksi-registrasikrs.destroy']);    

    //keuangan - konfirmasi pembayaran
    $router->post('/keuangan/konfirmasipembayaran',['middleware'=>['role:superadmin|keuangan|mahasiswa|mahasiswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@index','as'=>'konfirmasipembayaran.index']);
    $router->post('/keuangan/konfirmasipembayaran/store',['middleware'=>['role:superadmin|keuangan|mahasiswa|mahasiswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@store','as'=>'konfirmasipembayaran.store']);
    $router->get('/keuangan/konfirmasipembayaran/{id}',['middleware'=>['role:superadmin|keuangan|mahasiswa|mahasiswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@show','as'=>'konfirmasipembayaran.show']);
    $router->put('/keuangan/konfirmasipembayaran/{id}',['middleware'=>['role:superadmin|keuangan|mahasiswa|mahasiswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@update','as'=>'konfirmasipembayaran.update']);

    //akademik - dosen wali
    $router->get('/akademik/dosenwali',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DosenWaliController@index','as'=>'dosenwali.index']);
    $router->post('/akademik/dosenwali/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DosenWaliController@store','as'=>'dosenwali.store']);
    $router->put('/akademik/dosenwali/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DosenWaliController@update','as'=>'dosenwali.update']);
    $router->delete('/akademik/dosenwali/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DosenWaliController@destroy','as'=>'dosenwali.destroy']);

    //akademik - group matakuliah
    $router->get('/akademik/groupmatakuliah/',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\GroupMatakuliahController@index','as'=>'groupmatakuliah.index']);

    //akademik - matakuliah
    $router->post('/akademik/matakuliah/store',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@store','as'=>'matakuliah.store']);
    $router->get('/akademik/matakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@show','as'=>'matakuliah.show']);
    $router->put('/akademik/matakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@update','as'=>'matakuliah.update']);
    $router->delete('/akademik/matakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@destroy','as'=>'matakuliah.destroy']);
    //id disini adalah tujuan salin matakuliah
    $router->post('/akademik/matakuliah/salinmatkul/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@salinmatkul','as'=>'matakuliah.salinmatkul']);
    //daftar matakuliah yang tidak ada di dalam penyelenggaraan
    $router->post('/akademik/matakuliah/penyelenggaraan',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\MatakuliahController@penyelenggaraan','as'=>'matakuliah.penyelenggaraan']);

    //akademik - dulang
    $router->post('/akademik/dulang/dulangnotinkrs',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa'],'uses'=>'Akademik\DulangController@dulangnotinkrs','as'=>'dulang.dulangnotinkrs']);
    $router->post('/akademik/dulang/cekdulangkrs',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa'],'uses'=>'Akademik\DulangController@cekdulangkrs','as'=>'dulang.cekdulangkrs']);
    
    //akademik - daftar ulang - mahasiswa belum punya nim
    $router->post('/akademik/dulang/mhsbelumpunyanim',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\MahasiswaBelumPunyaNIMController@index','as'=>'mhsbelumpunyanim.index']);
    $router->post('/akademik/dulang/mhsbelumpunyanim/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\MahasiswaBelumPunyaNIMController@store','as'=>'mhsbelumpunyanim.store']);
    
    //akademik - daftar ulang - mahasiswa baru
    $router->post('/akademik/dulang/mhsbaru',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaBaruController@index','as'=>'dulangmhsbaru.index']);
    $router->post('/akademik/dulang/mhsbaru/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaBaruController@store','as'=>'dulangmhsbaru.store']);
    $router->get('/akademik/dulang/mhsbaru/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaBaruController@show','as'=>'dulangmhsbaru.show']);
    $router->put('/akademik/dulang/mhsbaru/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaBaruController@update','as'=>'dulangmhsbaru.update']);
    $router->delete('/akademik/dulang/mhsbaru/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaBaruController@destroy','as'=>'dulangmhsbaru.destroy']);
    
    //akademik - daftar ulang - mahasiswa lama
    $router->post('/akademik/dulang/mhslama',['middleware'=>['role:superadmin|akademik|keuangan'],'uses'=>'Akademik\DulangMahasiswaLamaController@index','as'=>'dulangmhslama.index']);
    $router->post('/akademik/dulang/mhslama/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaLamaController@store','as'=>'dulangmhslama.store']);
    $router->get('/akademik/dulang/mhslama/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaLamaController@show','as'=>'dulangmhslama.show']);
    $router->put('/akademik/dulang/mhslama/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaLamaController@update','as'=>'dulangmhslama.update']);
    $router->delete('/akademik/dulang/mhslama/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'Akademik\DulangMahasiswaLamaController@destroy','as'=>'dulangmhslama.destroy']);

    //akademik - kemahasiswaan - daftar mahasiswa
    $router->post('/akademik/kemahasiswaan/updatestatus/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'Akademik\KemahasiswaanController@updatestatus','as'=>'kemahasiswaan.updatestatus']);
    $router->post('/akademik/kemahasiswaan/daftarmhs',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|keuangan'],'uses'=>'Akademik\KemahasiswaanDaftarMahasiswaController@index','as'=>'daftarmhs.index']);
    $router->post('/akademik/kemahasiswaan/daftarmhs/printtoexcel',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|keuangan'],'uses'=>'Akademik\KemahasiswaanDaftarMahasiswaController@printtoexcel','as'=>'daftarmhs.printtoexcel']);
    
    $router->get('/akademik/kemahasiswaan/biodatamhs1/{id}',['middleware'=>['role:superadmin|akademik|programstudi|keuangan|mahasiswa'],'uses'=>'Akademik\MahasiswaController@biodatamhs1','as'=>'mahasiswa.biodatamhs1']);
    //uri ini diakses bila mahasiswa tidak ada tetap mengembalikan nilai yaitu null atau status = 0
    $router->get('/akademik/kemahasiswaan/biodatamhs2/{id}',['middleware'=>['role:superadmin|akademik|programstudi|keuangan|mahasiswa'],'uses'=>'Akademik\MahasiswaController@biodatamhs2','as'=>'mahasiswa.biodatamhs2']);

    //akademik - perkuliahan - penyelenggaraan
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|puslahta'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@index','as'=>'penyelenggaraanmatakuliah.index']);
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah/store',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@store','as'=>'penyelenggaraanmatakuliah.store']);
    $router->get('/akademik/perkuliahan/penyelenggaraanmatakuliah/member/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@member','as'=>'penyelenggaraanmatakuliah.member']);    
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah/members',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@members','as'=>'penyelenggaraanmatakuliah.members']);    
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah/pengampu',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@pengampu','as'=>'penyelenggaraanmatakuliah.pengampu']);    
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah/matakuliah',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@matakuliah','as'=>'penyelenggaraanmatakuliah.matakuliah']);
    $router->post('/akademik/perkuliahan/penyelenggaraanmatakuliah/storedosenpengampu',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@storedosenpengampu','as'=>'penyelenggaraanmatakuliah.storedosenpengampu']);
    $router->get('/akademik/perkuliahan/penyelenggaraanmatakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@show','as'=>'penyelenggaraanmatakuliah.show']);
    $router->put('/akademik/perkuliahan/penyelenggaraanmatakuliah/updateketua/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@updateketua','as'=>'penyelenggaraanmatakuliah.updateketua']);
    $router->delete('/akademik/perkuliahan/penyelenggaraanmatakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@destroy','as'=>'penyelenggaraanmatakuliah.destroy']);
    $router->delete('/akademik/perkuliahan/penyelenggaraanmatakuliah/deletepengampu/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PenyelenggaraanMatakuliahController@destroypengampu','as'=>'penyelenggaraanmatakuliah.destroypengampu']);
    
    //akademik - perkuliahan - krs
    $router->post('/akademik/perkuliahan/krs',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@index','as'=>'krs.index']);
    $router->post('/akademik/perkuliahan/krs/store',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@store','as'=>'krs.store']);
    //digunakan untuk mendapatkan daftar matakuliah yang diselenggarakan dan belum terdaftar di krsnya mhs
    $router->post('/akademik/perkuliahan/krs/penyelenggaraan',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@penyelenggaraan','as'=>'krs.penyelenggaraan']);
    $router->post('/akademik/perkuliahan/krs/storematkul',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@storematkul','as'=>'krs.storematkul']);
    $router->get('/akademik/perkuliahan/krs/{id}',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@show','as'=>'krs.show']);
    $router->post('/akademik/perkuliahan/krs/cekkrs',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@cekkrs','as'=>'krs.cekkrs']);
    $router->put('/akademik/perkuliahan/krs/updatestatus/{id}',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@updatestatus','as'=>'krs.updatestatus']);
    $router->delete('/akademik/perkuliahan/krs/{id}',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@destroy','as'=>'krs.destroy']);
    $router->delete('/akademik/perkuliahan/krs/deletematkul/{id}',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali'],'uses'=>'Akademik\KRSController@destroymatkul','as'=>'krs.destroymatkul']);
    //id krs
    $router->get('/akademik/perkuliahan/krs/printpdf/{id}',['middleware'=>['role:superadmin|akademik|programstudi|mahasiswa|dosenwali|puslahta'],'uses'=>'Akademik\KRSController@printpdf','as'=>'krs.printpdf']);
    
    //akademik - perkuliahan - pembagian kelas
    $router->post('/akademik/perkuliahan/pembagiankelas',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|mahasiswa|dosen'],'uses'=>'Akademik\PembagianKelasController@index','as'=>'pembagiankelas.index']);
    $router->post('/akademik/perkuliahan/pembagiankelas/store',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@store','as'=>'pembagiankelas.store']);
    $router->post('/akademik/perkuliahan/pembagiankelas/pengampu',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@pengampu','as'=>'pembagiankelas.pengampu']);
    $router->get('/akademik/perkuliahan/pembagiankelas/matakuliah/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@matakuliah','as'=>'pembagiankelas.matakuliah']);
    $router->get('/akademik/perkuliahan/pembagiankelas/peserta/{id}',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|dosen'],'uses'=>'Akademik\PembagianKelasController@peserta','as'=>'pembagiankelas.peserta']);
    $router->post('/akademik/perkuliahan/pembagiankelas/storematakuliah',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@storematakuliah','as'=>'pembagiankelas.storematakuliah']);
    $router->post('/akademik/perkuliahan/pembagiankelas/storepeserta',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@storepeserta','as'=>'pembagiankelas.storepeserta']);    
    $router->get('/akademik/perkuliahan/pembagiankelas/{id}',['middleware'=>['role:superadmin|akademik|programstudi|puslahta|dosen'],'uses'=>'Akademik\PembagianKelasController@show','as'=>'pembagiankelas.show']);
    $router->put('/akademik/perkuliahan/pembagiankelas/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@update','as'=>'pembagiankelas.update']);
    $router->delete('/akademik/perkuliahan/pembagiankelas/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@destroy','as'=>'pembagiankelas.destroy']);
    $router->delete('/akademik/perkuliahan/pembagiankelas/deletematkul/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@destroymatkul','as'=>'pembagiankelas.destroymatkul']);
    $router->delete('/akademik/perkuliahan/pembagiankelas/deletepeserta/{id}',['middleware'=>['role:superadmin|akademik|programstudi'],'uses'=>'Akademik\PembagianKelasController@destroypeserta','as'=>'pembagiankelas.destroypeserta']);

    // store nilai maksudnya menyimpan komponen nilai
    $router->get('/akademik/perkuliahan/pembagiankelas/nilaikomponen/{id}',['uses'=>'Akademik\PembagianKelasController@nilaikomponen','as'=>'pembagiankelas.nilaikomponen']);
    $router->post('/akademik/perkuliahan/pembagiankelas/storenilai',['middleware'=>['role:dosen'],'uses'=>'Akademik\PembagianKelasController@storenilai','as'=>'pembagiankelas.storenilai']);

    //akademik - perkuliahan - nilai
    $router->post('/akademik/nilai/matakuliah',['middleware'=>['role:superadmin|akademik|puslahta'],'uses'=>'Akademik\NilaiMatakuliahController@index','as'=>'nilaimatakuliah.index']);
    //id disini adalah kelas_mhs_id
    $router->get('/akademik/nilai/matakuliah/pesertakelas/{id}',['middleware'=>['role:puslahta|dosen'],'uses'=>'Akademik\NilaiMatakuliahController@pesertakelas','as'=>'nilaimatakuliah.pesertakelas']);
    $router->post('/akademik/nilai/matakuliah/perkelas/storeperkelas',['middleware'=>['role:puslahta'],'uses'=>'Akademik\NilaiMatakuliahController@storeperkelas','as'=>'nilaimatakuliah.storeperkelas']);
    $router->post('/akademik/nilai/matakuliah/perdosen/storeperdosen',['middleware'=>['role:dosen'],'uses'=>'Akademik\NilaiMatakuliahController@storeperdosen','as'=>'nilaimatakuliah.storeperdosen']);
    $router->get('/akademik/nilai/matakuliah/perkrs/{id}',['middleware'=>['role:puslahta'],'uses'=>'Akademik\NilaiMatakuliahController@perkrs','as'=>'nilaimatakuliah.perkrs']);
    $router->post('/akademik/nilai/matakuliah/perkrs/storeperkrs',['middleware'=>['role:puslahta'],'uses'=>'Akademik\NilaiMatakuliahController@storeperkrs','as'=>'nilaimatakuliah.storeperkrs']);

    //transkrip khs 
    $router->post('/akademik/nilai/khs',['uses'=>'Akademik\NilaiKHSController@index','as'=>'khs.index']);
    $router->get('/akademik/nilai/khs/{id}',['uses'=>'Akademik\NilaiKHSController@show','as'=>'khs.show']);
    // id == krs id
    $router->get('/akademik/nilai/khs/printpdf/{id}',['uses'=>'Akademik\NilaiKHSController@printpdf','as'=>'khs.printpdf']);
    
    //transkrip kurikulum 
    $router->post('/akademik/nilai/transkripkurikulum',['uses'=>'Akademik\TranskripKurikulumController@index','as'=>'transkripkurikulum.index']);
    $router->get('/akademik/nilai/transkripkurikulum/{id}',['uses'=>'Akademik\TranskripKurikulumController@show','as'=>'transkripkurikulum.show']);
    $router->get('/akademik/nilai/transkripkurikulum/printpdf1/{id}',['uses'=>'Akademik\TranskripKurikulumController@printpdf1','as'=>'transkripkurikulum.printpdf1']);
    $router->get('/akademik/nilai/transkripkurikulum/printpdf2/{id}',['uses'=>'Akademik\TranskripKurikulumController@printpdf2','as'=>'transkripkurikulum.printpdf2']);

    //setting - permissions
    $router->get('/system/setting/permissions',['middleware'=>['role:superadmin|akademik|pmb'],'uses'=>'System\PermissionsController@index','as'=>'permissions.index']);
    $router->post('/system/setting/permissions/store',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@store','as'=>'permissions.store']);
    $router->delete('/system/setting/permissions/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@destroy','as'=>'permissions.destroy']);

    //setting - roles
    $router->get('/system/setting/roles',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@index','as'=>'roles.index']);
    $router->post('/system/setting/roles/store',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@store','as'=>'roles.store']);
    $router->post('/system/setting/roles/storerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@storerolepermissions','as'=>'roles.storerolepermissions']);
    $router->post('/system/setting/roles/revokerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@revokerolepermissions','as'=>'users.revokerolepermissions']);
    $router->put('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@update','as'=>'roles.update']);
    $router->delete('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@destroy','as'=>'roles.destroy']);
    $router->get('/system/setting/roles/{id}/permission',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@rolepermissions','as'=>'roles.permission']);

    //setting - variables
    $router->get('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@index','as'=>'variables.index']);
    $router->get('/system/setting/variables/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@show','as'=>'variables.show']);
    $router->put('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@update','as'=>'variables.update']);
    $router->post('/system/setting/variables/clear',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@clear','as'=>'variables.clear']);

    //setting - users
    $router->get('/system/users',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@index','as'=>'users.index']);
    $router->post('/system/users/store',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@store','as'=>'users.store']);
    $router->put('/system/users/updatepassword/{id}',['uses'=>'System\UsersController@updatepassword','as'=>'users.updatepassword']);
    $router->post('/system/users/uploadfoto/{id}',['uses'=>'System\UsersController@uploadfoto','as'=>'users.uploadfoto']);
    $router->post('/system/users/resetfoto/{id}',['uses'=>'System\UsersController@resetfoto','as'=>'users.resetfoto']);
    $router->post('/system/users/syncallpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@syncallpermissions','as'=>'users.syncallpermissions']);
    $router->post('/system/users/storeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@storeuserpermissions','as'=>'users.storeuserpermissions']);
    $router->post('/system/users/revokeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@revokeuserpermissions','as'=>'users.revokeuserpermissions']);
    $router->put('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@update','as'=>'users.update']);
    $router->get('/system/users/{id}',['uses'=>'System\UsersController@show','as'=>'users.show']);
    $router->delete('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@destroy','as'=>'users.destroy']);
    $router->get('/system/users/{id}/permission',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersController@userpermissions','as'=>'users.permission']);
    $router->get('/system/users/{id}/prodi',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@usersprodi','as'=>'users.prodi']);
    $router->get('/system/users/{id}/roles',['uses'=>'System\UsersController@roles','as'=>'users.roles']);

    //setting - users keuangan
    $router->get('/system/userskeuangan',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@index','as'=>'userskeuangan.index']);
    $router->post('/system/userskeuangan/store',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@store','as'=>'userskeuangan.store']);
    $router->put('/system/userskeuangan/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@update','as'=>'userskeuangan.update']);
    $router->delete('/system/userskeuangan/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@destroy','as'=>'userskeuangan.destroy']);

    //setting - users pmb
    $router->get('/system/userspmb',['middleware'=>['role:superadmin|pmb'],'uses'=>'System\UsersPMBController@index','as'=>'userspmb.index']);
    $router->post('/system/userspmb/store',['middleware'=>['role:superadmin|pmb'],'uses'=>'System\UsersPMBController@store','as'=>'userspmb.store']);
    $router->put('/system/userspmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'System\UsersPMBController@update','as'=>'userspmb.update']);
    $router->delete('/system/userspmb/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'System\UsersPMBController@destroy','as'=>'userspmb.destroy']);

    //setting - users akademik
    $router->get('/system/usersakademik',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@index','as'=>'usersakademik.index']);
    $router->post('/system/usersakademik/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@store','as'=>'usersakademik.store']);
    $router->put('/system/usersakademik/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@update','as'=>'usersakademik.update']);
    $router->delete('/system/usersakademik/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@destroy','as'=>'usersakademik.destroy']);

    //setting - users program studi
    $router->get('/system/usersprodi',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@index','as'=>'usersprodi.index']);
    $router->post('/system/usersprodi/store',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@store','as'=>'usersprodi.store']);
    $router->put('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@update','as'=>'usersprodi.update']);
    $router->get('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@show','as'=>'usersprodi.show']);
    $router->delete('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@destroy','as'=>'usersprodi.destroy']);
    
    //setting - users puslahta
    $router->get('/system/userspuslahta',['middleware'=>['role:superadmin|puslahta'],'uses'=>'System\UsersPuslahtaController@index','as'=>'userspuslahta.index']);
    $router->post('/system/userspuslahta/store',['middleware'=>['role:superadmin|puslahta'],'uses'=>'System\UsersPuslahtaController@store','as'=>'userspuslahta.store']);
    $router->put('/system/userspuslahta/{id}',['middleware'=>['role:superadmin|puslahta'],'uses'=>'System\UsersPuslahtaController@update','as'=>'userspuslahta.update']);
    $router->get('/system/userspuslahta/{id}',['middleware'=>['role:superadmin|puslahta'],'uses'=>'System\UsersPuslahtaController@show','as'=>'userspuslahta.show']);
    $router->delete('/system/userspuslahta/{id}',['middleware'=>['role:superadmin|puslahta'],'uses'=>'System\UsersPuslahtaController@destroy','as'=>'userspuslahta.destroy']);

    //setting - users dosen
    $router->get('/system/usersdosen',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@index','as'=>'usersdosen.index']);    
    $router->post('/system/usersdosen/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@store','as'=>'usersdosen.store']);
    $router->get('/system/usersdosen/biodatadiri/{id}',['middleware'=>['role:superadmin|akademik|programstudi|dosen'],'uses'=>'System\UsersDosenController@biodatadiri','as'=>'usersdosen.biodatadiri']);
    $router->put('/system/usersdosen/biodatadiri/{id}',['middleware'=>['role:superadmin|akademik|programstudi|dosen'],'uses'=>'System\UsersDosenController@updatebiodatadiri','as'=>'usersdosen.updatebiodatadiri']);
    $router->put('/system/usersdosen/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@update','as'=>'usersdosen.update']);
    $router->delete('/system/usersdosen/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@destroy','as'=>'usersdosen.destroy']);

    //system-migration
    $router->post('/system/migration',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\SystemMigrationController@index','as'=>'systemmigration.index']);
    $router->post('/system/migration/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\SystemMigrationController@store','as'=>'systemmigration.store']);

    //untuk ui admin
    $router->get('/system/setting/uiadmin',['uses'=>'System\UIController@admin','as'=>'ui.admin']);

});
$router->group(['prefix'=>'h2h'], function () use ($router)
{
    //auth login
    $router->post('/brk/auth/login',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\AuthController@login','as'=>'brk.auth.login']);
});
$router->group(['prefix'=>'h2h','middleware'=>'auth:api'], function () use ($router)
{
    //setting - zoom api
    $router->get('/zoom',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@index','as'=>'zoom.index']);
    $router->post('/zoom/store',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@store','as'=>'zoom.store']);
    //sync ini digunakan untuk mensinkronkan data akun zoom
    $router->get('/zoom/sync/{id}',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@testing','as'=>'zoom.sync']);
    $router->get('/zoom/{id}',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@show','as'=>'zoom.show']);
    $router->put('/zoom/{id}',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@update','as'=>'zoom.update']);
    $router->delete('/zoom/{id}',['middleware'=>['role:superadmin'],'uses'=>'Plugins\H2H\ZoomAPI\ZoomController@destroy','as'=>'zoom.destroy']);

    //integrasi dengan perbangkan
    
    //[bank riau kepri]    
    //authentication
    $router->post('/brk/auth/logout',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\AuthController@logout','as'=>'brk.auth.logout']);
    $router->get('/brk/auth/refresh',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\AuthController@refresh','as'=>'brk.auth.refresh']);
    $router->get('/brk/auth/me',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\AuthController@me','as'=>'brk.auth.me']);

    //inquiry tagihan
    $router->post('/brk/inquiry-tagihan',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\TransaksiController@inquiryTagihan','as'=>'brk.transaksi.inquiry-tagihan']);
    //payment
    $router->post('/brk/payment',['uses'=>'Plugins\H2H\BankRiauKepriSyariah\TransaksiController@payment','as'=>'brk.transaksi.payment']);
});