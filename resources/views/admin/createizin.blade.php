@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Data Izin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
          <h5>Data Izin</h5>
        </div>
        <form action="{{ route('admin.storeizin') }}" method="POST" enctype="multipart/form-data">
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

              <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                  <label class="form-label" for="disabledInput">Tanggal</label>
                  <input class="datepicker-here form-control digits" id="tanggal" name="tanggal" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en">
              </div>
              
              
              <div class="col-xl-4 col-md-6 col-12 mb-1 mb-md-0">
                  <div class="mb-1">
                      <label class="form-label"
                          for="exampleFormControlTextarea1">Alasan</label>
                      <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Alasan" required></textarea>
                  </div>
              </div>
              <div class="col-12">
                  <div class="mb-1">
                      <div class="form-check form-check-success">
                          <input type="checkbox" class="form-check-input"
                              id="validasi" name="validasi" value="1">
                          <label class="form-check-label" for="colorCheck3" required>Dengan ini saya menyatakan dengan benar bahwa saya izin</label>
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

@endsection

