@extends('layouts.app')
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://repo.rachmat.id/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Data Cuti</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
          <h5>Data Cuti</h5>
        </div>
        <form action="{{ route('admin.storecuti') }}" method="POST" enctype="multipart/form-data">
          @csrf
      <div class="card-body">
          <div class="row">

            <div class="col-xl-2 col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="helpInputTop">No. Pegawai</label>
                    <select class="form-select digits" id="nopeg" name="nopeg">
                        <option value="" disabled selected>Pilih Karyawan</option>
                        @foreach($datauser as $p)
                            <option value="{{ $p->nopeg }}" data-name="{{ $p->name }}" data-unit="{{ $p->unit }}">{{ $p->nopeg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
              <div class="col-xl-3 col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="basicInput">Nama Karyawan</label>
                    <input type="text" class="form-control" id="name" name="name"
                        readonly value="" />
                </div>
            </div>
            
              <div class="col-xl-3 col-md-6 col-12">
                  <div class="mb-1">
                      <label class="form-label" for="disabledInput">Unit Kerja</label>
                      <input type="text" class="form-control" id="unit" name="unit"
                          readonly value="" />
                  </div>
              </div>
              <div class="col-xl-3 col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="basicInput">Jenis Cuti</label>
                    <select class="form-select" id="jenis_cuti" name="jenis_cuti">
                      <option value="" selected disabled>-- Pilih Jenis
                          Cuti--</option>
                      @foreach ($jeniscuti as $item)
                          <option value="{{ $item->jenis_cuti }}">
                              {{ $item->jenis_cuti }}</option>
                      @endforeach
                  </select>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                  <label class="form-label" for="disabledInput">Tanggal Awal</label>
                  <input class="datepicker-here form-control digits" id="startDate" name="startDate" type="text" data-language="en">
            </div>
            <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                <label class="form-label" for="disabledInput">Tanggal Akhir</label>
                <input class="datepicker-here form-control digits" id="endDate" name="endDate" type="text"  data-language="en">
            </div>
            
            <div class="col-xl-3 col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="basicInput">Total Lama Cuti</label>
                    <input type="text" class="form-control" id="total" name="total"
                        readonly value="" />
                </div>
            </div>
              <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                  <div class="mb-1">
                      <label class="form-label"
                          for="exampleFormControlTextarea1">Alamat</label>
                      <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat" required></textarea>
                  </div>
              </div>
              <div class="col-xl-3 col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="basicInput">No Telp</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"/>
                </div>
            </div>
              <div class="col-12">
                  <div class="mb-1">
                      <div class="form-check form-check-success">
                          <input type="checkbox" class="form-check-input"
                              id="validasi" name="validasi" value="1">
                          <label class="form-check-label" for="colorCheck3" required>Dengan ini saya menyatakan dengan benar bahwa saya cuti</label>
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
    function calcBusinessDays(start, end) {
            // This makes no effort to account for holidays
            // Counts end day, does not count start day

            // make copies we can normalize without changing passed in objects    
            var start = new Date(start);
            var end = new Date(end);
            
            // initial total
            var totalBusinessDays = 0;
            
            // normalize both start and end to beginning of the day
            start.setHours(0,0,0,0);
            end.setHours(0,0,0,0);
            
            var current = new Date(start);
            current.setDate(current.getDate() + 1);
            var day;
            // loop through each day, checking
            while (current <= end) {
                day = current.getDay();
                if (day >= 1 && day <= 5) {
                    ++totalBusinessDays;
                }
                current.setDate(current.getDate() + 1);
            }
            return totalBusinessDays;
        }

        $("#startDate, #endDate").datepicker();

        $("#endDate").on('change', function() {
            var total = calcBusinessDays(
                $("#startDate").datepicker("getDate"), 
                $("#endDate").datepicker("getDate")
            );

            $("#total").val(total);
            console.log($("#endDate").val());
        });
</script>

@endsection

