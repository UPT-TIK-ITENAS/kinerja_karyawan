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
                        <div class="row mb-2">
                            <div class="col">
                                <a href="#" class="btn btn-primary" data-bs-target="#tambahCuti"
                                    data-bs-toggle="modal" style="float: right">+ Tambah</a>
                            </div>
                        </div>
                        {{-- <a href="{{ route('admin.createcuti') }}" class="btn btn-primary"><i class="icofont icofont-plus-square"></i> Tambah</a> --}}
                        <div class="table-responsive">
                            <table class="dataTable" id="table-cuti">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Unit</th>
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
    <div class="modal fade bd-example-modal-lg" id="tambahCuti" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Form Pengajuan Cuti</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" novalidate="" action="{{ route('admin.storecuti') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" id="lebihHari" style="display: none;">
                            ⚠️ Tidak boleh melebihi jumlah hari yang telah ditentukan.
                        </div>
                        <div class="row g-1 mb-3">
                            <div class="col-md-12">
                                <span class="form-label" for="jenis_izin">Karyawan</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="nopeg" name="nopeg" required="">
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    @foreach ($data['user'] as $p)
                                        <option value="{{ $p->nopeg }}-{{ $p->name }}-{{ $p->unit }} ">
                                            {{ $p->nopeg }} - {{ $p->name }} - {{ $p->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-8">
                                <span class="form-label" for="jenis_cuti">Jenis Cuti</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="jenis_cuti" name="jenis_cuti" required="">
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    @foreach ($data['jeniscuti'] as $r)
                                        <option value="{{ $r->id_jeniscuti }}" data-max="{{ $r->max_hari }}">
                                            {{ $r->jenis_cuti }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="lama_cuti">
                                <div class="invalid-feedback">Pilih salah satu !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="sumcuti">Total cuti yang sudah terpakai</span>
                                <input class="form-control" id="sumcuti" name="sumcuti" type="text" required="">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_cuti">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_cuti" name="tgl_awal_cuti" type="date"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_akhir_cuti">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_cuti" name="tgl_akhir_cuti" type="date"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="total_cuti">Total Hari</span>
                                <input class="form-control" id="total_cuti" name="total_cuti" type="number"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <span class="form-label" for="alamat">Alamat</span>
                                <textarea name="alamat" id="alamat" name="alamat" class="form-control" required></textarea>
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="no_hp">No HP</span>
                                <div class="input-group">
                                    <span class="input-group-text" id="no_hp_input">+62</span>
                                    <input class="form-control" id="no_hp" name="no_hp" type="text"
                                        aria-describedby="no_hp_input" required="">
                                    <div class="invalid-feedback">Wajib Diisi !</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <div class="checkbox p-0">
                                    <div class="checkbox checkbox-dark">
                                        <input id="cb_valid" class="form-check-input" type="checkbox" required>
                                        <label class="form-check-label" for="cb_valid">Pengajuan cuti dilakukan oleh diri
                                            sendiri dan secara sadar sesuai dengan ketentuan yang berlaku</label>
                                    </div>
                                    <div class="invalid-feedback">Wajib di centang !</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu dan hari libur
                            nasional tidak
                            dihitung</span>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @parent
    <script>
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
                ajax: "{{ route('admin.listcuti') }}",
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
                        data: 'singkatan_unit',
                        name: 'singkatan_unit'
                    },
                    {
                        data: 'jenis_cuti',
                        name: 'jenis_cuti'
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
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ]
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };
        });

        $(document).ready(function() {
            $('#jenis_cuti').on('change', function() {
                const selected = $(this).find('option:selected');
                const max_hari = selected.data('max');

                $("#lama_cuti").val(max_hari);
            });
        });

        $('#jenis_cuti').on('change', function() {
            let nopeg = $('#nopeg').val().split('-')[0];
            let jenis = $('#jenis_cuti').val();

            let sumcuti = $('#sumcuti');
            $.get(window.baseurl + '/admin/historycuti/' + nopeg + '/' + jenis, function(res) {
                sumcuti.val(res);
                console.log(res);
            })
        });

        $('#tgl_akhir_cuti').on('change', function() {
            let tgl_awal = $('#tgl_awal_cuti').val();
            let tgl_akhir = $('#tgl_akhir_cuti').val();
            let total_cuti = $('#total_cuti');
            let sumcuti = $('#sumcuti').val();
            let lama = $('#lama_cuti').val();
            let total = 0;
            let totalll = 0;

            if (tgl_awal != '' && tgl_akhir != '') {
                $.get(window.baseurl + '/admin/getWorkingDays/' + tgl_awal + '/' + tgl_akhir, function(response) {
                    total = total_cuti.val(response);
                    totalll = parseInt(response) + parseInt(sumcuti);
                    console.log(totalll);

                    if (totalll > lama) {
                        $('#lebihHari').css('display', 'block');
                        $('#btnSubmit').attr('disabled', 'true');
                    } else {
                        $('#lebihHari').css('display', 'none');
                        $('#btnSubmit').removeAttr('disabled');
                    }

                })

            }
        });

        $('#table-cuti').on('click', '.batalcuti', function(e) {
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
