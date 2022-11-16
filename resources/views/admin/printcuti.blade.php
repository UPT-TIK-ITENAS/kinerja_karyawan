<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Cuti</title>
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
        margin-left: 19em;
        margin-right: 0;
        /* position:fixed; */
    }

    .ttd2 {

        bottom: 250;
        margin-left: 0.1em;
        margin-right: 5em;

        /* position:fixed; */
    }

    .ttd3 {
        position: absolute;
        left: 6%;
        margin-left: -120px;
        /* (300/2) */
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
            <p style="font-size: 20px;"> <b> FORM PENGAJUAN CUTI </b></p>
            {{-- <hr style="border-top:1px;color:#ffffff00;margin-top: -40px;background-color:white"> --}}
        </center>
    </div>

    <br>
    <br>
    <br>
    <br>
    <div class="isi">
        <table class="table1">
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Nama
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:20%;font-size: 15px;"> {{ $data->name }}</td>

            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Nomor Pegawai
                </td>
                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;"> {{ $data->nopeg }} </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Unit Kerja
                </td>

                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;">{{ $data->nama_unit }}</td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Jenis Cuti
                </td>

                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;">{{ $data->jenis_cuti }}</td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Tanggal Cuti
                </td>

                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;">{{ $data->tgl_awal_cuti }} s/d {{ $data->tgl_akhir_cuti }}</td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    Alamat & No Tlp yang dapat dihubungi selama cuti
                </td>

                <td style="width:2%;text-align: center">:</td>
                <td style="width:2%;font-size: 15px;">{{ $data->alamat }}. <b> No telpon </b> {{ $data->no_hp }}</td>
            </tr>
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
                        <b>Disetujui/Tidak Disetujui oleh,
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ url('qrcode/' . $data->qrcode_pejabat) }}" width="80" height="80"
                            alt="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>{{ $atasan_lang->nama }}</u></b>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ttd2">
            <table style="float:left">
                <tr>
                    <td>
                        <b style="color:white">D
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Diajukan oleh,
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ url('qrcode/' . $data->qrcode_peg) }}" width="80" height="80"
                            alt="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>{{ $data->name }}</u></b>
                    </td>
                </tr>
            </table>
        </div>


        <div class="ttd3">
            <table style="float:left">
                <tr>
                    <td>
                        <b style="color:white">Disetujui/Tidak Disetujui oleh,
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Disetujui/Tidak Disetujui oleh,
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ url('qrcode/' . $data->qrcode_kepala) }}" width="80" height="80"
                            alt="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>{{ $atasan->nama }}</u></b>
                    </td>
                </tr>
            </table>
        </div>


    </div>

    <footer>


    </footer>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="isi">
        <p style="font-size: 14.5px;"> Alasan tidak disetujui : </p>

        <br>
        <br>
        <br>
        <p style="font-size: 14.5px;"> <b>Keterangan : </b> </p>
        <table class="table1">
            <tr>
                <td style="width:10%;font-size: 15px;">
                    1. Jumlah hak cuti tahunan selama tahun 2022 adalah 12 hari
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    2. Jumllah hak cuti besar adalah 2 bulan per 6 tahun-an
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    3. Jumllah hak cuti melahirkan adalah 3 bulan dan berlaku untuk anak pertama sampai anak ke-tiga
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    4. Cuti Ibadah Haji diberikan selama 40 (empat puluh) hari kalender dan tidak memotong cuti tahunan
                    yang bersangkutan dapat menggunakan Cuti Besar.
                    (Cuti ibah haji hanya berlaku hanya untuk satu kali kesempatan ibadah haji, untuk ibdah haji
                    berikutnya menggunakan cuti besar)
                </td>
            </tr>
            <tr>
                <td style="width:10%;font-size: 15px;">
                    5. Cuti Umroh dapat diberikan dengan mengambil cuti tahunan (menggunakan hak cuti tahunan yang
                    bersangkutan)
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
