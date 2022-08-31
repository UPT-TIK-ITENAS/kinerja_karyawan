@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Data Pengajuan Cuti</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pengajuan</li>
                        <li class="breadcrumb-item active">Cuti</li>
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
                                <table class="dataTable" id="table-cutiKU">
                                    <thead>
                                        <tr align="center">
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>Jenis Cuti</th>
                                            <th>Tanggal Awal</th>
                                            <th>Tanggal Akhir</th>
                                            <th>Total Hari</th>
                                            <th>Alamat</th>
                                            <th>No. Telp</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $no => $r)
                                        <tr>
                                            <td align="center">{{ $no + 1 }}</td>
                                            <td>{{ $r->name }}</td>
                                            <td>{{ $r->nama_cuti }}</td>
                                            <td>{{ \Carbon\Carbon::parse($r->tgl_awal_cuti)->isoFormat('D MMMM Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($r->tgl_akhir_cuti)->isoFormat('D MMMM Y') }}
                                            </td>
                                            <td>{{ $r->total_cuti }}</td>
                                            <td>{{ $r->alamat }}</td>
                                            <td>{{ $r->no_hp }}</td>
                                            <td>{{ $r->tgl_pengajuan }}</td>
                                            @if ( $r->approval == 1)
                                                <td><span class="badge badge-primary">Disetujui</span> </td>
                                            @elseif ($r->approval == 2 )
                                                <td><span class="badge badge-success">Disetujui Atasan dari Atasan Langsung</span></td>  
                                            @else
                                                <td><span class="badge badge-warning">Menunggu</span></td>  
                                            @endif
                                            <td align="center">{!! getAksi($r->id_cuti, 'cuti') !!}</td>
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

<div class="modal fade bd-example-modal-lg" id="apprvCuti" aria-labelledby="myLargeModalLabel" aria-modal="true"
role="dialog">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Form Pengajuan Cuti</h4>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                data-bs-original-title="" title=""></button>
        </div>
        <form class="needs-validation" novalidate="" action="{{ route('kepalaunit.updatecuti') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="row g-1 mb-3">
                    <div class="col-md-4">
                        <span class="form-label" for="name">Nama</span>
                        <input class="form-control" id="name" name="name" type="text" readonly>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="nama_cuti">Jenis Cuti</span>
                        <input class="form-control" id="nama_cuti" name="nama_cuti" type="text" readonly>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="tgl_awal_cuti">Tanggal Awal</span>
                        <input class="form-control" id="tgl_awal_cuti" name="tgl_awal_cuti" type="text" readonly>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="tgl_akhir_cuti">Tanggal Akhir</span>
                        <input class="form-control" id="tgl_akhir_cuti" name="tgl_akhir_cuti" type="text" readonly>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="total_cuti">Total Hari</span>
                        <input class="form-control" id="total_cuti" name="total_cuti" type="number" readonly>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="no_hp">No HP</span>
                        <div class="input-group">
                            <span class="input-group-text" id="no_hp_input">+62</span>
                            <input class="form-control" id="no_hp" name="no_hp" type="text"
                                aria-describedby="no_hp_input" readonly>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="form-label" for="alamat">Alamat</span>
                        <textarea name="alamat" id="alamat" name="alamat" class="form-control" readonly></textarea>
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
              
            </div>
            <div class="modal-footer justify-content-between">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
</div>
    @parent
    <script>
        let table = $('#table-cutiKU').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.apprvCuti', function() {
            var id = $(this).data('id');

            $.get('/kepalaunit/editcuti/' + id, function(data) {
                $('#ModalTitle').html("Edit Kinerja");
                $('#apprvIzin').modal('show');
                $('#id_cuti').val(data.id_cuti);
                $('#jenis_cuti').val(data.jenis_cuti);
                $('#name').val(data.name);
                $('#nama_cuti').val(data.nama_cuti);
                $('#tgl_awal_cuti').val(data.tgl_awal_cuti);
                $('#tgl_akhir_cuti').val(data.tgl_akhir_cuti);
                $('#total_cuti').val(data.total_cuti);
                $('#tgl_pengajuan').val(data.tgl_pengajuan);
                $('#alamat').val(data.alamat);
                $('#no_hp').val(data.no_hp);
                $('#approval').val(data.approval);
                
                console.log(data);
            })
        });
    </script>
@endsection
