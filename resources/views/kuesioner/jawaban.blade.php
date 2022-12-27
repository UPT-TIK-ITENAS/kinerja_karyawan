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
                            <table class="dataTable" id="table-jawaban">
                                <thead>
                                    <th>No.</th>
                                    <th>Jawaban</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($data as $no => $b)
                                        <tr>
                                            <th scope="row">{{ $no + 1 }}</th>
                                            <td>{{ $b->jawaban }}</td>
                                            <td>{{ $b->nilai }}</td>
                                            <td> <a href="#" class="btn btn-warning btn-xs edit"
                                                    data-bs-toggle="modal" data-id='{{ $b->id }}'><i
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
                <form autocomplete="off" class="needs-validation" action="{{ route('admin.kuesioner.updateJawaban') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-8">
                                <input id="id" name="id" type="hidden">
                                <input id="pertanyaan_kinerja_id" name="pertanyaan_kinerja_id" type="hidden">
                                <span class="form-label" for="jawaban">Jawaban</span>
                                <textarea class="form-control" id="jawaban" name="jawaban" required></textarea>
                            </div>
                            <div class="col-md-3">
                                <span class="form-label" for="nilai">Nilai</span>
                                <input class="form-control" id="nilai" name="nilai" type="text" required="">
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
        let table = $('#table-jawaban').DataTable({
            fixedHeader: true,
            pageLength: 10,
            responsive: true,
            processing: true,
        });

        $('body').on('click', '.edit', function() {
            var id = $(this).data('id');

            $.get(`${window.baseurl}/admin/kuesioner/editJawaban/${id}`, function(data) {
                $('#ModalTitle').html("Jawaban");
                $('#show').modal('show');
                $('#id').val(data.id);
                $('#jawaban').val(data.jawaban);
                $('#pertanyaan_kinerja_id').val(data.pertanyaan_kinerja_id);
                $('#nilai').val(data.nilai);
            })
        });
    </script>
@endsection
