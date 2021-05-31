<html>
<head>
<style>
body {
  font-family:'Times New Roman';
  font-size:14px;
}
table.t1 {
  margin-left:30px;
}
table.t2 {
  margin-left:30px;
}
img {
  display:block;
  margin-top:5px;
  margin-bottom:5px;
  margin-left:auto;
  margin-right:auto;
}
@page {  
	footer: page-footer;
}
</style>
</head>
<body>
@include('spmb.ReportHeaderPMB')
<br>
<table>
  <tr>
    <td width="100">Nomor</td>
    <td>:</td>
    <td>{{$nomor_surat}}</td>
  </tr>
  <tr>
    <td>Lampiran</td>
    <td>:</td>
    <td>-</td>
  </tr>
  <tr>
    <td>Hal</td>
    <td>:</td>
    <td><strong>Surat Keterangan Lulus Tes Calon Mahasiswa</strong></td>
  </tr>
</table>
<p style="text-align: justify;text-justify: inter-word;text-indent:50px;">
  Panitia Seleksi Penerimaan Mahasiswa Baru Sekolah Tinggi Teknologi Indonesia Tanjungpinang T.A. {{$formulir->ta}}/{{$formulir->ta+1}}, setelah memperhatikan hasil Ujian Sdr/i dalam Seleksi Penerimaan Mahasiswa Baru yang dilaksanakan pada tanggal {{$tanggal_lulus}} atas nama calon mahasiswa:
</p>
<table class="t1">
  <tr>
    <td width="100">Nama</td>
    <td>:</td>
    <td><b>{{$formulir->nama_mhs}}</b></td>
  </tr>
  <tr>
    <td>No. Pendaftaran</td>
    <td>:</td>
    <td><b>{{$formulir->no_formulir}}</b></td>
  </tr>
</table>
<p style="text-align: justify;text-justify: inter-word;">
Dengan ini Sdr/i dinyatakan  untuk pilihan jurusan, <b>{{$formulir->nama_prodi}}</b>.<br>
Dengan ketentuan bahwa pendaftaran sebagai Calon Mahasiswa Baru STT Indonesia T.A. {{$formulir->ta}}/{{$formulir->ta+1}}, Sdr/i diwajibkan melakukan <b>registrasi paling lambat</b>:
</p>
<table class="t2">
  <tr>
    <td width="100">Hari/Tanggal</td>
    <td>:</td>
    <td><b>{{$next_day}}</b></td>
  </tr>
  <tr>
    <td>Tempat</td>
    <td>:</td>
    <td><b>Kampus STT Indonesia Tanjungpinang, Jl. Pompa Air No.28 - Tanjungpinang</b></td>
  </tr>
  <tr>
    <td>Jam</td>
    <td>:</td>
    <td><b>09:00 s.d. 16:00 WIB</b></td>
  </tr>
</table>

<p style="text-align: justify;text-justify: inter-word;text-indent:50px;">
  Pembayaran Registrasi dapat dilakukan langsung ke Bank BRI pada setiap jam kerja dan jika sampai dengan batas waktu tersebut diatas Sdr/i <b>tidak melakukan registrasi</b>, maka hak Sdr/i sebagai Calon Mahasiswa Baru Sekolah Tinggi Teknologi Indonesia Tanjungpinang T.A {{$formulir->ta}}/{{$formulir->ta+1}} di anggap mengundurkan diri. Dan apabila Sdr/i ingin melakukan pendaftaran, Sdr/I masih diberikan kesempatan untuk melakukan pendaftaran pada gelombang berikutnya.
</p>
<p style="text-align: justify;text-justify: inter-word;text-indent:50px;">Demikian Surat Keputusan ini disampaikan untuk dapat dipergunakan sebagaimana mestinya, Terima kasih.</p>
<br>
<p style="text-align: right;">
  Tanjungpinang, {{$tanggal_lulus}}<br>
  Kepala Bagian Marketing,<br>
  <img src="{{$sign_qrcode}}" height="80"><br>
  <u>{{$signature->nama}}</u><br>
  NIPY. {{$signature->nik}}
</p>
<htmlpagefooter name="page-footer">
  <hr>
  <br>
  Jl. Pompa Air No.28 <br/>
  Email: info@sttindonesia.ac.id <br/>
  Web: https://www.sttindonesia.ac.id
</htmlpagefooter>
</body>
</html>
