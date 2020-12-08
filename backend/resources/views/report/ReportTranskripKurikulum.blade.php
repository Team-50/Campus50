<html>
<head>
<style>
.header{
    width:100%;
    font-weight:bold;
    text-align:center;
    border-collapse: collapse;   
    border-bottom: 2px solid black;
}
.table {		
    width:100%;
    border-collapse: collapse;   
    font-size: 12px; 
    margin-bottom: 10px;
}
.table th {
    border: 1px solid #000;	    
}
.table td {
    border: 1px solid #000;	    
}
h2{
    text-align:center;
}
</style>
</head>
<body>
@include('report.ReportHeader')
<h2>TRANSKRIP NILAI SEMESTER</h2>
<table style="font-size:14px">
    <tbody>
        <tr>
            <td><strong>Nama Mahasiswa</strong></td>
            <td>:</td>
            <td width="250">{{$mahasiswa->nama_mhs}} ({{$mahasiswa->jk}})</td>

            <td><strong>Fakultas</strong></td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>NIRM</strong></strong></td>
            <td>:</td>
            <td>{{$mahasiswa->nirm}}</td>

            <td><strong>Program Studi</strong></td>
            <td>:</td>
            <td>{{$mahasiswa->nama_prodi}}</td>
        </tr>        
    </tbody>
</table>
<table>    
    <tbody>
        @foreach ($daftar_nilai as $key=>$item)
            <tr>
                <td>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="10">NO</th>
                                <th width="250">MATAKULIAH</th>
                                <th width="70">KODE</th>
                                <th width="50">SEMESTER</th>
                                <th width="50">KELOMPOK</th>
                                <th width="50">HM</th>
                                <th width="50">AM</th>
                                <th width="50">K</th>
                                <th width="50">M</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item as $key2=>$item2)
                                @if ($item2['pid']=='body')
                                <tr>
                                    <td style="text-align:center">
                                        {{ $key2 + 1 }}    
                                    </td>  
                                    <td>                                       
                                        {{$item2['nmatkul']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['kmatkul']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['semester']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['group_alias']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['HM']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['AM']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['sks']}}
                                    </td>  
                                    <td style="text-align:center">
                                        {{$item2['M']}}
                                    </td>  
                                </tr>
                                @else
                                <tr>
                                    <td colspan="6"></td>
                                    <td style="text-align:center">{{$item2['jumlah_am_smt']}}</td>
                                    <td style="text-align:center">{{$item2['jumlah_sks_smt']}}</td>
                                    <td style="text-align:center">{{$item2['jumlah_m_smt']}}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Indeks Prestasi Semester</td>
                                    <td style="text-align:center">{{$item2['ips']}}</td>                                    
                                </tr>
                                <tr>
                                    <td colspan="2">SKS Total</td>
                                    <td style="text-align:center">{{$item2['jumlah_sks_all']}}</td>                                    
                                </tr>
                                <tr>
                                    <td colspan="2">IPK</td>
                                    <td style="text-align:center">{{$item2['ipk']}}</td>                                    
                                </tr>
                                @endif                                
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<table style="font-size:12px">
    <tbody>
        <tr>
            <td width="150"><strong>SKS Total</strong></td>           
            <td width="5">:</td>
            <td>{{$jumlah_sks}}</td>            
        </tr>        
        <tr>
            <td><strong>IPK</strong></td>
            <td>:</td>
            <td>{{$ipk}}</td>            
        </tr>        
    </tbody>
</table>
<table style="font-size:12px">
    <tbody>
        <tr>
            <td width="65%"></td>
            <td><strong>Tanjungpinang, {{$tanggal}}</strong></td>                                   
        </tr>        
        <tr>
            <td width="65%"></td>
            <td><strong>Wakil Ketua 1 Bidang Akademik</strong></td>                                   
        </tr>        
        <tr>
            <td width="65%"></td>
            <td>
                <br>
                <br>
                <br>
                <br>
                <br>
                ____________________<br>
                NIDN.:
            </td>                                   
        </tr>        
            
    </tbody>
</table>
</body>
</html>
