<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | RESET KATALALUAN</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<style>
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
<body class="hold-transition login-page" style="background-image: url('{{asset('images/test2.png')}}');">
    @include('flash-message')
<div class="login-box">
    <div class="row">
        <img src="{{asset('images/penang.png')}}" class="center" >
    </div>
  <div class="login-logo">
    <a href="{{route('login')}}"><b>HELPDESK ICT</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Terlupa Katalaluan? Sila isi emel anda untuk dapatkan katalaluan sementara.</p>

       <form action="{{route('forgot.store')}}" method="post">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Mohon Katalaluan Baru</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1 text-center">
        <a href="{{route('login')}}">Log Masuk</a>
      </p>
      <p class="mb-0 text-center">
        <a href="{{route('daftar.daftarPengguna')}}" class="text-center">Daftar Baru</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{('dist/js/adminlte.min.js')}}"></script>

</body>
</html>
