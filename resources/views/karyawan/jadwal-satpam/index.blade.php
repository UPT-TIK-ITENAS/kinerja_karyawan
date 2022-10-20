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
@endpush

@section('scripts')
    @parent
    <script>
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
                    url: `{{ route('karyawan.jadwal-satpam.calendar.by-user', auth()->user()->nopeg) }}`,
                    method: 'GET',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                }],
                eventClick: function(info) {
                    let id = info.event.id
                    $.ajax({
                        url: "{{ url('karyawan/jadwal-satpam/by-id') }}/" + id,
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
                                $("#detail-jadwal").html('Detail Jadwal (Diganti)')
                            } else {
                                $("#tagable").html('')
                                $("#detail-jadwal").html('Detail Jadwal')
                            }
                            var myModal = new bootstrap.Modal(document.getElementById(
                                'modal-detail'))
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
    </script>
@endsection
