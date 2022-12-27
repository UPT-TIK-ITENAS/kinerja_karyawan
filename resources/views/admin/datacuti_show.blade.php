@extends('layouts.app')
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://repo.rachmat.id/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <h5>Form Cuti</h5>
        </div>
        <form autocomplete="off" action="{{ route('admin.storecuti') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <div class="mb-1">
                            <label class="form-label" for="helpInputTop">No. Pegawai</label>
                            <p class="fw-bold">{{ $cuti->nopeg }}</p>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">Jenis Cuti</label>
                            <p class="fw-bold">{{ $jeniscuti->jenis_cuti }}</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <label class="form-label" for="disabledInput">Tanggal Awal</label>
                        <p class="fw-bold">{{ $cuti->tgl_awal_cuti }}</p>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <label class="form-label" for="disabledInput">Tanggal Akhir</label>
                        <p class="fw-bold">{{ $cuti->tgl_akhir_cuti }}</p>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">Total Lama Cuti</label>
                            <p class="fw-bold">{{ $cuti->total_cuti }} Hari</p>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <div class="mb-1">
                            <label class="form-label" for="basicInput">No Telp</label>
                            <p class="fw-bold">{{ $cuti->no_hp }}</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 mb-2">
                        <div class="mb-1">
                            <label class="form-label" for="exampleFormControlTextarea1">Alamat</label>
                            <p class="fw-bold">{{ $cuti->alamat }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <div id="calendar"></div>
    </div>
@endsection

@push('modal')
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" id="modal-form">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Masukkan Pengganti
                    </h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="theme-form" action="{{ route('admin.datacuti.pengganti') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id_jadwal" id="id_jadwal" value="">
                        <div class="mb-3">
                            <label class="form-label" for="nip">Pengganti</label>
                            <select class="form-control select2 @error('nip') is-invalid @enderror" id="nip"
                                name="nip">
                                <option value="" disabled selected>Pilih Karyawan</option>
                            </select>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-primary mt-4" type="submit" id="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#nopeg').on('change', function() {
                const selected = $(this).find('option:selected');
                const name = selected.data('name');
                const unit = selected.data('unit');

                $("#name").val(name);
                $("#unit").val(unit);
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
                    url: `{{ route('admin.datacuti.calendar', ['id' => $cuti->id_cuti, 'nopeg' => $cuti->nopeg]) }}`,
                    method: 'GET',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                    isLoading: true,
                }],
                eventClick: function(info) {
                    let id = info.event.id
                    console.log(info)
                    $("#id_jadwal").val(id)
                    $.ajax({
                        url: `{{ url('admin/jadwal-satpam/check-pengganti') }}/${id}`,
                        method: 'GET',
                        success: function(data) {
                            console.log(data);
                            if (data.pengganti) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: `Pengganti sudah ada yaitu : ${data.data.nip_pengganti}`,
                                })
                            } else {
                                $(".select2").select2({
                                    dropdownParent: $('#modal-form'),
                                    ajax: {
                                        url: `{{ route('admin.jadwal-satpam.dataSatpamOffByDate') }}`,
                                        dataType: "json",
                                        data: function(params) {
                                            return {
                                                search: params.term,
                                                startDate: info.event.startStr,
                                                endDate: info.event.endStr,
                                            };
                                        },
                                        processResults: function(response) {
                                            let results = [];
                                            response.forEach(data => {
                                                results.push({
                                                    "id": data.user
                                                        .nopeg,
                                                    "text": `${data.user.nopeg} - ${data.user.name}`
                                                })
                                            })
                                            return {
                                                results
                                            };
                                        },
                                        cache: true,
                                    },
                                });
                                var myModal = new bootstrap.Modal(document.getElementById(
                                    'modal-form'))
                                myModal.show()
                            }

                        }
                    });

                }
            });
            calendar.setOption('locale', 'id');
            calendar.render();
        }
    </script>
@endsection
