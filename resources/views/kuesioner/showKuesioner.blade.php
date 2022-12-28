@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Kuesioner Kinerja {{ substr_replace($kuesioner->semester, '/', 4, 0) }}</h3>
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
                        <div class="row mt-1">
                            <div class="col col-md-2">
                                <h5 class=" form-control-label" style="color:green; font-weight:bold">
                                    Pertanyaan
                                </h5>
                            </div>
                            <form autocomplete="off" class="settings-form"
                                action="{{ route('kepalaunit.storeKuesioner', $kuesioner->id) }}" method="POST">
                                @csrf
                                <div class="app-card-body py-2 px-4">
                                    <div class="row mt-3">
                                        <div class="col col-md-2">
                                            <label class=" form-control-label" style="font-weight:bold" hidden>
                                                Nama Penilai
                                            </label>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            @foreach ($data['Jabatan'] as $j)
                                                <input class="form-control" name="nama_penilai" id="nama_penilai" required
                                                    readonly hidden
                                                    value="{{ $j->nopeg }}  -   {{ $j->nama }}  -   {{ $j->jabatan }}"></input>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col col-md-2">
                                            <label class=" form-control-label" style="font-weight:bold">
                                                Nama Pegawai
                                            </label>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <select
                                                class="form-control js-example-basic-single col-sm-12 select2-hidden-accessible"
                                                id="nama_pegawai" name="nama_pegawai" required="">
                                                <option selected="" disabled="" value="">-- Pilih Pegawai ---
                                                </option>
                                                @foreach ($data['User'] as $r)
                                                    <option
                                                        value="{{ $r->nopeg }}-{{ $r->name }}-{{ $r->unit }}-{{ $r->jabatan }}"
                                                        data-peg="{{ $r->nopeg }}">
                                                        {{ $r->nopeg }} | {{ $r->name }} | {{ $r->jabatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" id="peg" name="peg">
                                    @foreach ($kuesioner->pertanyaan as $key => $pertanyaan)
                                        <div class="row mt-3">
                                            <div class="col col-md-2">
                                                <label class=" form-control-label" style="font-weight:bold">
                                                    Pertanyaan {{ $key + 1 }}
                                                </label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <p class="form-control-static" style="font-weight:bold">
                                                    {{ $pertanyaan->pertanyaan }}
                                                </p>
                                                @foreach ($pertanyaan->jawaban as $jawaban)
                                                    <div class="form-check">
                                                        <label for="jawaban{{ $jawaban->id }}" class="form-check-label"
                                                            style="font-weight:normal">
                                                            <input type="radio" id="jawaban{{ $jawaban->id }}"
                                                                class="form-check-input"
                                                                name="responden[{{ $key }}][jawaban_kinerja_id]"
                                                                value="{{ $jawaban->id }}"
                                                                {{ old('responden.' . $key . '.jawaban_kinerja_id') == $jawaban->id ? 'checked' : '' }}
                                                                required>{{ $jawaban->jawaban }}
                                                        </label>
                                                        <input type="hidden"
                                                            name="responden[{{ $key }}][pertanyaan_kinerja_id]"
                                                            value="{{ $pertanyaan->id }}">
                                                    </div>

                                                    <div>
                                                        <small class="text-danger">
                                                            {{ $errors->first('responden.' . $key . '.jawaban_kinerja_id') }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="app-card-footer p-4 mt-auto">
                                    <button type="submit" class="btn btn-primary">Submit Kuesioner</button>
                                </div>
                            </form>
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
        });

        $(document).ready(function() {
            $('#nama_pegawai').on('change', function() {
                const selected = $(this).find('option:selected');
                $("#peg").val(selected.data('peg'));
            });
        });
    </script>
@endpush
