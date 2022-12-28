@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Izin Pehari</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Izin Perhari</li>
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
                        <div class="row">
                            <div class="dt-ext table-responsive">
                                <table class="dataTable" id="table-izin">
                                    <thead>
                                        <tr align="center">
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>Nopeg</th>
                                            <th>Alasan</th>
                                            <th>Waktu</th>
                                            <th>Jenis</th>
                                            <th>Action</th>
                                            <th>Status</th>
                                        </tr>
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
    </div>
    </div>
@endsection

@section('scripts')
    <div class="modal fade bd-example-modal-lg" id="show-mesin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('kepalaunit.approveIzinTelat') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" id="lebihHari" style="display: none;">
                            ⚠ Tidak boleh melebihi jumlah hari yang telah ditentukan.
                        </div>
                        <div class="row g-1 mb-3">
                            <div class="col-md-12">
                                <input type="hidden" id="id_izin" name="id_izin">
                                <input type="hidden" id="id_attendance" name="id_attendance">
                                <input type="hidden" id="nopeg" name="nopeg">
                                <span class="form-label" for="alasan">Alasan</span>
                                <textarea name="alasan" id="alasan" name="alasan" class="form-control" required readonly></textarea>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="jam">Waktu</span>
                                <input class="form-control" id="jam" name="jam" type="text" required=""
                                    readonly>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <p style="font-size: 14px; color: white"></p>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Setuju</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
    <script>
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
            ajax: "{{ route('kepalaunit.approvalIzinTelat') }}",
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
                    data: 'alasan',
                    name: 'alasan'
                },
                {
                    data: 'waktu',
                    name: 'waktu'
                },
                {
                    data: 'jenis',
                    name: 'jenis'
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
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'print'
            ]
        });

        $('body').on('click', '.tambahIzin', function() {
            id = $(this).data('id');
            $('#btnSubmit').prop('disabled', false);
            console.log('id :', id)
            $.get("{{ url('/kepalaunit/approval/editIzinTelat') }}/" + id, function(data, jenisizin) {
                $('#show-mesin').modal('show');
                $('#id_izin').val(data.id_izin);
                $('#id_attendance').val(data.id_attendance);
                $('#name').val(data.name);
                $('#nopeg').val(data.nopeg);
                $('#alasan').val(data.alasan);
                if (data.jam_awal != undefined && data.jam_akhir != undefined && data.tanggal !=
                    undefined) {
                    $('#jam').val(data.tanggal + ' ' + data.jam_awal + ' s/d ' + data.jam_akhir);
                } else {
                    $('#jam').val(data.tanggal_izin);
                }
                console.log('data :', data);
            })
        });
    </script>
@endsection
