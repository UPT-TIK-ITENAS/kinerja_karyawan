@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Cuti</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Pengajuan Cuti</li>
                        <li class="breadcrumb-item active">Data Cuti</li>
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
                            <table class="dataTable" id="table-cuti">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal Awal Cuti</th>
                                    <th>Tanggal Akhir Cuti</th>
                                    <th>Total Hari Cuti</th>
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
    <div class="modal fade bd-example-modal-lg" id="ProsesCuti" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Form Pengajuan Cuti</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('kepalaunit.approveCuti') }}" method="POST">
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
                            <div class="col-md-3">
                                <span class="form-label" for="jenis_cuti">Jenis Cuti</span>
                                <input class="form-control" id="jenis_cuti" name="jenis_cuti" type="text" required=""
                                    disabled>
                                <input type="hidden" id="lama_cuti">
                            </div>
                            <input type="hidden" id="id_cuti" name="id_cuti">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_cuti">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_cuti" name="tgl_awal_cuti" type="date"
                                    required="" disabled>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_akhir_cuti">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_cuti" name="tgl_akhir_cuti" type="date"
                                    required="" disabled>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="total_cuti">Total Hari</span>
                                <input class="form-control" id="total_cuti" name="total_cuti" type="number" required=""
                                    disabled>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <span class="form-label" for="alamat">Alamat</span>
                                <textarea name="alamat" id="alamat" name="alamat" class="form-control" required="" disabled></textarea>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="no_hp">No HP</span>
                                <div class="input-group">
                                    <span class="input-group-text" id="no_hp_input">+62</span>
                                    <input class="form-control" id="no_hp" name="no_hp" type="text"
                                        aria-describedby="no_hp_input" required="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-3">
                            <div class="col-md-6">
                                <span class="form-label" for="atasan">Atasan</span>
                                <input class="form-control" id="atasan" name="atasan" type="text" required=""
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="atasan_lang">Atasan Langsung</span>
                                <input class="form-control" id="atasan_lang" name="atasan_lang" type="text"
                                    required="" disabled>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <span class="form-label" for="approval">Persetujuan</span>
                            <select class="form-control col-sm-12" id="approval" name="approval" required="">
                                <option selected="" disabled="" value="">-- Pilih ---</option>
                                <option value="3">Ditolak</option>
                                <option value="1">Disetujui Atasan Langsung</option>
                            </select>
                            <input type="hidden" id="lama_cuti">
                            <div class="invalid-feedback">Pilih salah satu !</div>
                        </div>

                        <div class="col-md-12" id="ifYes" style="display: none;">
                            <span class="form-label" for="alasan_tolak">Alasan Tolak</span>
                            <textarea name="alasan_tolak" id="alasan_tolak" name="alasan_tolak" class="form-control col-12" required=""></textarea>
                        </div>


                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu dan hari libur
                            nasional tidak
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
            $.get("{{ url('/kepalaunit/approval/editCuti') }}/" + id, function({
                data,
                jabatan
            }) {
                $("#atasan").val(data.user.atasan.nama);
                $("#atasan_lang").val(data.user.atasan_langsung.nama);
                if (data.user.atasan_langsung.id == jabatan.id && data.approval == 1) {
                    $('#btnSubmit').prop('disabled', false);
                    $('#approval').prop('disabled', false);
                    let pilihan = new Option(`Disetujui Atasan dari Atasan Langsung`, 2, true, true);
                    $('#approval').append(pilihan)
                } else if (data.user.atasan.id == jabatan.id && data.approval == 0) {
                    $('#btnSubmit').prop('disabled', false);
                    $('#approval').prop('disabled', false);
                } else {
                    let pilihan = new Option(`Disetujui Atasan dari Atasan Langsung`, 2, true, true);
                    $('#approval').append(pilihan)
                    $('#btnSubmit').prop('disabled', true);
                    $('#approval').prop('disabled', true);
                }

                if (data.approval == 0) {
                    $('#approval').val(null);
                    $('#nopeg').val(data.nopeg);
                    $('#name').val(data.name);
                    $('#id_cuti').val(data.id_cuti);
                    $('#jenis_cuti').val(data.jenis_cuti);
                    $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                    $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                    $('#total_cuti').val(data.total_cuti);
                    $('#ifYes').hide();
                    $('#alasan_tolak').prop('disabled', false).val(data.alasan_tolak);
                    $('#alamat').val(data.alamat);
                    $('#no_hp').val(data.no_hp);
                }
                if (data.approval == 2) {
                    $('#nopeg').val(data.nopeg);
                    $('#name').val(data.name);
                    $('#approval').val(data.approval);
                    $('#id_cuti').val(data.id_cuti);
                    $('#jenis_cuti').val(data.jenis_cuti);
                    $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                    $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                    $('#ifYes').hide();
                    $('#total_cuti').val(data.total_cuti);
                    $('#alamat').val(data.alamat);
                    $('#no_hp').val(data.no_hp);
                }
                if (data.approval == 1) {
                    $('#nopeg').val(data.nopeg);
                    $('#name').val(data.name);
                    $('#approval').val(data.approval);
                    $('#id_cuti').val(data.id_cuti);
                    $('#jenis_cuti').val(data.jenis_cuti);
                    $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                    $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                    $('#ifYes').hide();
                    $('#total_cuti').val(data.total_cuti);
                    $('#alamat').val(data.alamat);
                    $('#no_hp').val(data.no_hp);
                }
                if (data.approval == 3) {
                    $('#nopeg').val(data.nopeg);
                    $('#name').val(data.name);
                    $('#alasan_tolak').prop('disabled', true).val(data.alasan_tolak);
                    $('#ifYes').show();
                    $('#approval').prop('disabled', true).val(data.approval);
                    $('#id_cuti').val(data.id_cuti);
                    $('#jenis_cuti').val(data.jenis_cuti);
                    $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                    $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                    $('#total_cuti').val(data.total_cuti);
                    $('#alamat').val(data.alamat);
                    $('#no_hp').val(data.no_hp);
                }
                $('#ModalTitle').html("Edit Jenis Kegiatan");
                $('#ProsesCuti').modal('show');
                $("#token").val($("meta[name=csrf-token]").attr("content"));
            })
        });

        $('#ProsesCuti').on('hidden.bs.modal', function(event) {
            $('#approval option[value="2"]').remove();
        })


        $().ready(function() {
            let table = $('#table-cuti').DataTable({
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
                ajax: "{{ route('kepalaunit.approval') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'user.nopeg',
                        name: 'user.nopeg'
                    },
                    {
                        data: 'jeniscuti.jenis_cuti',
                        name: 'jeniscuti.jenis_cuti'
                    },
                    {
                        data: 'tgl_awal_cuti',
                        name: 'tgl_awal_cuti'
                    },
                    {
                        data: 'tgl_akhir_cuti',
                        name: 'tgl_akhir_cuti'
                    },
                    {
                        data: 'total_cuti',
                        name: 'total_cuti'
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

            $("#approval").on('change', function(e) {
                e.preventDefault();
                if (e.target.value == "3") {
                    document.getElementById("ifYes").style.display = "block";
                    $('#alasan_tolak').prop('required', true);
                } else {
                    document.getElementById("ifYes").style.display = "none";
                    $('#alasan_tolak').prop('required', false);
                }
            })
        });
    </script>
@endsection
