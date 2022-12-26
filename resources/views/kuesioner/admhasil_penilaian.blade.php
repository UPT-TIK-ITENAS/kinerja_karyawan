@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Hasil Kuesioner Penilaian</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Kuesioner Penilian</li>
                        <li class="breadcrumb-item active">Hasil Kuesioner</li>
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
                        <h5>Silakan pilih periode untuk melihat penilaian kuesioner</h5>
                        <br>
                        <div class="form-group row">
                            <label class="col-lg-1 col-md-12 col-form-label">Periode</label>
                            <div class="col-lg-6 col-md-12">
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    name="filter1" id="filter1" required="">
                                    @foreach ($periode as $p)
                                        @if ($p->status == '1')
                                            <option value="{{ $p->id }}" selected>{{ $p->judul }}</option>
                                        @else
                                            <option value="{{ $p->id }}">{{ $p->judul }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-hasil">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Indeks</th>
                                    <th>Periode</th>
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
    <script>
        $().ready(function() {
            let table = $('#table-hasil').DataTable({
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
                url: "{{ route('admin.kuesioner.admlistPenilaian') }}",
                data: function(d) {
                    d.filter1 = $('#filter1').val() ? $('#filter1').val() : '<>';
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
                        data: 'nama_pegawai',
                        name: 'nama_pegawai'
                    },
                    {
                        data: 'nopeg',
                        name: 'nopeg'
                    },
                    {
                        data: 'indeks',
                        name: 'indeks'
                    },

                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ]
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
