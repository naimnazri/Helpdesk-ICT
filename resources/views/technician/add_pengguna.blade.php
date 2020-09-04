<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_technician.navbar')

    <!-- Main Sidebar Container -->
    @include('layout_technician.sidebar') --}}


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengurusan Aduan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profil Pengguna</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Tambah Pengguna</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" method="POST" action="{{ route('t_pengguna.store')}}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="idpengguna">No KP</label>
                            <input type="text" name="idpengguna" class="form-control" id="idpengguna" value="" placeholder="97082702XXXX" required
                            maxlength="12" minlength="12">
                        </div>
                        <div class="form-group">
                            <label for="katalaluan">Katalaluan</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan Kata Laluan" required>
                        </div>
                        <div class="form-group">
                            <label for="katalaluan">Taip Semula Katalaluan</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password" placeholder="Masukkan Kata Laluan" required>
                        </div>
                        <div class="form-group">
                            <label>Level Pengguna</label>
                            <div class="form-check">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="4" required>
                                    <label class="form-check-label" for="inlineRadio1">Pengguna Jabatan</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="6" required>
                                    <label class="form-check-label" for="inlineRadio2">Teknikal Jabatan</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="2" required>
                                    <label class="form-check-label" for="inlineRadio1">Teknikal BTMKN</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="1" required>
                                    <label class="form-check-label" for="inlineRadio2">Penyelaras BTMKN</label>
                                  </div>
                            </div>
                        </div>
                        <div class="form-group">
                          <label for="nama">Nama</label>
                          <input type="text" name="nama" class="form-control" id="nama" placeholder="Masukkan Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="jawatan">Jawatan</label>
                            <input type="text" name="jawatan" class="form-control" id="jawatan" placeholder="Masukkan Jawatan" required>
                        </div>
                        <div class="form-group">
                            <label for="Jabatan">Jabatan</label>
                            <select class="form-control dynamic" data-dependent="bahagian" name="idjab" id="idjab" style="width: 100%;" required>
                                <option selected="selected">Sila Pilih Jabatan</option>
                                @foreach($jabatan as $jab)
                                <option name="idjab" value="{{$jab->idjab}}">{{$jab->jabatan}}</option>
                                @endforeach
                              </select>
                        </div>
                        <div class="form-group">
                            <label>Bahagian/Unit</label>
                            <select class="form-control dynamic"  id="bahagian" name="idbahagian" style="width: 100%;" required>
                              <option value="" >Sila Pilih Bahagian</option>
                              {{-- @foreach($bahagian as $bah)
                                <option name="idbahagian" value="{{$bah->idbahagian}}">{{$bah->bahagian}}</option>
                              @endforeach --}}
                            </select>
                          </div>
                          {{ csrf_field() }}
                          <div class="form-group">
                            <label for="notel">No Pejabat</label>
                            <input type="text" name="no_ofis" class="form-control" id="no_ofis" placeholder=" Masukkan No Pejabat" required>
                            <span class="text-danger">Sila masukkan nombor tanpa '-'</span>
                        </div>
                        <div class="form-group">
                            <label for="notel">No Telefon</label>
                            <input type="text" name="notel" class="form-control" id="notel" placeholder=" Masukkan No Telefon" required>
                            <span class="text-danger">Sila masukkan nombor tanpa '-'</span>

                        </div>
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan Email" required>

                        </div>
                        <div class="form-group">
                            <label>Aktif</label>
                            <div class="form-check">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="aktif" id="idlevel" value="1" required>
                                    <label class="form-check-label" for="inlineRadio1">Ya</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="aktif" id="idlevel" value="0" required>
                                    <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                  </div>
                            </div>
                        </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
              <!-- /.card -->
        </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@include('layouts.footer')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

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
        url:"{{ route('t_pengguna.fetch') }}",
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

</body>
</html>
