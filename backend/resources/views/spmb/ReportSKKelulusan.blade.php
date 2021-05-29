<html>
<head>
<style>
body {
  font-family:'Times New Roman';
  font-size:14px;
}
@page {  
	footer: page-footer;
}
img {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>
</head>
<body>
@include('spmb.ReportHeaderPMB')
<br>
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
<p>
  Kepada Yth.<br>
  <strong>Sdr./i {{strtoupper($formulir->nama_mhs)}}</strong><br>
  DI – <br>
  &nbsp;&nbsp;&nbsp;<u>Tempat</u>
</p>
<p style="text-align: justify;text-justify: inter-word;">
Puji syukur kita panjatkan kehadirat 
</p>
<p style="text-align: justify;text-justify: inter-word;">
Menindak lanjuti hasil TES Kompetensi pada tanggal {{$tanggal_lulus}} maka dengan ini kami sampaikan bahwa saudara/i telah LULUS tes sebagai mahasiswa STT Indonesia Tanjungpinang tahun akademik {{$formulir->ta}}/{{$formulir->ta+1}}.
</p>
<p style="text-align: justify;text-justify: inter-word;">
Sebagai kelengkapan proses penerimaan mahasiswa baru, kami harapkan kehadiran Saudara/i untuk melakukan pendaftaran ulang pada kampus STMIK BANDUNG BALI segera setelah pelaksanaan TES Kompetensi Calon Mahasiswa selesai dilaksanakan paling lambat tanggal {{$next_day}} dengan melengkapi beberapa persyaratan sebagai berikut :
</p>
<ol>
  <li>
    Membayar Biaya dengan Rincian Biaya sebagai berikut :
    <ul>
      <li>Uang SPP</li>
      
      
    </ul>  
  </li>
  <li>
    Metode Pembayaran :
    <ul>
      <li>Transfer</li>      
    </ul>  
  </li>  
</ol>
<p>Demikian surat pemberitahuan ini, atas perhatian Saudara/i, kami sampaikan terima kasih.</p>
<p>
  Tanjungpinang, {{$tanggal_lulus}}<br>
  Kepala Bagian Marketing,<br>
  <img src="{{$sign_qrcode}}" height="150"><br>
  <u>{{$signature->nama}}</u><br>  
  NIPY. {{$signature->nik}}
</p>
<htmlpagefooter name="page-footer">
  <hr>
  Jl. Pompa Air No.28 <br/>
  Email: info@sttindonesia.ac.id <br/>
  Web: https://www.sttindonesia.ac.id
</htmlpagefooter>
</body>
</html>
