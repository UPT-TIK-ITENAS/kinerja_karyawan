@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
    integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Presensi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Data Kehadiran</li>
                        <li class="breadcrumb-item active">Data Presensi</li>
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
                    <div class="card-header">
                        <div class="row mb-2">
                            <h5>Monitoring Kehadiran </h5>
                            <span>Daftar hasil monitoring kehadiran karyawan terhitung dari tanggal 01 Juli 2022</span>
                        </div>
                        <hr>
                        <div class="row justify-content-start">
                            <label class="form-label" for="validationDefault01">Pilih Bulan</label>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <input class="date-picker form-control" type="text" id="bulan_hadir"
                                    autocomplete="off" />
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <button type="button" class="btn btn-info" id="btn-filter">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-kehadiran">
                                <thead>
                                    <th>No.</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    <th>Kurang Jam</th>
                                    <th>Note</th>
                                    <th>Is Cuti</th>
                                    <th>Is Izin</th>
                                    <th>Aksi</th>
                                    <th>File</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row mb-2">
                            <h5>Kehadiran Karyawan </h5>
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <div class="modal fade bd-example-modal-lg" id="show-izin" aria-labelledby="myLargeModalLabel" aria-modal="true"
        tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form autocomplete="off" class="needs-validation" novalidate=""
                    action="{{ route('karyawan.storeizinkehadiran') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <span class="form-label" for="nip">No Pegawai</span>
                                <input class="form-control" id="nip" name="nip" type="text" required=""
                                    readonly>
                                <input id="id" name="id" hidden />
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" required=""
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <span class="form-label" for="nama_unit">Unit</span>
                                <input class="form-control" id="nama_unit" name="nama_unit" type="text" readonly
                                    required="">
                                <input id="unit" name="unit" hidden />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="tanggal">Tanggal</span>
                                <input class="form-control" id="tanggall" name="tanggall" type="text" readonly
                                    required="">

                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="jenis">Jenis</span>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    <option value="1">Izin</option>
                                    <option value="2">Sidik Jari</option>
                                    <option value="3">Dispensasi</option>
                                </select>

                            </div>
                            <div class="col-md-4 jamawal">
                                <span class="form-label" for="jam_masuk">Jam Keluar </span>
                                <div class="input-group clockpicker" data-autoclose="true">
                                    <input class="form-control" type="text" id="jam_awal" name="jam_awal"><span
                                        class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                            <div class="col-md-4 jamakhir">
                                <span class="form-label" for="jam_akhir">Jam Kembali </span>
                                <div class="input-group clockpicker" data-autoclose="true">
                                    <input class="form-control" type="text" id="jam_akhir" name="jam_akhir"><span
                                        class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                            {{-- <div class="col-md-4 jamizin">
                                <span class="form-label" for="tanggal_izin">Tanggal</span>
                                <input type="text" class="form-control" id="tanggal_izin" name="tanggal_izin"
                                    value="" />
                                <div class="invalid-feedback">Wajib Diisi !</div>
                            </div> --}}
                            <div class="col-md-4 jamizin">
                                <span class="form-label" for="jam_izin">Jam</span>
                                <div class="input-group clockpicker" data-autoclose="true">
                                    <input class="form-control" type="text" id="jam_izin" name="jam_izin"><span
                                        class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-7">
                            <span class="form-label" for="alasan">Alasan</span>
                            <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Alasan" required></textarea>
                            {{-- <input class="form-control" id="jam_masuk" name="jam_masuk" type="date"
                        required=""> --}}
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <div class="checkbox p-0">
                                    <div class="checkbox checkbox-dark">
                                        <input id="cb_valid" class="form-check-input" type="checkbox" required>
                                        <label class="form-check-label" for="cb_valid">Keterangan izin dilakukan oleh diri
                                            sendiri dan secara sadar sesuai dengan ketentuan yang berlaku</label>
                                    </div>
                                    <div class="invalid-feedback">Wajib di centang !</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="detail-jadwal"
        aria-hidden="true" id="modal-detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-jadwal">
                        Detail
                    </h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="theme-form">
                        <div class="modal-body">
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <span class="form-label" for="nip">Nama</span>
                                    <input class="form-control" id="read_name" name="read_name" type="text" readonly>
                                </div>
                                <div class="col-md-4">
                                    <span class="form-label" for="name">No Pegawai</span>
                                    <input class="form-control" id="read_nip" name="read_nip" type="text" readonly>
                                </div>
                                <div id="data-type"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function() {
            $('#bulan_hadir').datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'mm-yy',
                onClose: function(dateText, inst) {
                    function isDonePressed(dateText) {
                        return ($('#ui-datepicker-div').html().indexOf(
                            'ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover'
                        ) > -1);
                    }

                    if (isDonePressed(dateText)) {

                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(year, month, 1));
                        // console.log((parseInt(month) + 1) + '-' + year)
                        var month_int = parseInt(month) + 1
                        if (month_int.toString().length < 2) month_int = '0' + month_int;
                        // console.log([month_int, year].join('-'));
                        $("#bulan_hadir").val([month_int, year].join('-'))
                    }
                }
            })
            let table = $('#table-kehadiran').DataTable({
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
                ajax: {
                    url: "{{ route('karyawan.listdatapresensi') }}",
                    data: function(d) {
                        d.bulan = $('#bulan_hadir').val() ? $('#bulan_hadir').val() : '';
                        // d.tahun = ('#bulan_hadir').val()
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'hari',
                        name: 'hari'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_siang',
                        name: 'jam_siang'
                    },
                    {
                        data: 'jam_pulang',
                        name: 'jam_pulang'
                    },
                    {
                        data: 'kurang_jam',
                        name: 'kurang_jam'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'is_cuti',
                        name: 'is_cuti'
                    },
                    {
                        data: 'is_izin',
                        name: 'is_izin'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'print',
                        name: 'print'
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
            $("#btn-filter").on('click', function() {
                document.getElementById('btn-filter').style.pointerEvents = 'none';
                table.draw();
                document.getElementById('btn-filter').style.pointerEvents = 'auto';
            });
            daterangepicker('#tanggal_izin', drops = "auto", autoUpdate = false, autoApply = false, timePicker =
                true, parentEl = '#show-izin');
        });

        $('body').on('click', '.editAtt', function() {
            var id = $(this).data('id');

            $.get(`${window.baseurl}/karyawan/editAtt/${id}`, function(data) {
                $('#ModalTitle').html('Attendance');
                $('#show-izin').modal('show');
                $('#id').val(data.id);
                $('#nip').val(data.nip);
                $('#name').val(data.name);
                $('#unit').val(data.unit);
                $('#nama_unit').val(data.nama_unit);
                $('#tanggall').val(data.tanggal);
                $('#jam_masuk').val(data.jam_masuk);
                $('#jam_siang').val(data.jam_siang);
                $('#jam_pulang').val(data.jam_pulang);

                console.log(data);
            })
        });

        $(".jamawal").hide();
        $(".jamakhir").hide();
        $(".jamizin").hide();

        $('#jenis').on('change', function(e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            console.log(valueSelected);
            if (valueSelected == 1) {
                $(".jamawal").show();
                $(".jamakhir").show();
                $(".jamizin").hide();
            } else {
                $(".jamizin").show();
                $(".jamawal").hide();
                $(".jamakhir").hide();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            loadCalendarEvents()
            $("#calendar .fc-toolbar-chunk:nth-child(2)").addClass("d-none d-md-block");
        });

        const loadCalendarEvents = () => {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                displayEventTime: false,
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
                    url: `{{ route('karyawan.calendar.by-user', auth()->user()->nopeg) }}`,
                    method: 'GET',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                }],
                eventClick: function(info) {
                    let id = info.event.id
                    let type = info.event.extendedProps?.type
                    $.ajax({
                        url: "{{ url('karyawan/show-data-calendar') }}" + '?id=' + id + '&type=' +
                            type,
                        type: 'GET',
                        dataType: 'JSON',
                        success: function(response) {
                            console.log(response)
                            if (type === "attendance") {
                                let html = `<div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_jam_masuk">Jam Masuk</label>
                                                <input class="form-control" id="read_jam_masuk" name="read_jam_masuk" type="text" value="${response
                                        .jam_masuk}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_jam_siang">Jam Siang</label>
                                                <input class="form-control" id="read_jam_siang" name="read_jam_siang" value="${response
                                        .jam_siang}" type="text"
                                                    readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_jam_pulang">Jam Pulang</label>
                                                <input class="form-control" id="read_jam_pulang" name="read_jam_pulang" value="${response
                                        .jam_pulang}" type="text"
                                                    readonly>
                                            </div`;
                                $("#read_name").val(response.user.name)
                                $("#read_nip").val(response.nip)
                                $('#data-type').html(html);
                            } else if (type === "cuti") {
                                let html = `<div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_tanggal_awal">Tanggal Awal</label>
                                                <input class="form-control" id="read_tanggal_awal" name="read_tanggal_awal" type="text"
                                                value="${response.tgl_awal_cuti}"
                                                readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_tanggal_akhir">Tanggal Akhir</label>
                                                <input class="form-control" id="read_tanggal_akhir" name="read_tanggal_akhir" type="text"
                                                value="${response.tgl_akhir_cuti}"
                                                    readonly>
                                            </div>`;
                                $("#read_name").val(response.user.name)
                                $("#read_nip").val(response.nopeg)
                                $('#data-type').html(html);
                            } else {
                                let html = `<div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_tanggal_awal">Tanggal Awal</label>
                                                <input class="form-control" id="read_tanggal_awal" name="read_tanggal_awal" type="text"
                                                value="${response.tgl_awal_izin}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="col-form-label pt-0" for="read_tanggal_akhir">Tanggal Akhir</label>
                                                <input class="form-control" id="read_tanggal_akhir" name="read_tanggal_akhir" type="text" value="${response.tgl_akhir_izin}"
                                                    readonly>
                                            </div>`;
                                $("#read_name").val(response.user.name)
                                $("#read_nip").val(response.nopeg)
                                $('#data-type').html(html);
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
                },
                dateClick: function(info) {
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
