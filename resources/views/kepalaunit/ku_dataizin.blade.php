@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Izin</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Izin</li>
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
                        <div class="row">
                            <div class="table-responsive">
                                <table class="dataTable" id="table-izinKU">
                                    <thead>
                                        <tr align="center">
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>Jenis Izin</th>
                                            <th>Tanggal Awal</th>
                                            <th>Tanggal Akhir</th>
                                            <th>Total Hari</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $no => $r)
                                            <tr>
                                                <td align="center">{{ $no + 1 }}</td>
                                                <td>{{ $r->nopeg }} - {{ $r->name  }}   </td>
                                                <td>{{ $r->nama_izin }} </td>
                                                
                                                <td>{{ \Carbon\Carbon::parse($r->tgl_awal_izin)->isoFormat('D MMMM Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($r->tgl_akhir_izin)->isoFormat('D MMMM Y') }}
                                                </td>
                                                <td align="center">{{ $r->total_izin }}</td>
                                                <td align="center">{{ $r->tgl_pengajuan }}</td>
                                                @if ( $r->approval == 1)
                                                <td><span class="badge badge-primary">Disetujui</span> </td>
                                                @elseif ($r->approval == 2 )
                                                <td><span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span></td> 
                                                @else
                                                <td><span class="badge badge-warning">Menunggu</span></td> 
                                                @endif
                                                <td align="center">{!! getAksi($r->id_izinkerja, 'izin') !!} <span class="jumlah" data-id="{{ $r->id_izinkerja }}"></span></td>
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
    </div>
@endsection

@section('scripts')

    <div class="modal fade bd-example-modal-lg" id="apprvIzin" aria-labelledby="myLargeModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Form Pengajuan Izin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" novalidate="" action="{{ route('kepalaunit.updateizin') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger" id="lebihHari" style="display: none;">
                            ⚠️ Tidak boleh melebihi jumlah hari yang telah ditentukan.
                        </div>
                        <div class="row g-1 mb-3">
                            <div class="col-md-4">
                                <span class="form-label" for="name">Nama</span>
                                <input class="form-control" id="name" name="name" type="text" readonly>
                            </div>
                            <input id="id_izinkerja" name="id_izinkerja" type="hidden">
                            
                            <div class="col-md-4">
                                <span class="form-label" for="namaizin">Jenis izin</span>
                                <input class="form-control" id="namaizin" name="namaizin" type="text" readonly>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="tgl_awal_izin">Tanggal Awal</span>
                                <input class="form-control" id="tgl_awal_izin" name="tgl_awal_izin" type="text" readonly>
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="tgl_akhir_izin">Tanggal Akhir</span>
                                <input class="form-control" id="tgl_akhir_izin" name="tgl_akhir_izin" type="text" readonly>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label" for="total_izin">Total Hari</span>
                                <input class="form-control" id="total_izin" name="total_izin" type="number" readonly>
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="tgl_pengajuan">Tanggal Pengajuan</span>
                                <input class="form-control" id="tgl_pengajuan" name="tgl_pengajuan" type="number" readonly>
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="approval">Status</span>
                                <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                    id="approval" name="approval" required="">
                                    <option selected="" disabled="" value="">-- Pilih ---</option>
                                    <option value="1">Disetujui</option>
                                    <option value="2">Disetujui Atasan dari Atasan Langsung</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                           
                            
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">

                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @parent
    <script>
        let table = $('#table-izinKU').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });


        $('body').on('click', '.apprvIzin', function() {
            var id = $(this).data('id');

            $.get('/kepalaunit/editizin/' + id, function(data) {
                $('#ModalTitle').html("Edit Kinerja");
                $('#apprvIzin').modal('show');
                $('#id_izinkerja').val(data.id_izinkerja);
                $('#jenis_izin').val(data.jenis_izin);
                $('#name').val(data.name);
                $('#namaizin').val(data.namaizin);
                $('#tgl_awal_izin').val(data.tgl_awal_izin);
                $('#tgl_akhir_izin').val(data.tgl_akhir_izin);
                $('#total_izin').val(data.total_izin);
                $('#tgl_pengajuan').val(data.tgl_pengajuan);
                $('#approval').val(data.approval);
                
                console.log(data);
            })
        });
     
    </script>
@endsection
