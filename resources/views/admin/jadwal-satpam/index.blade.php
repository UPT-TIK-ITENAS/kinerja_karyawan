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
                        <div class="table-responsive my-5">
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
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" id="modal-form">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Tambah Jadwal
                    </h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="theme-form">
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="nama">Nama</label>
                            <input class="form-control" id="nama" name="nama" type="text" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="nip">NIP</label>
                            <input class="form-control" id="nip" name="nip" type="text" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label pt-0" for="nip">NIP</label>
                            <input class="form-control" id="nip" name="nip" type="text" readonly>
                        </div>
                        <button class="btn btn-primary mt-4">Submit</button>
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
            });
            calendar.setOption('locale', 'id');
            calendar.render();
        }
    </script>
@endsection
