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
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-kar">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>NPP</th>
                                    <th>TTL</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($peg as $no => $p)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ $p->nopeg }} - {{ $p->name }}</td>
                                            <td>{{ $p->npp }}</td>
                                            <td>{{ $p->tempat }}, {{ $p->tanggal_lahir }}</td>
                                            <td>{{ $p->email }}</td>
                                            <td>{{ $p->nohp }}</td>
                                            <td>
                                                <div class='d-block text-center'>
                                                    <a href='#' data-toggle='tooltip'
                                                        class='btn btn btn-success btn-xs align-items-center editKaryawan'
                                                        data-id='{{ $p->iduser }}' title='Edit Karyawan'>
                                                        <i class='icofont icofont-eye-alt'></i>
                                                    </a>
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
    <div class="modal fade bd-example-modal-lg" id="show-karyawan" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Mesin Sidik Jari</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="name">No Pegawai</span>
                                <input class="form-control" id="nopeg" name="nopeg" type="text" required=""
                                    readonly>
                            </div>
                            <div class="col-md-5">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" required="">
                            </div>
                            <div class="col-md-2">
                                <span class="form-label" for="npp">NPP</span>
                                <input class="form-control" id="npp" name="npp" type="text" required="">
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="tempat">Tempat Lahir</span>
                                <input class="form-control" id="tempat" name="tempat" type="text" required="">
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="tanggal_lahir">Tanggal Lahir</span>
                                <div class="input-group date" id="dt-date" data-target-input="nearest">
                                    <input class="form-control col-sm-12 datetimepicker-input digits" type="text"
                                        data-target="#dt-date" id="tanggal_lahir" name="tanggal_lahir" required>
                                    <div class="input-group-text" data-target="#dt-date" data-toggle="datetimepicker"><i
                                            class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <span class="form-label" for="email">Email</span>
                                <input class="form-control" id="email" name="email" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="nohp">No HP</span>
                                <input class="form-control" id="nohp" name="nohp" type="text"
                                    required="">
                            </div>
                            <div class="col-md-7">
                                <span class="form-label" for="nama_unit">Unit</span>
                                <input class="form-control" id="nama_unit" name="nama_unit" type="text"
                                    required="">
                            </div>
                            <div class="col-md-5">
                                <input type="hidden" id="atasan" name="atasan">
                                <span class="form-label" for="name_jab">Atasan</span>
                                <input class="form-control" id="name_jab" name="name_jab" type="text"
                                    required="">
                            </div>
                            <div class="col-md-5">
                                <input type="hidden" id="atasan_lang" name="atasan_lang">
                                <span class="form-label" for="name_jab2">Atasan Langsung</span>
                                <input class="form-control" id="name_jab2" name="name_jab2" type="text"
                                    required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jabatan">Jabatan Karyawan</span>
                                <input class="form-control" id="jabatan" name="jabatan" type="text"
                                    required="">
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="status">Status</span>
                                <select name="status" id="status" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="1">Tendik</option>
                                    <option value="0">Nondik</option>
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
        let table = $('#table-kar').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.editKaryawan', function() {
            var id = $(this).data('id');

            $.get(`${window.baseurl}/admin/karyawan/editKaryawan/${id}`, function(data) {
                $('#ModalTitle').html("Pertanyaan");
                $('#show-karyawan').modal('show');
                $('#iduser').val(data.iduser);
                $('#name').val(data.name);
                $('#nopeg').val(data.nopeg);
                $('#npp').val(data.npp);
                $('#tempat').val(data.tempat);
                $('#tanggal_lahir').val(data.tanggal_lahir);
                $('#email').val(data.email);
                $('#nohp').val(data.nohp);
                $('#jabatan').val(data.jabatan);
                $('#atasan').val(data.atasan);
                $('#atasan_lang').val(data.atasan_lang);
                $('#masuk_kerja').val(data.masuk_kerja);
                $('#fungsi').val(data.fungsi);
                $('#status').val(data.status);
                $('#unit').val(data.unit);
                $('#nama_unit').val(data.nama_unit);
                $('#name_jab').val(data.name_jab);
                $('#name_jab2').val(data.name_jab2);

                console.log(data);
            })
        });
    </script>
@endsection
