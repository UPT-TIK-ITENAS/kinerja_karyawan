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
                    <div class="card-body">
                        {{-- <a href="{{ route('kepalaunit.createcuti') }}" class="btn btn-primary"><i
                                class="icofont icofont-plus-square"></i> Tambah</a> --}}
                        <div class="dt-ext table-responsive">
                            <table class="dataTable" id="table-hasil">
                                <thead>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Indeks</th>
                                    <th>Periode</th>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $b)
                                    <tr>
                                        <th scope="row">{{ $no + 1 }}</th>
                                        <td>{{ $b->nopeg }} - {{ $b->nama_pegawai }}</td>
                                        <td>{{ $b->indeks }}</td>
                                        <td>{{ $b->judul }}</td>
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
        let table = $('#table-hasil').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });
    </script>
@endsection
