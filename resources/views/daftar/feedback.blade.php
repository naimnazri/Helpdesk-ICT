<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HELPDESK ICT | MAKLUM BALAS PENGADU</title>
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
    .img {
      width: 135px;
      height: 190px;
    }
</style>
<body class="hold-transition login-page" style="background-image: url('{{asset('images/test2.png')}}');">
<div class="login-box">
    <div class="row ">
        <img class="img" src="{{asset('images/penang.png')}}" class="center" >
    </div>
  <div class="login-logo">
    <a href="{{ROUTE('login')}}"><b>HELPDESK ICT</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Maklum Balas Pengguna</p>

      <form action="{{route('daftar.storeFeedback')}}" method="post">
          @csrf
      <input type="text" name="no_aduan" value="{{$no_aduan}}" hidden>

        <div class="form-group">
            <label>Maklum Balas Pengadu</label>
            <div class="form-check">
                @foreach($respon_feedback as $res)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="respon" id="idlevel" value="{{$res->idrespon}}" required>
                    <label class="form-check-label" for="inlineRadio1">
                        @if($res->idrespon == '1')
                        <img src="{{asset('images/smile.png')}}" width="50" height="50" title="{{$res->respon_name}}" alt="{{$res->respon_name}}" />
                        @elseif($res->idrespon == '2')
                        <img src="{{asset('images/neutral.png')}}"  width="50" height="50" title="{{$res->respon_name}}" alt="{{$res->respon_name}}" />
                        @elseif($res->idrespon == '3')
                        <img src="{{asset('images/bad.png')}}"  width="50" height="50" title="{{$res->respon_name}}" alt="{{$res->respon_name}}" />
                        @endif
                    </label>
                  </div>
                @endforeach
            </div>
        </div>
        {{-- <div class="form-group">
            <label>Tempoh Masa Diambil</label>
            <div class="form-check">
                @foreach($respon_masa as $masa)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="respon_masa" id="idlevel" value="{{$masa->idrespon}}" required>
                    <label class="form-check-label" for="inlineRadio1">
                        @if($masa->idrespon == '1')
                        <img src="{{asset('images/smile.png')}}" width="50" height="50" title="{{$masa->respon_masa}}" alt="{{$masa->respon_masa}}" />
                        @elseif($masa->idrespon == '2')
                        <img src="{{asset('images/neutral.png')}}"  width="50" height="50" title="{{$masa->respon_masa}}" alt="{{$masa->respon_masa}}" />
                        @elseif($masa->idrespon == '3')
                        <img src="{{asset('images/bad.png')}}"  width="50" height="50" title="{{$masa->respon_masa}}" alt="{{$masa->respon_masa}}" />
                        @endif
                    </label>
                  </div>
                @endforeach
            </div>
        </div> --}}
        <div class="form-group">
            <label for="">Cadangan/Penambahbaikan</label>
            <input type="text" name="catatan" class="form-control" placeholder="" >
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="{{route('login')}}">Login</a>
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
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>

</body>
</html>
