@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Jadwal Satpam</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Jadwal Satpam</li>
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
                        <div class="dt-ext table-responsive my-5">
                            <table class="dataTable" id="table-jadwal-satpam">
                                <thead>
                                    <th>No.</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Fungsi</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5">
                            <h3>Jadwal Satpam</h3>
                            <ul class="list-group list-group-horizontal justify-content-center my-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Pagi
                                    <span class="badge rounded-pill"
                                        style="background: #24695c; color: transparent">{{ $jadwalSatpamCount['pagi']->count ?? '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Pagi 1
                                    <span class="badge rounded-pill"
                                        style="background: #03bd9e; color: transparent">{{ $jadwalSatpamCount['pagi1']->count ?? '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Siang
                                    <span class="badge rounded-pill"
                                        style="background: #ba895d; color: transparent">{{ $jadwalSatpamCount['siang']->count ?? '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Malam
                                    <span class="badge rounded-pill"
                                        style="background: #000000; color: transparent">{{ $jadwalSatpamCount['malam']->count ?? '0' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    Off
                                    <span class="badge rounded-pill"
                                        style="background: #f44336; color: transparent">{{ $jadwalSatpamCount['off']->count ?? '0' }}</span>
                                </li>
                            </ul>
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('modal')
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="detail-jadwal"
        aria-hidden="true" id="modal-detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-jadwal">
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
                            <input class="form-control" id="read_shift_awal" name="read_shift_awal" type="text" readonly>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" id="modal-form">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Form Tambah Jadwal
                    </h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="theme-form" action="{{ route('admin.jadwal-satpam.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="nip">NIP</label>
                            <select class="select2" id="nip" name="nip" required>
                                <option value="" disabled selected>Pilih Karyawan</option>
                                @foreach ($datauser as $p)
                                    <option value="{{ $p->nopeg }}">
                                        {{ $p->nopeg }} - {{ $p->name }} - {{ $p->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="shift_awal">Shift</label>
                            <select class="select2" id="shift_awal" name="shift_awal" required>
                                <option value="pagi">Pagi (07.00 - 15.00)</option>
                                <option value="pagi1">Pagi 1 (07.00 - 17.00)</option>
                                <option value="siang">Siang(15.00 - 23.00)</option>
                                <option value="malam">Malam (23.00 - 07.00)</option>
                                <option value="off">OFF (Libur)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="tanggal_awal">Tanggal Awal</label>
                            <input class="ts-datepicker form-control" id="tanggal_awal" name="tanggal_awal"
                                type="text" readonly required>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="tanggal_akhir">Tanggal Akhir</label>
                            <input class="ts-datepicker form-control" id="tanggal_akhir" name="tanggal_akhir"
                                type="text" readonly required>
                        </div>
                        <button class="btn btn-primary mt-4" type="submit" data-id="">Submit</button>
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
                    url: `{{ route('admin.jadwal-satpam.calendar.all') }}`,
                    method: 'GET',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                }],
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    console.log("clicked")
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
                            if (hasCuti || hasIzin) {
                                $("#tagable").html(`
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="">Alasan</label>
                                        <p>${(hasCuti) ? 'CUTI ' + response.tagable.jeniscuti.jenis_cuti : response.tagable.jenisizin.jenis_izin}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="">Total Cuti</label>
                                        <p>${(hasCuti) ? response.tagable.total_cuti + ' Hari' : response.tagable.total_izin + ' Hari'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-form-label pt-0" for="">Diganti Oleh</label>
                                        <p>${response.pengganti != undefined ? response.pengganti.nopeg + ' - ' + response.pengganti.name : 'Belum Ada!'}</p>
                                    </div>
                                `)
                                $("#detail-jadwal").html('Detail Jadwal (Pengganti)')
                            } else {
                                $("#tagable").html('')
                                $("#detail-jadwal").html('Detail Jadwal')
                            }
                            var myModal = new bootstrap.Modal(document.getElementById(
                                'modal-detail'), {
                                backdrop: 'static',
                            })
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
                },
                dateClick: function(info) {
                    info.jsEvent.preventDefault();

                    $('.ts-datepicker').daterangepicker({
                        singleDatePicker: true,
                        timePicker: true,
                        timePicker24Hour: true,
                        showDropdowns: true,
                        autoUpdateInput: true,
                        autoApply: true,
                        startDate: info.dateStr,
                        minDate: info.dateStr,
                        drops: 'auto',
                        locale: {
                            cancelLabel: 'Hapus',
                            applyLabel: 'Terapkan',
                            format: 'YYYY-MM-DD HH:mm'
                        },
                        parentEl: '#modal-form'
                    });
                    $(".select2").select2({
                        dropdownParent: $('#modal-form')
                    });
                    var myModal = new bootstrap.Modal(document.getElementById(
                        'modal-form'))
                    myModal.show()
                }
            });
            calendar.setOption('locale', 'id');
            calendar.render();
        }
    </script>
@endsection
