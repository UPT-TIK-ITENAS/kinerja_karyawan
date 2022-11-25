@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Periode Kuesioner Kinerja Pegawai</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Daftar Periode Kuesioner Kinerja</li>
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
                                <a href="#" class="btn btn-primary" data-bs-target="#tambah-pertanyaan"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="dataTable" id="table-pertanyaan">
                                <thead>
                                    <th>No.</th>
                                    <th>Judul</th>
                                    <th>Keterangan</th>
                                    <th>Semester</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($kuesioner as $no => $p)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ $p->judul }} </td>
                                            <td>{{ $p->keterangan }}</td>
                                            <td>{{ $p->semester }}</td>
                                            <td>
                                                <div class='d-block text-center'>
                                                    <a href='#' data-toggle='tooltip'
                                                        class='btn btn btn-warning btn-xs align-items-center editPertanyaan'
                                                        data-id='{{ $p->id }}' title='Edit Periode'>
                                                        <i class='icofont icofont-edit-alt'></i>
                                                    </a>
                                                    <a href='{{ route('admin.destroyPeriode', $p->id) }}'
                                                        title='Hapus Periode'
                                                        class='btn btn-sm btn-danger btn-xs align-items-center hapusPertanyaan'><i
                                                            class='icofont icofont-trash'></i></a>

                                                </div>

                                            </td>
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
@endsection

@section('scripts')
    <div class="modal fade bd-example-modal-lg" id="show-pertanyaan" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Periode Pertanyaan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.updatePeriode') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="judul">Judul</span>
                                <input class="form-control" id="judul" name="judul" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="keterangan">Keterangan</span>
                                <input class="form-control" id="keterangan" name="keterangan" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="semester">Semester</span>
                                <input class="form-control" id="semester" name="semester" type="text" required="">
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

    <div class="modal fade bd-example-modal-lg" id="tambah-pertanyaan" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Periode Pertanyaan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.createPeriode') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="judul">Judul</span>
                                <input class="form-control" id="judull" name="judull" type="text"
                                    required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="keterangan">Keterangan</span>
                                <input class="form-control" id="keterangann" name="keterangann" type="text"
                                    required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="semester">Semester</span>
                                <input class="form-control" id="semesterr" name="semesterr" type="text"
                                    required="">
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
        let table = $('#table-pertanyaan').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.editPertanyaan', function() {
            var id = $(this).data('id');

            $.get("{{ route('admin.admin_v') }}" + '/editPeriode/' + id, function(data) {
                $('#ModalTitle').html("Pertanyaan");
                $('#show-pertanyaan').modal('show');
                $('#id').val(data.id);
                $('#judul').val(data.judul);
                $('#keterangan').val(data.keterangan);
                $('#semester').val(data.semester);
                console.log(data);
            })
        });

        $('#table-pertanyaan').on('click', '.hapusPertanyaan', function(e) {
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
    </script>
@endsection
