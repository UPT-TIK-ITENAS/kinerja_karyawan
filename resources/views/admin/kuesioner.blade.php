@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Zero Configuration  Starts-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row mb-2">
                        <h5>Pendataan Hari Libur </h5>
                        <span>Daftar hari libur nasional</span>
                    </div>
                 
                </div>
                <div class="card-body">
                    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                        <form action="{{ route('admin.storekuesioner') }}" method="POST" enctype="multipart/form-data">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 1</h4>
                                </div>
                                <div class="card-body">
                                    @csrf
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[0]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 2</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[1]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 3</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[2]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 4</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[3]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 5</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[4]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 6</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[5]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan 7</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        {{ $pertanyaan_kuesioner[6]->pertanyaan }}
                                    </p>
                                    <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1" checked />
                                            <label class="form-check-label" for="inlineRadio1">Jawaban 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3" />
                                            <label class="form-check-label" for="inlineRadio2">Jawaban 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')


    @parent

@endsection
