@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Izin</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Pengajuan Izin</li>
                        <li class="breadcrumb-item active">Data Izin</li>
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
                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col">
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahIzin"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>

                        {{-- <a href="{{ route('admin.createizin') }}" class="btn btn-primary"><i class="icofont icofont-plus-square"></i> Tambah</a> --}}
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-izin">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Unit</th>
                                    <th>Jenis Izin</th>
                                    <th>Tanggal Awal Izin</th>
                                    <th>Tanggal Akhir Izin</th>
                                    <th>Total Hari Izin</th>
                                    <th>Aksi</th>
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

@section('scripts')
    <div class="modal fade bd-example-modal-lg" id="tambahIzin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" novalidate="" action="{{ route('admin.izin-resmi.storeizin') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" id="lebihHari" style="display: none;">
                            ⚠️ Tidak boleh melebihi jumlah hari yang telah ditentukan.
                        </div>

                        <div class="row g-1 mb-3">
                            <div class="col-md-12">
                                <span class="form-label" for="jenis_izin">Karyawan</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="nopeg" name="nopeg" required="">
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    @foreach ($data['user'] as $p)
                                        <option value="{{ $p->nopeg }}-{{ $p->name }}-{{ $p->unit }}"
                                            data-atasan="{{ $p->peg_jab }}" data-name_jab="{{ $p->name_jab }}">
                                            {{ $p->nopeg }} - {{ $p->name }} - {{ $p->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input id="atasan" name="atasan" type="hidden">
                        <input id="name_jab" name="name_jab" type="hidden">
                        <div class="row g-1 mb-3">
                            <div class="col-md-12">
                                <span class="form-label" for="jenis_izin">Jenis izin</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="jenis_izin" name="jenis_izin" required="">
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    @foreach ($data['jenisizin'] as $r)
                                        <option value="{{ $r->id_izin }}|{{ $r->lama_izin }}">
                                            {{ $r->lama_izin ? '[' . $r->lama_izin . ' Hari] ' : '' }}
                                            {{ $r->jenis_izin }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Pilih salah satu !</div>
                                <input type="hidden" id="lama_izin">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_izin">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_izin" name="tgl_awal_izin" type="text"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_akhir_izin">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_izin" name="tgl_akhir_izin" type="text"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="total_izin">Total Hari</span>
                                <input class="form-control" id="total_izin" name="total_izin" type="number"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <div class="checkbox p-0">
                                    <div class="checkbox checkbox-dark">
                                        <input id="cb_valid" class="form-check-input" type="checkbox" required>
                                        <label class="form-check-label" for="cb_valid">Pengajuan izin dilakukan oleh diri
                                            sendiri dan secara sadar sesuai dengan ketentuan yang berlaku</label>
                                    </div>
                                    <div class="invalid-feedback">Wajib di centang !</div>
                                </div>
                            </div>
                            <p class="fw-bold">Apabila pengajuan izin/cuti pada 2 bulan yang berbeda, maka harus mengajukan
                                2(dua) kali pada form yang berbeda, agar dapat terdata setiap bulannya.</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu, libur nasional
                            dan cuti bersama tidak
                            dihitung</span>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @parent

    <script>
        $().ready(function() {
            let table = $('#table-izin').DataTable({
                fixedHeader: true,
                pageLength: 10,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: "{{ route('admin.izin-resmi.listizin') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nopeg',
                        name: 'nopeg'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'singkatan_unit',
                        name: 'singkatan_unit'
                    },
                    {
                        data: 'jenis_izin',
                        name: 'jenis_izin'
                    },
                    {
                        data: 'tgl_awal_izin',
                        name: 'tgl_awal_izin'
                    },
                    {
                        data: 'tgl_akhir_izin',
                        name: 'tgl_akhir_izin'
                    },
                    {
                        data: 'total_izin',
                        name: 'total_izin'
                    },
                    {
                        data: 'print',
                        name: 'print'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],

            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };
        });



        daterangepicker('#tgl_awal_izin', 'auto', false, '#tambahIzin');
        daterangepicker('#tgl_akhir_izin', 'auto', false, '#tambahIzin');

        $("#tgl_awal_izin").on('change', function(e) {
            e.preventDefault();
            let tgl_awal = $(this).val();
            $("#tgl_akhir_izin").daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                showDropdowns: true,
                autoUpdateInput: true,
                autoApply: true,
                locale: {
                    cancelLabel: "Hapus",
                    applyLabel: "Terapkan",
                    format: "YYYY-MM-DD",
                },
                drops: "auto",
                parentEl: "#tambahIzin",
                minDate: moment(tgl_awal).format('YYYY-MM-DD'),
                maxDate: moment(tgl_awal).endOf('month').format('YYYY-MM-DD')
            });
        });

        $('#table-izin').on('click', '.batalizin', function(e) {
            let id = $(this).data('id');
            const href = $(this).attr('href');

            e.preventDefault()
            Swal.fire({
                title: 'Apakah Yakin?',
                text: `Apakah Anda yakin ingin menghapus?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.value == true) {
                    document.location.href = href;
                }
            })
        })

        daterangepicker('#tgl_awal_izin', 'auto', false, '#tambahIzin');
        $("#tgl_awal_izin").on('change', function(e) {
            e.preventDefault();
            let tgl_awal = $(this).val();
            $("#tgl_akhir_izin").daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                showDropdowns: true,
                autoUpdateInput: true,
                autoApply: true,
                locale: {
                    cancelLabel: "Hapus",
                    applyLabel: "Terapkan",
                    format: "YYYY-MM-DD",
                },
                drops: "auto",
                parentEl: "#tambahIzin",
                minDate: moment(tgl_awal).format('YYYY-MM-DD'),
                maxDate: moment(tgl_awal).endOf('month').format('YYYY-MM-DD')
            });
        });


        $('#tgl_akhir_izin').on('change', function() {
            let tgl_awal = $('#tgl_awal_izin').val();
            let tgl_akhir = $('#tgl_akhir_izin').val();
            let total_izin = $('#total_izin');
            let lama_izin = $('#lama_izin').val();
            let total = 0;
            if (tgl_awal != '' && tgl_akhir != '') {

                $.get(window.baseurl + '/admin/getWorkingDays/' + tgl_awal + '/' + tgl_akhir, function(response) {
                    total = total_izin.val(response);
                    console.log(response);
                    console.log(lama_izin);
                    if (lama_izin == 0) {
                        $('#lebihHari').css('display', 'none');
                        $('#btnSubmit').removeAttr('disabled');
                    } else if (lama_izin != 0) {
                        if (response > lama_izin) {
                            $('#lebihHari').css('display', 'block');
                            $('#btnSubmit').attr('disabled', 'true');
                        } else {
                            $('#lebihHari').css('display', 'none');
                            $('#btnSubmit').removeAttr('disabled');
                        }
                    }
                })
            }
        });

        $('#jenis_izin').on('change', function() {
            let jenis_izin = $('#jenis_izin');
            let lama_izin = $('#lama_izin');
            let durasi_izin = jenis_izin.val().split('|')[1] ? jenis_izin.val().split('|')[1] : 100;
            if (durasi_izin == 100) {
                lama_izin.val(0);
            } else {
                lama_izin.val(durasi_izin);
            }
        });

        $(document).ready(function() {
            $('#nopeg').on('change', function() {
                const selected = $(this).find('option:selected');
                $("#atasan").val(selected.data('atasan'));
                $("#name_jab").val(selected.data('name_jab'));
            });
        });
    </script>
@endsection
