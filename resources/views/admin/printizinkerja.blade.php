<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Izin</title>
    <link rel="icon" type="image/png" href="{{ url('templates/img/logo.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script> --}}
</head>
<style>
    @page {
        margin: 220px 3em 3em 3em;
    }

    header {
        position: fixed;
        top: -200px;
        left: 0;
        right: 0;
        height: 200px;
        padding: 0px;
        margin: 0px;
        z-index: 900;
        vertical-align: bottom;
    }

    .table {
        position: relative;
        width: 80%;
        max: width 100%;
        margin: auto;
    }

    .table1{
        position: relative;
        width: 100%;
        max: width 100%;
        margin: auto;
    }


    .isi {
        margin-left: 2em;
        margin-right: 1em;
        margin-bottom: 1em;
        margin-top: -6em;
    }

    .container {
        display: flex;
        align-items: flex-start;
    }

    .p-al {
        font-size: 11px;
        line-height: 0.2;
    }

    .p-itenas {
        font-size: 25px;
        font-family: Georgia, 'Times New Roman', Times, serif;
        line-height: 0.2;
    }

    .isisurat {
        text-align: justify;
    }

    footer {
        position: fixed;
        top: 21cm;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
    }

    .tembusan {
        bottom: 110;
        margin-left: 3em;
        /* position:fixed; */
    }

    .over {
        text-decoration: overline;
    }

    .ttd {
        bottom: 250;
        margin-left: 23em;
        margin-right: 0;
        /* position:fixed; */
    }

    .ttd2 {

        bottom: 250;
        margin-left: 0.1em;
        margin-right: 5em;

        /* position:fixed; */
    }


    .paddingttd {

        padding-bottom: 10px;
    }

    .page:after {
        content: counter(page);
    }

</style>

<body>
    <header class="header">
        <table style="width: 100%">
            <tr>
                <td style="width:10%;">
                    <img src="{{ url('assets/images/logo-itenas.png') }}" width="120" height="110">
                </td>
                <td class="header">
                    <p align="center"><b>YAYASAN PENDIDIKAN DAYANG SUMBI</b></p>
                    <p align="center" class="p-itenas"><b>INSTITUT TEKNOLOGI NASIONAL</b>
                    <p align="center" class="p-al">Jl. PKH. Hasan Mustafa No. 23 Bandung 40124
                        Indonesia,Telepon:+62-22-7272215 ext 181; Fax: +62-22-7202892</p>
                    <p align="center" class="p-al">Website: <font color="blue">http://www.itenas.ac.id</font>;
                        Email : <font color="blue">baa@itenas.ac.id</font>
                    </p>
                </td>
            </tr>
        </table>
        <hr style="border-top: 1px solid black;margin-top: -10px">
    </header>

    <div class="isi">
        <center>
            <!-- <p style="font-size: 14px;"><u>FORM PEMBIMBINGAN PELAKSANAAN PRAKTIK KERJA / TUGAS AKHIR (TA) *) -->
            <br>
            <p style="font-size: 24px;"> <b> Form Izin Tidak Masuk Kerja </b></p>
            {{-- <hr style="border-top:1px;color:#ffffff00;margin-top: -40px;background-color:white"> --}}
        </center>
    </div>

    <br>
    <br>
    <br>
    <div class="isi">
        <p style="font-size: 14x;"> Yang bertanda tangan di bawah ini :</p>

        <table class="table">
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Nama
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:20%;font-size: 15px;"> </td>

            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Nomor Pegawai
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;"> </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Unit Kerja
                </td>
                
                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;"></td>
            </tr>
        </table>
        <p style="font-size: 14x;"> Mengajukan permohonan tidak masuk kerja selama ............</p>
        <table class="table1" border="1">
            <tr>
                <td style="width:10%;font-size: 15px;">
                    <b> 1. Sakit </b>
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                   <center> Tanggal Sakit : {!! getCheck($data->jenis_izin, $data->id_izinkerja,'sakit') !!} </center>
                </td>
            </tr>
        </table>
        <br>
        <table class="table1">
            <tr>
                <td style="width:10%;font-size: 15px;">
                    a.  Bila sakit sampai dengan 1 (satu) hari, harus mengisi form ini.
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    b.  Bila sakit 2 s/d 14 hari, harus mengisi form ini dan melampirkan surat keterangan sakit dari dokter.
                </td>
               
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    c.  Bila sakit lebih dari > 14 hari, harus mengisi form ini dan dilampiri surat keterangan sakit dari dokter spesialis.
                </td>
             
            </tr>
        </table>

         <table class="table1" border="1">
            <thead class="text-center">
                <tr>
                    <td style="width:10%;font-size: 15px;"colspan="4">
                        2. Izin karena kegiatan
                    </td>
                </tr>
                <tr>
                    <td style="width:1%;font-size: 15px;">No</td>
                    <td style="width:2%;font-size: 15px;">Alasan Izin</td>
                    <td style="width:2%;font-size: 15px;">Lamanya Hari yang diizinkan </td>
                    <td style="width:2%;font-size: 15px;">Periode Tanggal Izin yang diambil </td>
            </tr>
            </thead>
            <tbody>
                @foreach ($jenisizin as $no => $p)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td>{{ $p->jenis_izin }}</td>
                        <td>{{ $p->lama_izin }}</td>
                        <td> {!! getCheck($p->jenis_izin, $data->id_izinkerja,'check') !!} </td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>

    </div>
    <div class="isi">
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="ttd">
            <table style="float:right">
                <tr>
                    <td>
                        Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Yang bersangkutan, 
                    </td>
                </tr>
                <tr>
                    <td>
                        {{-- <img src="data:image/png;base64, {!! $qrcode !!}"> --}}
                        <img src="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>Epih Haryanti, A.Md.</u></b>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ttd2">
            <table style="float:left">
                <tr>
                    <td>
                        Mengetahui
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Pimpinan Unit Kerja 
                    </td>
                </tr>
                <tr>
                    <td>
                        {{-- <img src="data:image/png;base64, {!! $qrcode !!}"> --}}
                        <img src="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>Epih Haryanti, A.Md.</u></b>
                    </td>
                </tr>
            </table>
        </div>
    
        {{-- <div class="ttd2">
            <table style="float:right">
                <tr>
                    <td>
                        Menyetujui,
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Pimpinan Unit Kerja
                    </td>
                </tr>
                <tr>
                    <td>
                        
                        <img src="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b></u></b>
                    </td>
                </tr>
            </table>
        </div> --}}
    </div>
    <footer>
        

    </footer>
</body>

</html>
