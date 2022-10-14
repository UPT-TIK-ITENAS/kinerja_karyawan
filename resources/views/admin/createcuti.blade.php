@extends('layouts.app')
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://repo.rachmat.id/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <h5>Form Cuti</h5>
        </div>
        <form action="{{ route('admin.storecuti') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">

                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="helpInputTop">No. Pegawai</label>
                            <select class="js-example-basic-single" id="nopeg" name="nopeg">
                                <option value="" disabled selected>Pilih Karyawan</option>
                                @foreach ($datauser as $p)
                                    <option value="{{ $p->nopeg }}-{{ $p->name }}-{{ $p->unit }}">
                                        {{ $p->nopeg }} - {{ $p->name }} - {{ $p->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">Jenis Cuti</label>
                            <select class="form-select js-example-basic-single" id="jenis_cuti" name="jenis_cuti">
                                <option value="" selected disabled>-- Pilih Jenis
                                    Cuti--</option>
                                @foreach ($jeniscuti as $item)
                                    <option value="{{ $item->id_jeniscuti }}-{{ $item->max_hari }}">
                                        {{ $item->jenis_cuti }} - {{ $item->max_hari }} Hari</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                        <label class="form-label" for="disabledInput">Tanggal Awal</label>
                        <input class="form-control" id="tgl_awal_cuti" name="tgl_awal_cuti" type="date" required="">
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                        <label class="form-label" for="disabledInput">Tanggal Akhir</label>
                        <input class="form-control" id="tgl_akhir_cuti" name="tgl_akhir_cuti" type="date" required="">
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">Total Lama Cuti</label>
                            <input type="text" class="form-control" id="total" name="total" readonly
                                value="" />
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="mb-1">
                            <label class="form-label" for="no_hp">No HP</label>
                            <div class="input-group">
                                <span class="input-group-text" id="no_hp_input">+62</span>
                                <input class="form-control" id="no_hp" name="no_hp" type="text"
                                    aria-describedby="no_hp_input" required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                        <div class="mb-1">
                            <label class="form-label" for="exampleFormControlTextarea1">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat" required></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <div class="form-check form-check-success">
                                <input type="checkbox" class="form-check-input" id="validasi" name="validasi"
                                    value="1">
                                <label class="form-check-label" for="colorCheck3" required>Pengajuan cuti dilakukan oleh
                                    diri sendiri dan secara sadar sesuai dengan ketentuan yang berlaku</label>
                            </div>
                        </div>
                    </div>

                </div>

                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
            <form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#nopeg').on('change', function() {
                const selected = $(this).find('option:selected');
                const name = selected.data('name');
                const unit = selected.data('unit');

                $("#name").val(name);
                $("#unit").val(unit);
            });
        });
    </script>
    <script src="{{ asset('assets/js/jquery.ui.min.js') }}"></script>
    <script>
        $('#tgl_akhir_cuti').on('change', function() {
            let tgl_awal = $('#tgl_awal_cuti').val();
            let tgl_akhir = $('#tgl_akhir_cuti').val();
            let total_cuti = $('#total_cuti');
            let total = 0;
            if (tgl_awal != '' && tgl_akhir != '') {
                let date1 = new Date(tgl_awal);
                let date2 = new Date(tgl_akhir);
                total = getBusinessDatesCount(date1, date2);
                total_cuti.val(total);
            }
        });

        function getBusinessDatesCount(startDate, endDate) {
            let count = 0;
            const curDate = new Date(startDate.getTime());
            while (curDate <= endDate) {
                const dayOfWeek = curDate.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) count++;
                curDate.setDate(curDate.getDate() + 1);
            }
            return count;
        }
    </script>
@endsection
