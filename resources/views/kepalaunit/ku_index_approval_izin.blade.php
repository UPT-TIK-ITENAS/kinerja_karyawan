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
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-izin">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
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
    <div class="modal fade bd-example-modal-lg" id="ProsesIzin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('kepalaunit.approveIzin') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-1 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="nopeg">No Pegawai</span>
                                <input class="form-control" id="nopeg" name="nopeg" type="text" required=""
                                    disabled>
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" required=""
                                    disabled>
                            </div>
                            <div class="col-md-12">
                                <span class="form-label" for="jenis_izin">Jenis izin</span>
                                <input class="form-control" id="jenis_izin" name="jenis_izin" type="text" required=""
                                    disabled>
                            </div>
                        </div>
                        <input id="jbnopeg" name="jbnopeg" type="hidden">
                        <input type="hidden" id="id_izinkerja" name="id_izinkerja">
                        <input type="hidden" id="id_izin" name="id_izin">

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_izin">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_izin" name="tgl_awal_izin" type="date"
                                    required="" disabled>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_akhir_izin">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_izin" name="tgl_akhir_izin" type="date"
                                    required="" disabled>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="total_izin">Total Hari</span>
                                <input type="text" class="form-control" id="total_izin" name="total_izin" required=""
                                    disabled>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu, libur nasional
                            dan cuti bersama tidak
                            dihitung</span>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Setuju</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
    <script>
        $('body').on('click', '.editAK', function() {
            id = $(this).data('id');
            console.log('id :', id)
            $.get("{{ url('/kepalaunit/approval/editIzin') }}/" + id, function(data, jenisizin) {
                if (data.approval == 1) {
                    $('#btnSubmit').prop('disabled', true);
                }
                $('#ProsesIzin').modal('show');
                $('#name').val(data.name);
                $('#nopeg').val(data.nopeg);
                $('#approval').val(data.approval);
                $('#id_izinkerja').val(data.id_izinkerja);
                $('#jenis_izin').val(data.jenis_izin);
                $('#id_izin').val(data.id_izin);
                $('#tgl_awal_izin').val(data.tgl_awal_izin);
                $('#tgl_akhir_izin').val(data.tgl_akhir_izin);
                $('#total_izin').val(data.total_izin);
                console.log('data :', data);

            })
        });


        $().ready(function() {
            let table = $('#table-izin').DataTable({
                fixedHeader: true,
                pageLength: 10,
                responsive: true,
                processing: true,
                autoWidth: false,
                serverSide: true,
                searching: true,
                columnDefs: [{
                    targets: 1,
                    width: "200px !important",
                }, ],
                ajax: "{{ route('kepalaunit.approvalIzin') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'nopeg',
                        name: 'nopeg'
                    },
                    {
                        data: 'izin',
                        name: 'izin'
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
                        data: 'action',
                        name: 'action'
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
    </script>
@endsection
