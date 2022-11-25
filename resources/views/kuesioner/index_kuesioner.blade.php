@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Kuesioner Pegawai</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Daftar Pertanyaan Kuesioner Kinerja</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-1">
                            <div class="col col-md-3">
                                <label class="mt-1 fs-4 fw-bold form-control-label">
                                    Pilih Periode
                                </label>
                            </div>

                            <div class="row g-1 mb-3">
                                <div class="col-md-12">
                                    <span class="form-label" for="semester">Periode</span>
                                    <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                        id="select-kuesioner" name="select-kuesioner" required="">
                                        <option selected="" disabled="" value="">-- Pilih ---</option>
                                        @foreach ($kuesioner as $kue)
                                            <option value="{{ $kue->id }}">
                                                {{ substr_replace($kue->semester, '/', 4, 0) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Pilih salah satu !</div>
                                    <br></br>
                                    <button class="btn btn-primary" id="btn-start">Mulai</button>
                                </div>

                            </div>

                            {{-- <div class="col-12 col-md-9">
                                <select class="form-select my-2" id="select-kuesioner">
                                    @foreach ($kuesioner as $kue)
                                        <option value="{{ $kue->id }}">
                                            {{ substr_replace($kue->semester, '/', 4, 0) }}
                                        </option>
                                    @endforeach
                                </select>

                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Start Kuesioner
            $('#btn-start').click(function() {
                let kuesioner = $('#select-kuesioner').val();
                window.open(`/kepalaunit/kuesioner/kinerja/${kuesioner}`);
                //window.open(`${window.baseUrl}/mahasiswa/akademik/${kuesioner}`, '_blank');
            });
        });
    </script>
@endpush