@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="app-page-title">Kuesioner Akademik
                            {{ substr_replace($kuesioner->semester, '/', 4, 0) }}</h1>
                        <div class="app-card alert shadow-sm mb-4 border-left-decoration">
                            <div class="inner">
                                <div class="app-card-body p-3 p-lg-4">
                                    <h3 class="mb-3">Instrumen Penilaian Efektivitas Proses Pembelajaran
                                        {{ substr_replace($kuesioner->semester, '/', 4, 0) }}
                                    </h3>
                                    <div class="row gx-5 gy-3">
                                        <div class="col-12">
                                            <div>Kuesioner ini menanyakan pendapat anda mengenai Pembelajaran dan Suasana
                                                Akademik
                                                selama
                                                semester ini. Pengumpulan data menggunakan kuesioner ini bertujuan mengukur
                                                keefektifan
                                                kegiatan belajar yang telah dilakukan. Berikan tanggapan berdasarkan
                                                pendapat
                                                sendiri
                                                dan bukan pandangan/pendapat orang lain. Kami mengucapkan banyak terima
                                                kasih
                                                atas
                                                partisipasinya dalam pengisian kuesioner ini.
                                            </div>
                                        </div>
                                        <!--//col-->
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col col-md-3">
                                            <label class=" form-control-label" style="color:green; font-weight:bold">
                                                Petunjuk Pengisian
                                            </label>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <p class="form-control-static" style="color:green">Kuesioner ini terdiri
                                                dari {{ count($kuesioner->pertanyaan) }} butir
                                                pertanyaan dengan bentuk jawaban pilihan ganda yang terdiri dari 4
                                                (empat) pilihan jawaban.
                                                Anda dapat memilih satu pilihan sesuai pendapat pribadi.</p>
                                            <div class="form-check">
                                                <div class="radio">
                                                    <label for="radio3" class="form-check-label " style="color:green">
                                                        <input type="radio" id="radio4" name="radios" value="option4"
                                                            class="form-check-input" disabled="">Sangat
                                                        Baik (Sangat Puas)
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label for="radio3" class="form-check-label " style="color:green">
                                                        <input type="radio" id="radio3" name="radios" value="option3"
                                                            class="form-check-input" checked="" disabled="">Baik
                                                        (Puas)
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label for="radio1" class="form-check-label " style="color:green">
                                                        <input type="radio" id="radio1" name="radios" value="option1"
                                                            class="form-check-input" disabled="">Kurang
                                                        (Tidak Puas)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center gx-3">
                            <div class="col-auto">
                                <div class="app-icon-holder">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-receipt"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z">
                                        </path>
                                        <path fill-rule="evenodd"
                                            d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-auto">
                                <h4 class="app-card-title">Pertanyaan</h4>
                            </div>
                        </div>
                    </div>

                    <form class="settings-form" action="{{ route('kepalaunit.storeKuesioner', $kuesioner->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" readonly class="form-control-plaintext" name="kode_matkul" id="kode_matkul">
                        <input type="hidden" readonly class="form-control-plaintext" name="nama_matkul" id="nama_matkul">
                        <input type="hidden" readonly class="form-control-plaintext" name="kelas" id="kelas">
                        <input type="hidden" readonly class="form-control-plaintext" name="kelas_asistensi"
                            id="kelas_asistensi">
                        <input type="hidden" readonly class="form-control-plaintext" name="kode_rek" id="kode_rek">
                        <input type="hidden" readonly class="form-control-plaintext" name="kode_fakultas"
                            id="kode_fakultas">
                        <input type="hidden" readonly class="form-control-plaintext" name="kode_prodi" id="kode_prodi">
                        <div class="app-card-body py-2 px-4">
                            @foreach ($kuesioner->pertanyaan as $key => $pertanyaan)
                                <div class="row mt-3">
                                    <div class="col col-md-3">
                                        <label class=" form-control-label" style="font-weight:bold">
                                            Pertanyaan {{ $key + 1 }}
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <p class="form-control-static" style="">
                                            {{ $pertanyaan->pertanyaan }}
                                        </p>
                                        @foreach ($pertanyaan->jawaban as $jawaban)
                                            <div class="form-check mb-1">
                                                <div class="radio">
                                                    <label for="jawaban{{ $jawaban->id }}" class="form-check-label "
                                                        style="">
                                                        <input type="radio" id="jawaban{{ $jawaban->id }}"
                                                            class="form-check-input"
                                                            name="responden[{{ $key }}][jawaban_akademik_id]"
                                                            value="{{ $jawaban->id }}"
                                                            {{ old('responden.' . $key . '.jawaban_akademik_id') == $jawaban->id ? 'checked' : '' }}
                                                            required>{{ $jawaban->jawaban }}
                                                    </label>
                                                </div>
                                                <input type="hidden"
                                                    name="responden[{{ $key }}][pertanyaan_kinerja_id]"
                                                    value="{{ $pertanyaan->id }}">
                                            </div>
                                            <div>
                                                <small class="text-danger">
                                                    {{ $errors->first('responden.' . $key . '.jawaban_akademik_id') }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="app-card-footer p-4 mt-auto">
                            <button type="submit" class="btn app-btn-primary">Submit Kuesioner</button>
                        </div>
                    </form>
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
            $("#select-matkul").change(function(e) {
                e.preventDefault();
                const values = e.target.value;
                $("#nodos").val(objectArray.dosen[0])
                $("#nama_dosen").val(objectArray.dosen[1])
                $("#kode_fakultas").val(objectArray.prodi[0])
                $("#kode_prodi").val(objectArray.prodi[1])
                $("#kode_matkul").val(objectArray.mataKuliah[0])
                $("#kelas").val(objectArray.mataKuliah[1])
                $("#kelas_asistensi").val(objectArray.mataKuliah[2] == 'null' ? '' : objectArray.mataKuliah[
                    2])
                $("#nama_matkul").val(objectArray.mataKuliah[3])
                $("#kode_rek").val(objectArray.mataKuliah[4])
            });
        });
    </script>
@endpush
