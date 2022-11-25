@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Izin</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Izin</li>
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

                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-izin">
                                <thead>
                                    <tr align="center">
                                        <th width="5%">No.</th>
                                        <th>Alasan</th>
                                        <th>Jam Awal</th>
                                        <th>Jam Akhir</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        {{-- <th>Approval</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $r)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ $r->alasan }}</td>
                                            <td align="center">{{ $r->jam_awal }}</td>
                                            <td align="center">{{ $r->jam_akhir }}</td>
                                            <td align="center">{{ $r->tanggal }}</td>
                                            <td>
                                                <div class='d-block text-center'>
                                                    <a href='#' data-toggle='tooltip'
                                                        class='btn btn btn-warning btn-xs align-items-center tambahIzin'
                                                        data-id='{{ $r->id_izin }}' title='Edit Izin'>
                                                        <i class='icofont icofont-edit-alt'></i>
                                                    </a>
                                                </div>

                                            </td>
                                            {{-- <td align="center">{{   $r->approval }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                <form class="needs-validation" novalidate="" action="{{ route('kepalaunit.approveIzinTelat') }}"
                    method="POST">
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
                                <textarea name="alasan" id="alasan" name="alasan" class="form-control" required></textarea>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="jam_awal">Tanggal Awal</span>
                                <input class="form-control" id="jam_awal" name="jam_awal" type="text" required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jam_akhir">Tanggal Akhir</span>
                                <input class="form-control" id="jam_akhir" name="jam_akhir" type="text" required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggal" name="tanggal" type="date" required="">
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
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu tidak
                            dihitung</span>
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
        });

        // $('body').on('click', '.tambahIzin', function() {
        //     var id = $(this).data('id');

        //     $.get("{{ url('/kepalaunit/approval/editIzinTelat') }}/" + id, function(data) {
        //         $('#ModalTitle').html("Pertanyaan");
        //         $('#show-mesin').modal('show');
        //         $('#id_izin').val(data.id_izin);
        //         $('#id_attendance').val(data.id_attendance);
        //         $('#name').val(data.name);
        //         $('#nopeg').val(data.nopeg);
        //         $('#alasan').val(data.alasan);
        //         $('#jam_awal').val(data.jam_awal);
        //         $('#jam_akhir').val(data.jam_akhir);
        //         $('#tanggal').val(data.tanggal);
        //         console.log(data);
        //     })
        // });

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
                $('#jam_awal').val(data.jam_awal);
                $('#jam_akhir').val(data.jam_akhir);
                $('#tanggal').val(data.tanggal);
                console.log('data :', data);
            })
        });
    </script>
@endsection
