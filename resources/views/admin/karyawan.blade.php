@extends('layouts.app')
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
                                <a href="#" class="btn btn-primary" data-bs-target="#tambah" data-bs-toggle="modal"
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
    <div class="modal fade bd-example-modal-lg" id="show-karyawan" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Karyawan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="name">No Pegawai</span>
                                <input class="form-control" id="nopeg" name="nopeg" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" readonly>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="npp">NPP</span>
                                <input class="form-control" id="npp" name="npp" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="tempat">Tempat Lahir</span>
                                <input class="form-control" id="tempat" name="tempat" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="tanggal_lahir">Tanggal Lahir</span>
                                <div class="input-group date" id="dt-date" data-target-input="nearest">
                                    <input class="form-control col-sm-12 datetimepicker-input digits" type="text"
                                        data-target="#dt-date" id="tanggal_lahir" name="tanggal_lahir" readonly>
                                    <div class="input-group-text" data-target="#dt-date" data-toggle="datetimepicker"><i
                                            class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="email">Email</span>
                                <input class="form-control" id="email" name="email" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="nohp">No HP</span>
                                <input class="form-control" id="nohp" name="nohp" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="nama_unit">Unit</span>
                                <input class="form-control" id="nama_unit" name="nama_unit" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="jabatan">Jabatan Karyawan</span>
                                <input class="form-control" id="jabatan" name="jabatan" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="atasan">Atasan</span>
                                <input class="form-control" id="atasan" name="atasan" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="atasan_langsung">Atasan Langsung</span>
                                <input class="form-control" id="atasan_langsung" name="atasan_langsung" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="status">Status</span>
                                <input class="form-control" id="status" name="status" type="text" readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="masuk_kerja">Masuk Kerja</span>
                                <input class="form-control" id="masuk_kerja" name="masuk_kerja" type="text" readonly>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @parent
    <script>
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
