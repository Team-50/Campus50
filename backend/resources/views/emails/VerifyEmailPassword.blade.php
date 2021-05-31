<div style="font-family: Arial, Helvetica, sans-serif;">
    <table>
        <tr>
            <td style="width: 20%;">
                <img src="http://api.sttindonesia.ac.id/storage/images/logostti.png" style="width:85%; height: auto;">
            </td>
            <td style="font-size: 24pt;padding-left: 18px;"><b><p>{{$nama_pt}}</p>
            </td>
        </tr>
    </table>
    <h2 style="text-align:center; background-color: #FFB300; padding-block: 20px; ">Verifikasi Alamat Email</h2>
    <table>
        <tr>
            <td>
                <p>Hi, {{$name}}</p>
                <p>Terima kasih atas pendaftaran Anda sebagai peserta ujian, untuk menyelesaikan proses yang telah dilalui kami ingin memastikan bahwa email ini adalah milik Anda. Untuk memverifikasi email gunakan kode keamanan berikut.</p>
            <p>
                <h2 style="text-align:center; background-color:#FFB300; padding-block: 5px; padding-inline: 5px; font-size: 28px;">{{$code}}</h2>
            </p>
            <p>
                Untuk langkah selanjutnya, yang harus dilakukan adalah :
                <ol type="1">
                    <li>Login di situs <a href="https://campus50.sttindonesia.ac.id/login">ini</a> menggunakan username dan password tersebut di atas</li>
                    <li>Isi data sesuai petunjuk</li>
                    <li>Lakukan pembayaran dengan menunjukkan No Billing (Bank) Anda</li>
                    <li>Upload berkas pendaftaran</li>
                    <li>Cetak Kartu Tanda Peserta secara mandiri</li>
                </ol>
            </p>
            <p>Lengkapilah data pribadi Anda sesuai dengan formulir yang telah kami sediakan, lalu silahkan Anda melakukan pendaftaran calon peserta ujian STT Indonesia Tanjungpinang tahun akademik {{$ta}}.<br>
            Jika Anda merasa tidak pernah mendaftar, silahkan abaikan email ini.</p>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                <p>
                    Salam,<br>
                    Divisi Humas dan Marketing<br>
                    {{ucwords($nama_pt)}}<br>
                    Alamat. Jl. Pompa Air No.28, Km. 2.5, Tanjungpinang - Kepulauan Riau 29122 <br>
                    Telp. (0771) 317780, 0811 7002638 <br>
                    Website. <a href="https://sttindonesia.ac.id">www.sttindonesia.ac.id</a> <br>
                    Email. info@sttindonesia.ac.id
                </p>
            </td>
        </tr>
    </table>
</div>