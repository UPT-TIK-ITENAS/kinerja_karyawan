@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Edit Profile</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Users</li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="edit-profile">
            <form autocomplete="off" class="needs-validation" novalidate=""
                action="{{ route('karyawan.profile.update_profile') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="profile-title">
                                        <div class="media"> <img class="img-70 rounded-circle" alt=""
                                                src="{{ asset('assets/images/dashboard/1.png') }}">
                                            <div class="media-body">
                                                <h3 class="mb-1 f-20 txt-primary">{{ auth()->user()->name }}</h3>
                                                <p class="f-12">{{ auth()->user()->jabatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input class="form-control" type="text" name="name" id="name"
                                                value="{{ auth()->user()->name }}" placeholder="Nama Lengkap">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">No Pegawai</label>
                                            <input class="form-control" type="text" name="nopeg" id="nopeg"
                                                value="{{ auth()->user()->nopeg }}" placeholder="No Pegawai">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">NPP</label>
                                            <input class="form-control" type="text" name="npp" id="npp"
                                                value="{{ auth()->user()->npp }}" placeholder="NPP">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Masuk Kerja</label>
                                            <input class="form-control" type="date" name="masuk_kerja" id="masuk_kerja"
                                                value="{{ auth()->user()->masuk_kerja }}" placeholder="Tanggal Masuk Kerja">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tempat</label>
                                            <input class="form-control" type="text" name="tempat" id="tempat"
                                                value="{{ auth()->user()->tempat }}" placeholder="Tempat Lahir">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input class="form-control" type="text" name="tanggal_lahir"
                                                id="tanggal_lahir" value="{{ auth()->user()->tanggal_lahir }}"
                                                placeholder="Tanggal Lahir">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input class="form-control" type="text" name="email" id="email"
                                                value="{{ auth()->user()->email }}" placeholder="xxxxxx@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">No Handphone</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="no_hp_input">+62</span>
                                                <input class="form-control" id="nohp" name="nohp" type="text" placeholder="+62 878-XXXX-XXXX"
                                                    aria-describedby="nohp_input" value="{{ auth()->user()->nohp }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">ID Telegram</label>
                                            <input class="form-control" type="text" name="telegram_id" id="telegram_id"
                                                value="{{ auth()->user()->telegram_id }}" placeholder="ID Telegram">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Password </label>
                                            <div class="input-group" id="show_hide_password">
                                                <input class="form-control" type="password" id="password"
                                                    name="password">
                                                <a href="" class="btn btn-outline-info"><i class="bi bi-eye-slash"
                                                        aria-hidden="true"></i></a>
                                            </div>
                                            <p style="color:red; font-size:12px;"> <b> *) Kosongkan jika tidak ingin merubah password </b></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary" type="submit">Ubah Profile</button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        document.getElementById('nohp').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,5})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bi bi-eye-slash");
                    $('#show_hide_password i').removeClass("bi bi-eye");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bi bi-eye-slash");
                    $('#show_hide_password i').addClass("bi bi-eye");
                }
            });
        });
    </script>
@endsection
