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
                            <table class="dataTable" id="table-pertanyaan">
                                <thead>
                                    <th>No.</th>
                                    <th>Pertanyaan</th>
                                    <th>Detail Jawaban</th>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $b)
                                        <tr>
                                            <th scope="row">{{ $no + 1 }}</th>
                                            <td>{{ $b->pertanyaan }}</td>
                                            <td> <a class="btn btn-success btn-xs"
                                                    href="{{ route('admin.kuesioner.jawaban', $b->id) }}"><i
                                                        class="icofont icofont-eye-alt"></i></a>

                                                <a href="#" class="btn btn-warning btn-xs edit" data-bs-toggle="modal"
                                                    data-id='{{ $b->id }}'><i
                                                        class="icofont icofont-pencil-alt-2"></i></a>
                                            </td>
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
    <div class="modal fade bd-example-modal-lg" id="show" aria-labelledby="myLargeModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit Pertanyaan</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                        data-bs-original-title="" title=""></button>
                </div>
                <form class="needs-validation" action="{{ route('admin.kuesioner.updatePertanyaan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-8">
                                <input id="id" name="id" type="hidden">
                                <span class="form-label" for="pertanyaan">Pertanyaan</span>
                                <input class="form-control" id="pertanyaan" name="pertanyaan" type="text" required="">
                            </div>
                            <div class="col-md-4">
                                <span class="form-label" for="status">Periode</span>
                                <select name="kuesioner_kinerja_id" id="kuesioner_kinerja_id" class="form-control">
                                    <option value='' disabled selected>Pilih Status</option>
                                    @foreach ($periode as $p)
                                        <option value="{{ $p->id }}">{{ $p->judul }}</option>
                                    @endforeach

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
        let table = $('#table-pertanyaan').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.edit', function() {
            var id = $(this).data('id');

            $.get(`${window.baseurl}/admin/kuesioner/editPertanyaan/${id}`, function(data) {
                $('#ModalTitle').html("Pertanyaan");
                $('#show').modal('show');
                $('#id').val(data.id);
                $('#pertanyaan').val(data.pertanyaan);
                $('#kuesioner_kinerja_id').val(data.kuesioner_kinerja_id);
            })
        });
    </script>
@endsection
