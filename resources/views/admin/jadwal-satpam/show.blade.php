@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Jadwal Satpam</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Jadwal Satpam</li>
                        <li class="breadcrumb-item">{{ $data->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="success-session" data-flashdata="{{ session('success') }}"></div>
    @elseif(session('danger'))
        <div class="danger-session" data-flashdata="{{ session('danger') }}"></div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.jadwal-satpam.storeByUser', $data->nopeg) }}" method="POST">
                            <div class="row mb-4">
                                @csrf
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="nip">No. Pegawai</label>
                                        <select class="js-example-basic-single @error('nip') is-invalid @enderror"
                                            id="nip" name="nip">
                                            <option value="{{ $data->nopeg }}" readonly selected>
                                                {{ $data->nopeg . ' - ' . $data->name }}
                                            </option>
                                        </select>
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="shift_awal">Shift</label>
                                        <select class="js-example-basic-single @error('shift_awal') is-invalid @enderror"
                                            id="shift_awal" name="shift_awal">
                                            <option value="pagi">Pagi (07.00 - 15.00)</option>
                                            <option value="pagi1">Pagi 1 (07.00 - 17.00)</option>
                                            <option value="siang">Siang(15.00 - 23.00)</option>
                                            <option value="malam">Malam (23.00 - 07.00)</option>
                                            <option value="off">OFF (Libur)</option>
                                        </select>
                                        @error('shift_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="tanggal_awal">Tanggal Awal</label>
                                        <input type="text"
                                            class="ts-datepicker form-control @error('tanggal_awal') is-invalid @enderror"
                                            id="tanggal_awal" name="tanggal_awal" readonly value="" />
                                        @error('tanggal_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="tanggal_akhir">Tanggal Akhir</label>
                                        <input type="text"
                                            class="ts-datepicker form-control @error('tanggal_akhir') is-invalid @enderror"
                                            id="tanggal_akhir" name="tanggal_akhir" readonly value="" />
                                        @error('tanggal_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6 col-12 d-flex align-items-end mb-1"
                                    style="padding: .375rem .75rem">
                                    <button class="btn btn-primary" type="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <ul class="list-group list-group-horizontal justify-content-center my-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Pagi
                                    <span class="badge rounded-pill"
                                        style="background: #24695c; color: transparent">{{ '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Pagi 1
                                    <span class="badge rounded-pill"
                                        style="background: #03bd9e; color: transparent">{{ '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Siang
                                    <span class="badge rounded-pill"
                                        style="background: #ba895d; color: transparent">{{ '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Malam
                                    <span class="badge rounded-pill"
                                        style="background: #000000; color: transparent">{{ '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Off
                                    <span class="badge rounded-pill"
                                        style="background: #f44336; color: transparent">{{ '0' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" id="modal-form">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Detail Jadwal
                    </h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="theme-form">
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="read_name">Nama</label>
                            <input class="form-control" id="read_name" name="read_name" type="text" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="read_nip">NIP</label>
                            <input class="form-control" id="read_nip" name="read_nip" type="text" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="read_shift_awal">Shift</label>
                            <input class="form-control" id="read_shift_awal" name="read_shift_awal" type="text"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="read_tanggal_awal">Tanggal Awal</label>
                            <input class="form-control" id="read_tanggal_awal" name="read_tanggal_awal" type="text"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="read_tanggal_akhir">Tanggal Akhir</label>
                            <input class="form-control" id="read_tanggal_akhir" name="read_tanggal_akhir" type="text"
                                readonly>
                        </div>
                        <div id="tagable">

                        </div>
                        <button class="btn btn-danger mt-4" data-id="" id="hapus">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('scripts')
    @parent
    <script>
        $().ready(function() {
            let table = $('#table-jadwal-satpam').DataTable({
                fixedHeader: true,
                pageLength: 10,
                responsive: true,
                processing: true,
                autoWidth: true,
                serverSide: true,
                ajax: "{{ route('admin.jadwal-satpam.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nopeg',
                        name: 'nopeg',
                        className: 'text-center',
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'text-center',
                    },
                    {
                        data: 'fungsi',
                        name: 'fungsi',
                        className: 'text-center',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    },
                ]
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };

            let flashdatasukses = $('.success-session').data('flashdata');
            if (flashdatasukses) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: flashdatasukses,
                    type: 'success'
                })
            }
            let flashdatadanger = $('.danger-session').data('flashdata');
            if (flashdatadanger) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: flashdatadanger,
                    type: 'error'
                })
            }

            $('.ts-datepicker').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                showDropdowns: true,
                autoUpdateInput: true,
                autoApply: true,
                locale: {
                    cancelLabel: 'Hapus',
                    applyLabel: 'Terapkan',
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            loadCalendarEvents()
        });

        const loadCalendarEvents = () => {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                dayMaxEventRows: true, // for all non-TimeGrid views
                views: {
                    timeGrid: {
                        dayMaxEventRows: 7 // adjust to 6 only for timeGridWeek/timeGridDay
                    },
                    dayGrid: {
                        dayMaxEventRows: 5
                    }
                },
                headerToolbar: {
                    center: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                weekNumbers: true,
                eventSources: [{
                    url: `{{ route('admin.jadwal-satpam.calendar.by-user', $data->nopeg) }}`,
                    method: 'GET',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                }],
                eventClick: function(info) {
                    let id = info.event.id
                    $.ajax({
                        url: "{{ url('admin/jadwal-satpam/by-id') }}/" + id,
                        type: 'GET',
                        dataType: 'JSON',
                        success: function(response) {
                            const hasCuti = response.tagable?.hasOwnProperty('id_cuti');
                            const hasIzin = response.tagable?.hasOwnProperty('id_izin');
                            const capitalize = (s) => {
                                if (typeof s !== 'string') return ''
                                return s.charAt(0).toUpperCase() + s.slice(1)
                            }
                            $("#read_name").val(response.user.name)
                            $("#read_nip").val(response.nip)
                            $("#read_shift_awal").val(capitalize(response.shift_awal))
                            $("#read_tanggal_awal").val(response.tanggal_awal)
                            $("#read_tanggal_akhir").val(response.tanggal_akhir)
                            if (hasCuti) {
                                $("#tagable").html(`
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="read_cuti">Alasan</label>
                                        <p>CUTI ${response.tagable.jeniscuti.jenis_cuti}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="read_cuti">Total Cuti</label>
                                        <p>CUTI ${response.tagable.total_cuti} Hari</p>
                                    </div>
                                `)
                                $("#myLargeModalLabel").html('Detail Jadwal (Pengganti)')
                            } else if (hasIzin) {
                                $("#tagable").html(`
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="read_cuti">Alasan</label>
                                        <p>IZIN ${response.tagable.jenisizin.jenis_izin}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="read_cuti">Total Izin</label>
                                        <p>${response.tagable.total_izin} Hari</p>
                                    </div>
                                `)
                                $("#myLargeModalLabel").html('Detail Jadwal (Pengganti)')
                            } else {
                                $("#tagable").html('')
                                $("#myLargeModalLabel").html('Detail Jadwal')
                            }
                            $("#hapus").attr("data-id", response.id)
                            var myModal = new bootstrap.Modal(document.getElementById(
                                'modal-form'))
                            myModal.show()
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: 'error',
                                type: 'error',
                                title: 'Error saat melihat data',
                                showConfirmButton: true
                            })
                        }
                    })
                }
            });
            calendar.setOption('locale', 'id');
            calendar.render();
        }

        $("#hapus").click(function(e) {
            e.preventDefault()
            let id = $(this).data("id")
            let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/jadwal-satpam/delete') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            _method: "delete",
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    type: 'success'
                                })
                                location.reload()
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: 'error',
                                type: 'error',
                                title: 'Error saat menghapus data',
                                showConfirmButton: true
                            })
                        }
                    })
                }
            })
        })
    </script>
@endsection
