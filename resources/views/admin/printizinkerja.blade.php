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

    .table1 {
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
            <!-- <p style="font-size: 14.5px;"><u>FORM PEMBIMBINGAN PELAKSANAAN PRAKTIK KERJA / TUGAS AKHIR (TA) *) -->
            <br>
            <p style="font-size: 20px;"> <b> Form Izin Tidak Masuk Kerja </b></p>
            {{-- <hr style="border-top:1px;color:#ffffff00;margin-top: -40px;background-color:white"> --}}
        </center>
    </div>

    <br>
    <br>
    <br>
    <div class="isi">
        <p style="font-size: 14.5px;"> Yang bertanda tangan di bawah ini :</p>

        <table class="table">
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    Nama
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:20%;font-size: 14.5px;">{{ $data->name }} </td>

            </tr>
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    Nomor Pegawai
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 14.5px;"> {{ $data->nopeg }} </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    Unit Kerja
                </td>

                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 14.5px;">{{ $data->nama_unit }}</td>
            </tr>
        </table>
        <p style="font-size: 14.5px;"> Mengajukan permohonan tidak masuk kerja selama {{ $data->total_izin }} hari</p>
        <table class="table1" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    <b> 1. Sakit </b>
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    <center> Tanggal Sakit : {!! getCheck($data->jenis_izin, $data->id_izinkerja, 'sakit') !!} </center>
                </td>
            </tr>
        </table>
        <br>
        <table class="table1">
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    a. Bila sakit sampai dengan 1 (satu) hari, harus mengisi form ini.
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    b. Bila sakit 2 s/d 14 hari, harus mengisi form ini dan melampirkan surat keterangan sakit dari
                    dokter.
                </td>

            </tr>
            <tr>
                <td style="width:10%;font-size: 14.5px;">
                    c. Bila sakit lebih dari > 14 hari, harus mengisi form ini dan dilampiri surat keterangan sakit dari
                    dokter spesialis.
                </td>

            </tr>
        </table>
        <br>

        <table class="table1" border="1" cellpadding="0" cellspacing="0">
            <thead class="text-center">
                <tr>
                    <td style="font-size: 14.5px;"colspan="4">
                        <b> 2. Izin karena kegiatan</b>
                    </td>
                </tr>
                <tr>
                    <th style="width:4%;font-size: 14.5px;">No</th>
                    <th style="font-size: 14.5px;">Alasan Izin</th>
                    <th style="font-size: 14.5px;">Lamanya Hari yang diizinkan </th>
                    <th style="font-size: 14.5px;">Periode Tanggal Izin yang diambil </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jenisizin as $no => $p)
                    <tr>
                        <td style="width:1%;font-size: 14.5px;">{{ $no + 1 }}</td>
                        <td style="font-size: 14.5px;">{{ $p->jenis_izin }} </td>
                        <td style="font-size: 14.5px;">{{ $p->lama_izin }} hari kerja</td>
                        <td style="font-size: 14.5px;"> {!! getCheck($p->id_izin, $data->id_izinkerja, 'check') !!} </td>
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
                    <td style="font-size: 14.5px;">
                        Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14.5px;">
                        <b>Yang bersangkutan,
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ url('qrcode/' . $data->qrcode_peg) }}" width="80" height="80"
                            alt="">
                        <img src="">
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14.5px;">
                        <u><b>{{ $data->name }}</u></b>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ttd2">
            <table style="float:left">
                <tr>
                    <td style="font-size: 14.5px;">
                        Mengetahui
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14.5px;">
                        <b>Pimpinan Unit Kerja
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ url('qrcode/' . $data->qrcode_kepala) }}" width="80" height="80"
                            alt="">
                        <img src="">
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14.5px;">
                        <u><b>{{ $atasan->nama }}</u></b>
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
