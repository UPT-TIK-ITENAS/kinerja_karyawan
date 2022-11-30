@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row mb-2">
                            <h5>Pendataan Hari Libur </h5>
                            <span>Daftar hari libur nasional</span>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col">
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahLibur"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-libur">
                                <thead>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
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
    <div class="modal fade bd-example-modal-lg" id="show-libur" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Data Libur</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.libur-nasional.updatelibur') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggal" name="tanggal" type="date" required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="tanggal">Keterangan</span>
                                <input class="form-control" id="keterangan" name="keterangan" type="text" required="">
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">

                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="tambahLibur" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Data Libur</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.libur-nasional.createlibur') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggal" name="tanggal" type="date" required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="tanggal">Keterangan</span>
                                <input class="form-control" id="keterangan" name="keterangan" type="text" required="">
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">

                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @parent

    <script>
        $(function() {
            let table = $('#table-libur').DataTable({
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
                ajax: "{{ route('admin.libur-nasional.listlibur') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ],
            });


            $('body').on('click', '.editLibur', function() {
                var id = $(this).data('id');

                $.get(`${window.baseurl}/admin//libur-nasional/editlibur/${id}`, function(data) {
                    $('#ModalTitle').html("Pertanyaan");
                    $('#show-libur').modal('show');
                    $('#id').val(data.id);
                    $('#tanggal').val(data.tanggal);
                    $('#keterangan').val(data.keterangan);
                    console.log(data);
                })
            });

            $('#table-libur').on('click', '.hapusLibur', function(e) {
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

        });
    </script>
@endsection
