@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Master Data Presensi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sistem Laporan Presensi Karyawan</li>
                        <li class="breadcrumb-item active">Master Data Presensi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        @if (auth()->user()->role == 'admin')
                            <p><b>Sinkronisasi Mesin Sidik Jari</b> </p>
                            <form action="{{ route('admin.SyncAndInsertBiometric') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label">Tanggal</label>
                                    <div class="col-xl-2">
                                        <input type="text"
                                            class="ts-datepicker form-control @error('tanggal') is-invalid @enderror"
                                            id="tanggal" name="tanggal" value="" />
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                        <button class="btn btn-outline-success-2x" type="submit"><i
                                                class="fa fa-refresh"></i>
                                            Sinkron</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                        @endif

                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Filter</label>
                            <div class="col-xl-3">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter1" id="filter1" required="">
                                    <option selected="" disabled="" value=""> Pilih Nama </option>
                                    @foreach ($user as $u)
                                        <option value="{{ $u->nopeg }}">{{ $u->nopeg }} -
                                            {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter2" id="filter2" required="">
                                    <option selected="" disabled="" value="">Pilih Tanggal </option>
                                    @foreach ($attendance as $a)
                                        <option value="{{ $a->tanggal }}">{{ $a->tanggal }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter3" id="filter3" required="">
                                    <option selected="" disabled="" value="">Pilih Bulan </option>
                                    @foreach ($bulan as $a)
                                        <option value="{{ $a->bulan }}">{{ getNamaBulan($a->bulan) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3">
                                <button class="btn btn-outline-danger txt-red" type="button" id="clear"><i
                                        class="icofont icofont-ui-delete"></i> Clear</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col">
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahAtt" data-bs-toggle="modal"
                                    style="float: right">+ Tambah</a>
                            </div>
                        </div>

                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-admin">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Kurang Jam</th>
                                    <th>Note</th>
                                    <th>Aksi</th>
                                    @if (auth()->user()->role == 'admin_bsdm' || auth()->user()->role == 'admin')
                                        <th>Aksi BSDM</th>
                                    @endif
                                    <th>Status</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade bd-example-modal-lg" id="show-izin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" novalidate="" action="{{ route('admin.presensi.storeizinkehadiran') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="nip">No Pegawai</span>
                                <input class="form-control" id="nip" name="nip" type="text" required=""
                                    readonly>
                                <input id="id" name="id" hidden />
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" required=""
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="nama_unit">Unit</span>
                                <input class="form-control" id="nama_unit" name="nama_unit" type="text" readonly
                                    required="">
                                <input id="unit" name="unit" hidden />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggall" name="tanggall" type="text" readonly
                                    required="">

                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jenis">Jenis</span>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="1">Izin</option>
                                    <option value="2">Sidik Jari</option>
                                </select>

                            </div>
                            <div class="col-md-4 jamawal">
                                <span class="form-label" for="jam_masuk">Jam Keluar </span>
                                <div class="input-group clockpicker" data-autoclose="true">
                                    <input class="form-control" type="text" id="jam_awal" name="jam_awal"><span
                                        class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                            <div class="col-md-4 jamakhir">
                                <span class="form-label" for="jam_akhir">Jam Kembali </span>
                                <div class="input-group clockpicker" data-autoclose="true">
                                    <input class="form-control" type="text" id="jam_akhir" name="jam_akhir"><span
                                        class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                            <div class="col-md-4 jamizin">
                                <span class="form-label" for="tanggal_izin">Tanggal</span>
                                <input type="text" class="form-control" id="tanggal_izin" name="tanggal_izin"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>

                        </div>
                        <div class="col-md-7">
                            <span class="form-label" for="alasan">Alasan</span>
                            <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Alasan" required></textarea>
                            {{-- <input class="form-control" id="jam_masuk" name="jam_masuk" type="date"
                        required=""> --}}
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <div class="checkbox p-0">
                                    <div class="checkbox checkbox-dark">
                                        <input id="cb_valid" class="form-check-input" type="checkbox" required>
                                        <label class="form-check-label" for="cb_valid">Keterangan izin dilakukan oleh diri
                                            sendiri dan secara sadar sesuai dengan ketentuan yang berlaku</label>
                                    </div>
                                    <div class="invalid-feedback">Wajib di centang !</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="tambahAtt" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation my-5" novalidate="" action="{{ route('admin.presensi.storeAttendance') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-1 mb-3">
                            <div class="col-md-8">
                                <span class="form-label" for="jenis_izin">Karyawan</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="nip" name="nip" required>
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    @foreach ($user as $u)
                                        <option value="{{ $u->nopeg }}">{{ $u->nopeg }} -
                                            {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input type="text" class="form-control" id="tanggal-tambah" name="tanggal"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="jam_masuk">Jam Masuk</span>
                                <input type="text" class="form-control" id="jam_masuk" name="jam_masuk"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jam_siang">Jam Siang</span>
                                <input type="text" class="form-control" id="jam_siang" name="jam_siang"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jam_pulang">Jam Pulang</span>
                                <input type="text" class="form-control" id="jam_pulang" name="jam_pulang"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>

                            <div class="col-md-7">
                                <span class="form-label" for="status">Status</span>
                                <select name="status" id="status" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="1">Lengkap</option>
                                    <option value="0">Kurang</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="show-att" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" novalidate="" action="{{ route('admin.presensi.updateAttendance') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-1 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="nip2">No Pegawai</span>
                                <input class="form-control" id="nip2" name="nip2" type="text" required=""
                                    readonly>
                                <input id="id2" name="id2" hidden />
                            </div>
                            <div class="col-md-5">
                                <span class="form-label" for="name2">No Pegawai</span>
                                <input class="form-control" id="name2" name="name2" type="text" required=""
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jam_masuk1">Jam Masuk</span>
                                <input type="text" class="form-control" id="jam_masuk1" name="jam_masuk1"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">

                            <div class="col-md-4">
                                <span class="form-label" for="jam_siang1">Jam Siang</span>
                                <input type="text" class="form-control" id="jam_siang1" name="jam_siang1"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jam_pulang1">Jam Pulang</span>
                                <input type="text" class="form-control" id="jam_pulang1" name="jam_pulang1"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="status">Status</span>
                                <select name="status1" id="status1" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="1">Lengkap</option>
                                    <option value="0">Kurang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endpush

@section('scripts')
    @parent


    <script>
        $().ready(function() {
            let table = $('#table-admin').DataTable({
                fixedHeader: true,
                pageLength: 25,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                searching: false,
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: {
                    url: "{{ route('admin.presensi.listkaryawan') }}",
                    data: function(d) {
                        d.filter1 = $('#filter1').val() ? $('#filter1').val() : '<>';
                        d.filter2 = $('#filter2').val() ? $('#filter2').val() : '<>';
                        d.filter3 = $('#filter3').val() ? $('#filter3').val() : '<>';
                        // d.search = $('input[type="search"]').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'days',
                        name: 'days'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_siang',
                        name: 'jam_siang'
                    },
                    {
                        data: 'jam_pulang',
                        name: 'jam_pulang'
                    },
                    {
                        data: 'kurang_jam',
                        name: 'kurang_jam',
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'action_edit',
                        name: 'action_edit',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],
            });

            $("#clear").on('click', function(e) {
                e.preventDefault();
                // location.reload();
                $("#filter1").val('').trigger('change');
                $("#filter2").val('').trigger('change');
                $("#filter3").val('').trigger('change');
            });
            $("#filter1").on('change', function() {
                table.draw();
            });
            $("#filter2").on('change', function() {
                table.draw();
            });
            $("#filter3").on('change', function() {
                table.draw();
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };

            // init daterangepicker
            daterangepicker('.ts-datepicker');
            daterangepicker('.ts-datetimepicker', true);
            daterangepicker('#tanggal-tambah', "auto", false, '#tambahAtt');
            daterangepicker('#jam_masuk', "auto", true, '#tambahAtt');
            daterangepicker('#jam_siang', "auto", true, '#tambahAtt');
            daterangepicker('#jam_pulang', "auto", true, '#tambahAtt');
            daterangepicker('#tanggal_izin', "auto", true, '#show-izin');
            daterangepicker('#jam_masuk1', "auto", true, '#show-att');
            daterangepicker('#jam_siang1', "auto", true, '#show-att');
            daterangepicker('#jam_pulang1', "auto", true, '#show-att');
        });



        $('body').on('click', '.editAtt', function() {
            var id = $(this).data('id');

            $.get(`${window.baseurl}/admin/presensi/editAtt/${id}`, function(data) {
                $('#ModalTitle').html('Attendance');
                $('#show-izin').modal('show');
                $('#id').val(data.id);
                $('#nip').val(data.nip);
                $('#name').val(data.name);
                $('#unit').val(data.unit);
                $('#nama_unit').val(data.nama_unit);
                $('#tanggall').val(data.tanggal);
                $('#jam_masuk').val(data.jam_masuk);
                $('#jam_siang').val(data.jam_siang);
                $('#jam_pulang').val(data.jam_pulang);

                console.log(data);
            })
        });

        $(".jamawal").hide();
        $(".jamakhir").hide();
        $(".jamizin").hide();

        $('#jenis').on('change', function(e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            console.log(valueSelected);
            if (valueSelected == 1) {
                $(".jamawal").show();
                $(".jamakhir").show();
                $(".jamizin").hide();
            } else {
                $(".jamizin").show();
                $(".jamawal").hide();
                $(".jamakhir").hide();
            }
        });

        $('body').on('click', '.editAttendance', function() {
            var id = $(this).data('id');
            console.log(`${window.baseurl}/admin/presensi/editAtt/${id}`);
            $.get(`${window.baseurl}/admin/presensi/editAtt/${id}`, function(data) {
                $('#ModalTitle').html('Attendance');
                $('#show-att').modal('show');
                $('#id2').val(data.id);
                $('#nip2').val(data.nip);
                $('#name2').val(data.name);
                $('#unit').val(data.unit);
                $('#nama_unit').val(data.nama_unit);
                $('#jam_masuk1').val(data.jam_masuk);
                $('#jam_siang1').val(data.jam_siang);
                $('#jam_pulang1').val(data.jam_pulang);
                $('#status1').val(data.status);

                console.log(data);
            })
        });
    </script>
@endsection
