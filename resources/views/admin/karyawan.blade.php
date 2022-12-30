@extends('layouts.app')
@push('styles')
    <style>
        .select2-selection__clear{
            font-size: 17px;
            margin-right: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Karyawan</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">List Data Karyawan</li>
                        <li class="breadcrumb-item active">Karyawan</li>
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
                                <a href="#" class="btn btn-primary" id="btn-create" data-bs-target="#create-karyawan" data-bs-toggle="modal"
                                    style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-kar">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>NPP</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Unit</th>
                                    <th>Aksi</th>
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
    <div class="modal fade bd-example-modal-xl" id="show-karyawan" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Show Karyawan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-lg-2 col-sm-12">
                                <span class="form-label" for="name">No Pegawai</span>
                                <input class="form-control" id="nopeg" name="nopeg" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" readonly>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <span class="form-label" for="npp">NPP</span>
                                <input class="form-control" id="npp" name="npp" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="tempat">Tempat Lahir</span>
                                <input class="form-control" id="tempat" name="tempat" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="tanggal_lahir">Tanggal Lahir</span>
                                <div class="input-group date" id="dt-date" data-target-input="nearest">
                                    <input class="form-control col-sm-12 datetimepicker-input digits" type="text"
                                        data-target="#dt-date" id="tanggal_lahir" name="tanggal_lahir" readonly>
                                    <div class="input-group-text" data-target="#dt-date" data-toggle="datetimepicker"><i
                                            class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="email">Email</span>
                                <input class="form-control" id="email" name="email" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="nohp">No HP</span>
                                <input class="form-control" id="nohp" name="nohp" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="nama_unit">Unit</span>
                                <input class="form-control" id="nama_unit" name="nama_unit" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="jabatan">Jabatan Karyawan</span>
                                <input class="form-control" id="jabatan" name="jabatan" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="atasan">Atasan</span>
                                <input class="form-control" id="atasan" name="atasan" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="atasan_langsung">Atasan Langsung</span>
                                <input class="form-control" id="atasan_langsung" name="atasan_langsung" type="text" readonly>
                            </div>
                            <div class="col-lg- col-sm-12">
                                <span class="form-label" for="status">Status</span>
                                <input class="form-control" id="status" name="status" type="text" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="masuk_kerja">Masuk Kerja</span>
                                <input class="form-control" id="masuk_kerja" name="masuk_kerja" type="text" readonly>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="create-karyawan" aria-labelledby="myLargeModalLabel" aria-modal="true"
         role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Create Karyawan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                            data-bs-original-title="" title=""></button>
                </div>
                <form id="form-create" autocomplete="off" class="needs-validation" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-lg-2 col-sm-12">
                                <span class="form-label" for="name">No Pegawai</span>
                                <input class="form-control" name="nopeg" type="text">
                                <div class="text-danger text-sm fw-bold" id="nopeg-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" name="name" type="text">
                                <div class="text-danger text-sm fw-bold" id="name-error"></div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <span class="form-label" for="npp">NPP</span>
                                <input class="form-control" name="npp" type="text">
                                <div class="text-danger text-sm fw-bold" id="npp-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="tempat">Tempat Lahir</span>
                                <input class="form-control" name="tempat" type="text">
                                <div class="text-danger text-sm fw-bold" id="tempat-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="tanggal_lahir">Tanggal Lahir</span>
                                <input class="form-control" name="tanggal_lahir" type="text">
                                <div class="text-danger text-sm fw-bold" id="tanggal_lahir-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="email">Email</span>
                                <input class="form-control" name="email" type="email">
                                <div class="text-danger text-sm fw-bold" id="email-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="nohp">No HP</span>
                                <input class="form-control" name="nohp" type="text">
                                <div class="text-danger text-sm fw-bold" id="nohp-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="unit">Unit</span>
                                <select class="form-control select2-hidden-accessible" name="unit" id="unit-create">
                                    <option></option>
                                </select>
                                <div class="text-danger text-sm fw-bold" id="unit-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="jabatan">Jabatan Karyawan</span>
                                <select class="form-control js-example-basic-single" name="jabatan">
                                    <option value="Staff" selected>Staff</option>
                                    <option value="Kepala Bagian">Kepala Bagian</option>
                                </select>
                                <div class="text-danger text-sm fw-bold" id="jabatan-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="atasan">Atasan</span>
                                <select class="form-control select2-hidden-accessible" name="atasan" id="atasan-create">
                                    <option></option>
                                </select>
                                <div class="text-danger text-sm fw-bold" id="atasan-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="atasan_langsung">Atasan Langsung</span>
                                <select class="form-control select2-hidden-accessible" name="atasan_langsung" id="atasan-langsung-create">
                                    <option></option>
                                </select>
                                <div class="text-danger text-sm fw-bold" id="atasan_langsung-error"></div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <span class="form-label" for="masuk_kerja">Masuk Kerja</span>
                                <input class="form-control" name="masuk_kerja" type="text">
                                <div class="text-danger text-sm fw-bold" id="masuk_kerja-error"></div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer justify-content-between">
                      <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @parent
    <script>
        $('#btn-create').on('click', function (){
            daterangepicker('#form-create [name="tanggal_lahir"]', drops = "auto", autoUpdate = true, autoApply = true, timePicker = false, parentEl =
                    '#create-karyawan');
            daterangepicker('#form-create [name="masuk_kerja"]', drops = "auto", autoUpdate = true, autoApply = true, timePicker = false, parentEl =
                    '#create-karyawan');
            $('#unit-create').select2({
                placeholder: 'Pilih Unit',
                allowClear: true,
                ajax: {
                    url: '{{ route('data.unit.index') }}',
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });

            $('#atasan-create').select2({
                placeholder: 'Pilih Atasan',
                allowClear: true,
                ajax: {
                    url: '{{ route('data.user.atasan') }}',
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });

            $('#atasan-langsung-create').select2({
                placeholder: 'Pilih Atasan Langsung',
                allowClear: true,
                ajax: {
                    url: '{{ route('data.user.atasan_langsung') }}',
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });


        let table = $('#table-kar').DataTable({
            fixedHeader: true,
            pageLength: 25,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.karyawan.list') }}",
                type: "GET",
            },
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
                data: 'npp',
                name: 'npp'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'nohp',
                name: 'nohp'
            },
            {
                data: 'units.nama_unit',
                name: 'units.nama_unit'
            },
            {
                data: 'action',
                name: 'action'
            },
            ],
        });

        $('#btnSubmit').on('click', function (e){
            e.preventDefault();
            // serialize form-create
            let formData = $('#form-create').serialize();
            $.ajax({
                url: "{{ route('admin.karyawan.store') }}",
                type: "POST",
                data: formData,
                success: function (response) {
                    console.log(response);
                    $('#modal-create').modal('hide');
                    table.ajax.reload();
                    $.notify({
                        title: 'Success',
                        message: response.message
                    }, {
                        type: 'primary',
                        allow_dismiss: true,
                        newest_on_top: false,
                        mouse_over: true,
                        showProgressbar: false,
                        spacing: 10,
                        timer: 1700,
                        placement: {
                            from: 'top',
                            align: 'center'
                        },
                        offset: {
                            x: 30,
                            y: 30
                        },
                        delay: 1000,
                        z_index: 10000,
                        animate: {
                            enter: 'animated bounce',
                            exit: 'animated bounce'
                        }
                    });
                },
                error: function (response) {
                    let errors = response.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                }
            });
        });


        $('body').on('click', '.show-karyawan', function() {
            const id = $(this).data('id');
            $.get(`${window.baseurl}/admin/karyawan/${id}`, function(data) {
                console.log(data);
                $('#show-karyawan').modal('show');
                $('#iduser').val(data.id);
                $('#name').val(data.name);
                $('#nopeg').val(data.nopeg);
                $('#npp').val(data.npp);
                $('#tempat').val(data.tempat);
                $('#tanggal_lahir').val(data.tanggal_lahir);
                $('#email').val(data.email);
                $('#nohp').val(data.nohp);
                $('#jabatan').val(data.jabatan);
                $('#atasan').val(data.atasan?.nama);
                $('#atasan_langsung').val(data.atasan_langsung?.nama);
                $('#masuk_kerja').val(data.masuk_kerja);
                $('#fungsi').val(data.fungsi);
                if (data.status == '1') {
                    $('#status').val('Tendik');
                } else {
                    $('#status').val('Nondik');
                }
                $('#unit').val(data.unit);
                $('#nama_unit').val(data.units?.nama_unit);
            })
        });
    </script>
@endsection
