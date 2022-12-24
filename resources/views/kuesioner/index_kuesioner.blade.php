@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Kuesioner Pegawai</h3>
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
                            <div class="row g-1 mb-3">
                                <div class="col-md-12">

                                    <p>Kuesioner ini menanyakan pendapat anda mengenai kinerja karyawan selama 1 periode penilaian. 
                                        Pengumpulan data menggunakan kuesioner ini bertujuan untuk mengevaluasi tingkat kecakapan karyawan dalam melakukan pekerjaannya berdasarkan indikator yang ditentukan. 
                                        Berikan tanggapan berdasarkan pendapat sendiri dan bukan pandangan/pendapat orang lain. 
                                        Kami mengucapkan banyak terima kasih atas partisipasinya dalam pengisian kuesioner ini.</p>
                                    <span class="form-label" for="semester"> Pilih Periode</span>
                                    <br>
                                    <select class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                        id="select-kuesioner" name="select-kuesioner" required="">
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