@extends('layouts.app')
@section('content')
    <div class="container-xl">

        <h1 class="app-page-title">Kuesioner Akademik</h1>
        @if (session('success'))
            <div class="success-session" data-flashdata="{{ session('success') }}"></div>
        @elseif (session('error'))
            <div class="error-session" data-flashdata="{{ session('error') }}"></div>
        @endif
        <div class="app-card alert shadow-sm mb-4 border-left-decoration">
            <div class="inner">
                <div class="app-card-body p-3 p-lg-4">
                    <h3 class="mb-3">Instrumen Penilaian Efektivitas Proses Pembelajaran</h3>
                    <div class="row gx-5 gy-3">
                        <div class="col-12">
                            <div>
                                Kuesioner ini menanyakan pendapat anda mengenai Pembelajaran dan Suasana Akademik selama
                                semester ini. Pengumpulan data menggunakan kuesioner ini bertujuan mengukur keefektifan
                                kegiatan belajar yang telah dilakukan. Berikan tanggapan berdasarkan pendapat sendiri
                                dan bukan pandangan/pendapat orang lain. Kami mengucapkan banyak terima kasih atas
                                partisipasinya dalam pengisian kuesioner ini.
                            </div>
                        </div>
                        <!--//col-->
                    </div>
                    <div class="row mt-1">
                        <div class="col col-md-3">
                            <label class="mt-1 fs-4 fw-bold form-control-label">
                                Pilih Semester
                            </label>
                        </div>
                        <div class="col-12 col-md-9">
                            <select class="form-select my-2" id="select-kuesioner">
                                @foreach ($kuesioner as $kue)
                                    <option value="{{ $kue->id }}">
                                        {{ substr_replace($kue->semester, '/', 4, 0) }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="mt-3 btn btn-lg app-btn-primary" id="btn-start">Mulai</button>
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
                <form class="needs-validation" novalidate="" action="{{ route('kepalaunit.storeKuesioner') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-1 mb-3">
                            <div class="col-md-12">
                                <span class="form-label" for="jenis_cuti">Jenis Cuti</span>
                                <input class="form-control" id="jenis_cuti" name="jenis_cuti" type="text" required=""
                                    disabled>
                                <input type="hidden" id="lama_cuti">
                            </div>
                        </div>

                        <input type="hidden" id="id_cuti" name="id_cuti">
                        <div class="col-md-12">
                            <span class="form-label" for="approval">Persetujuan</span>
                            <select class="form-control col-sm-12" onchange="yesnoCheck(this);" id="approval"
                                name="approval" required="">
                                <option selected="" disabled="" value="">-- Pilih ---</option>
                                <option value="2">Disetujui</option>
                                <option value="3">Ditolak</option>
                            </select>
                            <input type="hidden" id="lama_cuti">
                            <div class="invalid-feedback">Pilih salah satu !</div>
                        </div>
                        <div class="col-md-12" id="ifYes" style="display: none;">
                            <span class="form-label" for="alasan_tolak">Alasan Tolak</span>
                            <textarea name="alasan_tolak" id="alasan_tolak" name="alasan_tolak" class="form-control col-12" required=""></textarea>
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
                                <input class="form-control" id="total_cuti" name="total_cuti" type="number"
                                    required="" disabled>
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

@section('scripts')
    @parent
    <script>
        // $('body').on('click', '.editAK', function() {
        //     id = $(this).data('id');
        //     console.log(id)
        //     $('#approval').prop('disabled', false);
        //     $('#alasan_tolak').prop('disabled', false);
        //     $('#approval').val(null);
        //     $('#btnSubmit').prop('disabled', false);
        //     console.log("approval", $("#approval"))
        //     $.get("{{ url('/pejabat/approval/editCuti') }}/" + id, function(data, jeniscuti) {
        //         if (data.approval != 0) {
        //             $('#approval').prop('disabled', true);
        //             $('#alasan_tolak').prop('disabled', true);
        //         }
        //         if (data.approval == 2) {
        //             document.getElementById("ifYes").style.display = "block";
        //             $('#btnSubmit').prop('disabled', true);
        //         }
        //         if (data.approval == 1) {
        //             document.getElementById("ifYes").style.display = "none";
        //             $('#btnSubmit').prop('disabled', true);
        //         }
        //         $('#approval').val(data.approval);
        //         $('#ModalTitle').html("Edit Jenis Kegiatan");
        //         $('#ProsesCuti').modal('show');
        //         $("#token").val($("meta[name=csrf-token]").attr("content"));
        //         $('#id_cuti').val(data.id_cuti);
        //         $('#alasan_tolak').val(data.alasan_tolak);
        //         $('#jenis_cuti').val(data.jenis_cuti);
        //         $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
        //         $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
        //         $('#total_cuti').val(data.total_cuti);
        //         $('#alamat').val(data.alamat);
        //         $('#no_hp').val(data.no_hp);
        //         console.log(data);
        //         //console.log(jeniscuti);
        //     })
        // });

        $('body').on('click', '.editAK', function() {
            id = $(this).data('id');
            console.log(id)
            $('#approval').prop('disabled', false);
            $('#alasan_tolak').prop('disabled', false);
            $('#approval').val(null);
            $('#btnSubmit').prop('disabled', false);
            console.log("approval", $("#approval"))
            $.get("{{ url('/kepalaunit/approval/editCuti') }}/" + id, function(data, jeniscuti) {
                if (data.approval == 2) {
                    document.getElementById("ifYes").style.display = "block";
                    $('#btnSubmit').prop('disabled', true);
                    $('#approval').prop('disabled', true);
                    $('#alasan_tolak').prop('disabled', true);
                }
                if (data.approval == 1) {
                    document.getElementById("ifYes").style.display = "none";
                    $('#btnSubmit').prop('disabled', false);
                    $('#approval').prop('disabled', false);
                    $('#alasan_tolak').prop('disabled', false);
                }
                $('#approval').val(data.approval);
                $('#ModalTitle').html("Edit Jenis Kegiatan");
                $('#ProsesCuti').modal('show');
                $("#token").val($("meta[name=csrf-token]").attr("content"));
                $('#id_cuti').val(data.id_cuti);
                $('#alasan_tolak').val(data.alasan_tolak);
                $('#jenis_cuti').val(data.jenis_cuti);
                $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                $('#total_cuti').val(data.total_cuti);
                $('#alamat').val(data.alamat);
                $('#no_hp').val(data.no_hp);
                console.log(data);
                //console.log(jeniscuti);
            })
        });

        $().ready(function() {
            let table = $('#table-cuti').DataTable({
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
                ajax: "{{ route('kepalaunit.indexKuesioner') }}",
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
                    // {
                    //     data: 'nama_unit',
                    //     name: 'nama_unit'
                    // },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
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
                // dom: 'Bfrtip',
                // buttons: [
                //     'copy', 'csv', 'print'
                // ]
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };

        });

        function yesnoCheck(that) {
            if (that.value == "3") {
                document.getElementById("ifYes").style.display = "block";
                $('#alasan_tolak').prop('required', true);
            } else {
                document.getElementById("ifYes").style.display = "none";
                $('#alasan_tolak').prop('required', false);
            }
        }
    </script>
@endsection
