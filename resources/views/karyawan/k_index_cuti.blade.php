@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Cuti</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Cuti</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <h6>Total Cuti yang Pernah Diajukan Pada Tahun {{ \Carbon\Carbon::now()->year }} </h6>
                            <p style="font-size: 80%;">*Akan muncul jika sudah mengajukan cuti</p>
                        </div>
                        <div class="row">
                            @foreach ($data['history'] as $r)
                                <div class="col-lg-3 col-md-3 col-sm-4">
                                    <div class="alert alert-light" role="alert">
                                        <p align="center"><b>{{ $r->jeniscuti }}</b></p>
                                        <hr>
                                        <div class="alert alert-primary" role="alert">
                                            {{-- <i class="icon-timer"></i> --}}
                                            <div class="row">
                                                <div class="col-lg-12 col-sm-12 col-md-12">
                                                    <p align="center"><b>[{{ $r->total_harinya }}hari]</b></p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <div class="row">
                            <div class="dt-ext table-responsive">
                                <table class="dataTable" id="table-cuti">
                                    <thead>
                                        <tr align="center">
                                            <th width="5%">No.</th>
                                            <th>Jenis Cuti</th>
                                            <th>Tanggal Awal</th>
                                            <th>Tanggal Akhir</th>
                                            <th>Total Hari</th>
                                            <th>Alamat</th>
                                            <th>No. Telp</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Aksi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['cuti'] as $no => $r)
                                            <tr>
                                                <td align="center">{{ $no + 1 }}</td>
                                                <td>{{ $r->nama_cuti }}</td>
                                                <td>{{ \Carbon\Carbon::parse($r->tgl_awal_cuti)->isoFormat('D MMMM Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($r->tgl_akhir_cuti)->isoFormat('D MMMM Y') }}
                                                </td>
                                                <td>{{ $r->total_cuti }}</td>
                                                <td>{{ $r->alamat }}</td>
                                                <td>{{ $r->no_hp }}</td>
                                                <td>{{ $r->tgl_pengajuan }}</td>
                                                <td align="center"><a class="btn btn-success btn-xs" title="Print Surat"
                                                        href={{ route('karyawan.print.cuti', $r->id_cuti) }}><i
                                                            class="icofont icofont-download-alt"></i></a></td>
                                                <td align="center">{!! getApproval($r->id_cuti, 'cuti', $r->alasan_tolak) !!}</td>
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
    <div class="modal fade bd-example-modal-lg" id="tambahCuti" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Form Pengajuan Cuti</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('karyawan.store_cuti') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" id="lebihHari" style="display: none;">
                            ?????? Pengajuan anda <b>DITOLAK</b> karena telah melebihi hari yang telah ditentukan.
                        </div>
                        <div class="row g-1 mb-3">
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
                                <input type="hidden" id="nopeg" value="{{ auth()->user()->nopeg }}">
                                <input type="hidden" id="lama_cuti">
                                <div class="invalid-feedback">Pilih salah satu !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="sumcuti">Total cuti yang sudah terpakai</span>
                                <input class="form-control" readonly id="sumcuti" name="sumcuti" type="text"
                                    required="">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_cuti">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_cuti" name="tgl_awal_cuti" type="text"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_akhir_cuti">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_cuti" name="tgl_akhir_cuti" type="text"
                                    required="">
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="total_cuti">Total Hari</span>
                                <input class="form-control" id="total_cuti" name="total_cuti" type="number"
                                    required="" readonly>
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
                            <p class="fw-bold">Apabila pengajuan izin/cuti pada 2 bulan yang berbeda, maka harus mengajukan
                                2(dua) kali pada form yang berbeda, agar dapat terdata setiap bulannya.</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span class="badge badge-secondary" style="font-size: 14px;">*) Hari sabtu/minggu, libur nasional
                            dan cuti bersama tidak
                            dihitung</span>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
    <script>
        let table = $('#table-cuti').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });


        document.getElementById('no_hp').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,5})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });

        // daterangepicker('#tgl_awal_cuti', drops = "auto", autoUpdate = true, autoApply = true, timePicker = false,
        // parentEl = '#tambahCuti');
        $("#tgl_awal_cuti").daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            autoUpdateInput: true,
            autoApply: true,
            locale: {
                cancelLabel: "Hapus",
                applyLabel: "Terapkan",
                format: "YYYY-MM-DD",
            },
            drops: "auto",
            parentEl: "#tambahCuti",
            minDate: moment().startOf('year').format('YYYY-MM-DD'),
            maxDate: moment().endOf('year').format('YYYY-MM-DD')
        });

        $("#tgl_awal_cuti").on('change', function(e) {
            e.preventDefault();
            let tgl_awal = $(this).val();
            $("#tgl_akhir_cuti").daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                showDropdowns: true,
                autoUpdateInput: true,
                autoApply: true,
                locale: {
                    cancelLabel: "Hapus",
                    applyLabel: "Terapkan",
                    format: "YYYY-MM-DD",
                },
                drops: "auto",
                parentEl: "#tambahCuti",
                minDate: moment(tgl_awal).format('YYYY-MM-DD'),
                maxDate: moment(tgl_awal).endOf('month').format('YYYY-MM-DD')
            });
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

        $(document).ready(function() {

            $('#jenis_cuti').on('change', function() {
                const selected = $(this).find('option:selected');
                const max_hari = selected.data('max');

                $("#lama_cuti").val(max_hari);
                let nopeg = $('#nopeg').val();
                let jenis = $('#jenis_cuti').val();

                let sumcuti = $('#sumcuti');
                $.get(window.baseurl + '/karyawan/historycuti/' + nopeg + '/' + jenis, function(res) {
                    sumcuti.val(res);
                })
                getTotalCuti();
            });
            $('#tgl_akhir_cuti').on('change', function() {
                getTotalCuti();
            });
            $('#tgl_awal_cuti').on('change', function() {
                getTotalCuti();
            });

            function getTotalCuti() {
                let tgl_awal = $('#tgl_awal_cuti').val();
                let tgl_akhir = $('#tgl_akhir_cuti').val();
                let total_cuti = $('#total_cuti');
                let sumcuti = $('#sumcuti').val();
                let lama = $('#lama_cuti').val();
                let total = 0;
                let totalll = 0;

                if (tgl_awal != '' && tgl_akhir != '') {
                    $.get(window.baseurl + '/karyawan/getWorkingDays/' + tgl_awal + '/' + tgl_akhir, function(
                        response) {
                        total = total_cuti.val(response);
                        totalll = parseInt(response) + parseInt(sumcuti);

                        if (totalll > lama) {
                            $('#lebihHari').css('display', 'block');
                            $('#btnSubmit').attr('disabled', 'true');
                        } else {
                            $('#lebihHari').css('display', 'none');
                            $('#btnSubmit').removeAttr('disabled');
                        }

                    })

                }
            }
        });
    </script>
@endsection
