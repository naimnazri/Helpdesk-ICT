<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HELPDESK ICT | DAFTAR PENGGUNA</title>
  <link rel = "icon" href="{{asset('images/penang.png')}}" type="image/x-icon">
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <script src="jquery.js"></script>
  <script src="parsley.min.js"></script>
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
input.parsley-success,
select.parsley-success,
textarea.parsley-success {
  color: #468847;
  background-color: #DFF0D8;
  border: 1px solid #D6E9C6;
}

input.parsley-error,
select.parsley-error,
textarea.parsley-error {
  color: #B94A48;
  background-color: #F2DEDE;
  border: 1px solid #EED3D7;
}

.parsley-errors-list {
  margin: 2px 0 3px;
  padding: 0;
  list-style-type: none;
  font-size: 0.9em;
  line-height: 0.9em;
  opacity: 0;
  color: #B94A48;

  transition: all .3s ease-in;
  -o-transition: all .3s ease-in;
  -moz-transition: all .3s ease-in;
  -webkit-transition: all .3s ease-in;
}

.parsley-errors-list.filled {
  opacity: 1;
}
</style>
<body class="hold-transition register-page" style="background-image: url('{{asset('images/test2.png')}}');">

<div class="register-box">
    <div class="row">
        <img src="{{asset('images/penang.png')}}" class="left" >
    </div>
  <div class="register-logo">
  <a href="{{url('/')}}" class="text-black "><b>HELPDESK ICT</b></a>
  </div>
  @include('flash-message')
  <div class="card">
    <div class="card-body register-card-body">
      <h5 class="login-box-msg">Daftar Pengguna Baru </h5>

      <div class="text-danger text-bold text-center pb-3">
        <span>Katalaluan boleh didapati dalam emel yang didaftar setelah akaun diaktifkan</span>
    </div>
      <form action="{{url('store_daftar')}}" method="post" id="validate_form">
        @csrf
        <input name="idlevel" type="text" value="2" hidden>
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="no_kp" placeholder="No KP" required
            maxlength="12" data-parsley-length="[8,16]" data-parsley-trigger="keyup">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-id-card"></span>
              </div>
            </div>
            @error('no_kp')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

       {{--  <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" placeholder="KataLaluan" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password_confirmation" placeholder="Taip Semula Katalaluan" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
        </div>--}}
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="nama" placeholder="Nama" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
            @error('nama')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="jawatan" placeholder="Jawatan" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-briefcase"></span>
              </div>
            </div>
            @error('jawatan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-3">
            <select type="text" class="form-control dynamic" data-dependent="bahagian" name="jabatan" id="idjab" required>
                <option value="" name="jabatan" >Sila Pilih Jabatan</option>
                @foreach($jabatan as $jab)
                <option value="{{$jab->idjab}}">{{$jab->jabatan}}</option>
                @endforeach
            </select>
            @error('jabatan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-3">
            <select type="text" class="form-control dynamic" name="bahagian" id="bahagian" required>
                <option  value="">Sila Pilih Bahagian </option>

                {{-- @foreach($bahagian as $bah)
                <option value="{{$bah->idbahagian}}" name="bahagian" >{{$bah->bahagian}}</option>
                @endforeach --}}
            </select>
            @error('bahagian')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        {{ csrf_field() }}
        <div class="input-group mb-3">
            <input type="text" name="no_ofis" class="form-control" placeholder="04650XXXX" required
            minlength="9" maxlength="13">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-phone"></span>
              </div>
            </div>
            @error('notel')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="text" name="notel" class="form-control" placeholder="017345XXXX" required
            minlength="10" maxlength="13">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-phone"></span>
              </div>
            </div>
            @error('notel')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="text" name="email" class="form-control @error('name') is-invalid
            @enderror" placeholder="E-mel" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="far fa-envelope"></span>
              </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="col ml-5 mr-5">
          <button type="submit" name="submit" class="btn btn-primary btn-block ">Daftar</button>
        </div>
    </div>
    <div class="row">
        <div class="col  text-center">
            <a class="btn btn-link " href="{{ route('login') }}">
                Home
            </a>
        </div>
    </div>
    <br>
  </div>

      </form>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>

{{-- <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {

        $('#jabatan').on('change',function(e) {

         var idjab = e.target.value;

         $.ajax({

               url:"{{ route('bahagian') }}",
               type:"POST",
               data: {
                   idjab: idjab
                },

               success:function (data) {

                $('#subbahagian').empty();

                $.each(data.idbah[0].idbah,function(index,subcategory){

                    $('#subbahagian').append('<option value="'+subcategory.idbahagian+'">'+subcategory.bahagian+'</option>');
                })

               }
           })
        });

    });
</script> --}}

<script>
    $(document).ready(function(){

     $('.dynamic').change(function(){
      if($(this).val() != '')
      {
       var select = $(this).attr("id");
       var value = $(this).val();
       var dependent = $(this).data('dependent');
       var _token = $('input[name="_token"]').val();
       $.ajax({
        url:"{{ route('daftarPengguna.fetch') }}",
        method:"POST",
        data:{select:select, value:value, _token:_token, dependent:dependent},
        success:function(result)
        {
         $('#'+dependent).html(result);
        }
       })
      }
     });
     $('#idjab').change(function(){
      $('#bahagian').val('');
     });
    });
    </script>

<script>
    $(document).ready(function(){

     $('#validate_form').parsley();

    });
    </script>

</body>
</html>
