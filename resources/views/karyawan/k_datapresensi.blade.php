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
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Filter</label>
                            <div class="col-xl-4">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter1" id="filter1" required="">
                                    <option selected="" disabled="" value="">Pilih Bulan </option>
                                    @foreach ($attendance as $a)
                                        <option value="{{ $a->month_index }}">{{ $a->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <button class="btn btn-outline-danger txt-red" type="button" id="clear"><i
                                        class="icofont icofont-ui-delete"></i> Hapus</button>
                                {{-- <button class="btn btn-danger" type="submit" id="clear">Hapus</button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="dataTable" id="table-kehadiran">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Siang</th>
                                    <th>Jam Keluar</th>
                                    {{-- <th>Durasi</th> --}}
                                    <th>Telat Masuk</th>
                                    <th>Telat Siang</th>
                                    <th>Note</th>
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
                pageLength: 20,
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
                        d.filter1 = $('#filter1').val() ? $('#filter1').val() : '<>';
                        // d.search = $('input[type="search"]').val();
                    }
                },
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
                        data: 'days',
                        name: 'days'
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
                        data: 'latemasuk',
                        name: 'latemasuk'
                    },
                    {
                        data: 'latesiang',
                        name: 'latesiang'
                    },
                    {
                        data: 'note',
                        name: 'note'
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


            $("#clear").on('click', function(e) {
                e.preventDefault();
                // location.reload();
                $("#filter1").val('').trigger('change');
            });
            $("#filter1").on('change', function() {
                table.draw();
            });

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
                console.log(message);
            };
        });
    </script>
@endsection
