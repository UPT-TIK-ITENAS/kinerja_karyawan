@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Karyawan</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">List Data Karyawan</li>
                        <li class="breadcrumb-item active">Karyawan</li>
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
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-kar">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>NPP</th>
                                    <th>TTL</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Unit</th>
                                    <th>Atasan Langsung</th>
                                    <th>Atasan dari Atasan Langsung</th>
                                    <th>TMT</th>
                                </thead>
                                <tbody>
                                    @foreach ($peg as $no => $p)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ $p->nopeg }} - {{ $p->name }}</td>
                                            <td>{{ $p->npp }}</td>
                                            <td>{{ $p->tempat }}, {{ $p->tanggal_lahir }}</td>
                                            <td>{{ $p->email }}</td>
                                            <td>{{ $p->nohp }}</td>
                                            <td>{{ $p->singkatan_unit }}</td>
                                            <td>{{ $p->name_jab }}</td>
                                            <td>{{ $p->name_jab2 }}</td>
                                            <td>{{ $p->masuk_kerja }}</td>
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
@endsection

@section('scripts')
    @parent
    <script>
        let table = $('#table-kar').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });
    </script>
@endsection
