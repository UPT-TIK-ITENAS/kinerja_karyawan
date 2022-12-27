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
        top: -170px;
        margin-left: 1em;
        width: 50%;
        text-align: justify;
    }

    table {
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

    .table2 {
        position: relative;
        /* width: 100%; */
        max: width 100%;
        margin: auto;
        text-align: justify;
    }


    .isi {
        margin-left: 1em;
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
        top: 20cm;
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
        text-decoration: underline;
    }

    .ttd {
        bottom: 250;
        margin-left: 20em;
        margin-right: 0;
        /* position:fixed; */
    }

    .ttd2 {

        bottom: 250;
        margin-left: 15em;
        margin-right: 5em;

        /* position:fixed; */
    }



    .center {
        border: 1px solid #000000;
        text-align: center;
    }

    .page:after {
        content: counter(page);
    }
</style>

<body>
    <header class="header">
        <p style="font-size: 20px;align:left;"> INSTITUT TEKNOLOGI NASIONAL <br> JL. PHH MUSTAPA 23 BANDUNG <br>
            ------------------------------------------------</p>
    </header>
    <br>
    <div class="isi">
        <table class="table1">
            <tr>
                <td style="vertical-align:top;font-size: 16px;">Yang bertanda tangan dibawah ini:</td>
            </tr>
        </table>
        <table class="table2">
            <tr>
                <td style="vertical-align:top;width:150px;font-size: 16px;">Nama</td>
                <td style="width:100px;text-align: center">:</td>
                <td style="font-size: 16px;"> {{ $data->name }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top;width:150px;font-size: 16px;">Nomor Pegawai</td>
                <td style="width:100px;text-align: center">:</td>
                <td style="font-size: 16px;"> {{ $data->nopeg }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top;width:150px;font-size: 16px;">Unit</td>
                <td style="width:100px;text-align: center">:</td>
                <td style="font-size: 16px;"> {{ $data->unit }} </td>
            </tr>
        </table>
    </div>

    <div class="isi">
        <p style="font-size: 16px;align:left;"> Dengan ini menyatakan bahwa saya tidak hadir di tanggal
            <b>{{ $data->tanggal }}</b> pada jam <b> {{ $data->jam_awal }} </b> sampai dengan jam <b>
                {{ $data->jam_akhir }} </b> dikarenakan <b> {{ $data->alasan }} </b>
        </p>

    </div>
    <div class="isi">
        <br>
        <br>
        <br>
        <br>
        <div class="ttd">
            <table style="float:right">
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
                        @if (!empty($data->qrcode_kepala))
                            <img src="{{ url('qrcode/' . $data->qrcode_kepala) }}" width="80" height="80"
                                alt="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>{{ $atasan->nama }}</u></b>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ttd2">
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
                        @if (!empty($data->qrcode_peg))
                            <img src="{{ url('qrcode/' . $data->qrcode_peg) }}" width="80" height="80"
                                alt="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <u><b>{{ $data->name }}</u></b>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <footer>


    </footer>

</body>

</html>
