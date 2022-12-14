@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Pendataan Mesin Sidik Jari</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Daftar Mesin Sidik Jari</li>
                        <li class="breadcrumb-item active">Mesin</li>
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
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahsidik"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-sidik">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Remark</th>
                                    <th>IP Address</th>
                                    <th>Port</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($att as $no => $b)
                                        <tr>
                                            <th scope="row">{{ $no + 1 }}</th>
                                            <td>{{ $b->name }}</td>
                                            <td>{{ $b->remark }}</td>
                                            <td>{{ $b->ipaddress }}</td>
                                            <td>{{ $b->port }}</td>
                                            <td>{{ $b->status }}</td>
                                            <td>
                                                <div class='d-block text-center'>
                                                    <a href='#' data-toggle='tooltip'
                                                        class='btn btn btn-warning btn-xs align-items-center editMesin'
                                                        data-id='{{ $b->id }}' title='Edit Mesin'>
                                                        <i class='icofont icofont-edit-alt'></i>
                                                    </a>
                                                    <a href='{{ route('admin.destroymesin', $b->id) }}' title='Hapus Mesin'
                                                        class='btn btn-sm btn-danger btn-xs align-items-center hapusMesin'><i
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
    <div class="modal fade bd-example-modal-lg" id="show-mesin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Mesin Sidik Jari</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.updatemesin') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="name">Nama Mesin</span>
                                <input class="form-control" id="name" name="name" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="remark">Remark</span>
                                <input class="form-control" id="remark" name="remark" type="text" required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="ipaddress">Ip Address</span>
                                <input class="form-control" id="ipaddress" name="ipaddress" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="port">Port</span>
                                <input class="form-control" id="port" name="port" type="text" required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="status">Status</span>
                                <select name="status" id="status" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="enable">Enable</option>
                                    <option value="disable">Disable</option>
                                </select>
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

    <div class="modal fade bd-example-modal-lg" id="tambahsidik" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Tambah Mesin Sidik Jari</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.createmesin') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="name">Nama Mesin</span>
                                <input class="form-control" id="namee" name="namee" type="text"
                                    required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="remark">Remark</span>
                                <input class="form-control" id="remarkk" name="remarkk" type="text"
                                    required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="ipaddress">Ip Address</span>
                                <input class="form-control" id="ipaddresss" name="ipaddresss" type="text"
                                    required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="port">Port</span>
                                <input class="form-control" id="portt" name="portt" type="text"
                                    required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="status">Status</span>
                                <select name="statuss" id="statuss" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="enable">Enable</option>
                                    <option value="disable">Disable</option>
                                </select>
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
        let table = $('#table-sidik').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.editMesin', function() {
            var id = $(this).data('id');

            $.get("{{ route('admin.admin_v') }}" + '/editmesin/' + id, function(data) {
                $('#ModalTitle').html("Pertanyaan");
                $('#show-mesin').modal('show');
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#remark').val(data.remark);
                $('#ipaddress').val(data.ipaddress);
                $('#port').val(data.port);
                $('#status').val(data.status);
                console.log(data);
            })
        });

        $('#table-sidik').on('click', '.hapusMesin', function(e) {
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
