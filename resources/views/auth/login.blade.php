<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HELPDESK ICT | LOG MASUK</title>
  <link rel = "icon" href="{{asset('images/penang.png')}}" type="image/x-icon">
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<style>
.login,.image {
  min-height: 100vh;
}

.bg-image {
  background-image: url('https://res.cloudinary.com/mhmd/image/upload/v1555917661/art-colorful-contemporary-2047905_dxtao7.jpg');
  background-size: cover;
  background-position: center center;
}
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  /* width: 50%; */
}
img {
  width: 135px;
  height: 190px;
}


</style>
<body class="hold-transition login-page" style="background-image: url('#');">

<div class="container-fluid">
    <div class="row no-gutter">
        <!-- The image half -->
        <div class="col-md-6 d-none d-md-flex bg-image"></div>


        <!-- The content half -->
        <div class="col-md-6 bg-light">
            <div class="login d-flex align-items-center py-5">

                <!-- Demo content-->
                <div class="container">
                    @include('flash-message')
                    <div class="row">
                        <img src="{{asset('images/penang.png')}}" class="center" >
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-10 col-xl-7 mx-auto">
                            <h3 class="display-5 text-center">HELPDESK ICT</h3>
                            <p class="text-center text-muted mb-4"></p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <input id="inputIDpengguna" @error('idpengguna') is-invalid @enderror name="idpengguna"
                                        value="{{ old('idpengguna') }}" type="text" placeholder="Masukkan No. Kad Pengenalan" required autofocus
                                        autocomplete="idpengguna" class="form-control rounded-pill border-0 shadow-sm px-4">

                                        @error('idpengguna')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <input id="inputPassword" type="password"
                                        placeholder="Masukkan Kata Laluan"  @error('password') is-invalid @enderror
                                        name="password" required autocomplete="current-password"
                                        class="form-control rounded-pill border-0 shadow-sm px-4 text-primary">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                </div>

                                {{-- <div class="custom-control custom-checkbox mb-3">
                                    <input id="customCheck1" type="checkbox" checked class="custom-control-input">
                                    <label for="customCheck1" class="custom-control-label">Remember password</label>
                                </div> --}}
                                <button type="submit" class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">Log Masuk</button>
                                <div class="text-center ">
                                    <a class="btn btn-link" href="{{ route('daftar.forgotpass') }}">
                                        Reset Katalaluan
                                    </a>
                                    <a class="btn btn-link" href="{{ route('daftar.daftarPengguna') }}">
                                        Daftar Pengguna
                                    </a>
                                </div>


                                {{-- <div class="text-center d-flex justify-content-between mt-4"><p>Copyright by <a href="#" class="font-italic text-muted">
                                        <u>BTMKN</u></a></p></div> --}}
                            </form>
                        </div>
                    </div>
                </div><!-- End -->

            </div>
        </div><!-- End -->

    </div>
</div>

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
