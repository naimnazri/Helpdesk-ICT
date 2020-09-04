<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_admin.navbar') --}}

    {{-- <!-- Main Sidebar Container -->
    @include('layout_admin.sidebar') --}}


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
              <li class="breadcrumb-item active">Kemaskini Pengguna</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        @include('flash-message')
      <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Kemaskini Maklumat Pengguna</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" method="POST" action="{{url('store_detail')}}">
                    @csrf
                  <div class="card-body">
                    <div class="form-group">
                        <label for="idpengguna">No KP</label>
                        <input type="text"  class="form-control" id="idpengguna" value="{{$pengguna->idpengguna}}" disabled>
                        <input type="text" name="idpengguna" class="form-control" id="idpengguna" value="{{$pengguna->idpengguna}}" hidden>
                    </div>
                    <div class="form-group">
                        <label for="katalaluan">Katalaluan</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Kosongkan jika tiada perubahan Kata Laluan">
                    </div>
                    <div class="form-group">
                        <label for="katalaluan">Taip Semula Katalaluan</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password" placeholder="Kosongkan jika tiada perubahan Kata Laluan">
                    </div>
                    <div class="form-group">
                        <label>Level Pengguna</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="4"
                                {{ $pengguna->idlevel == '4' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio1">Pengguna Jabatan</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="6"
                                {{ $pengguna->idlevel == '6' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio2">Teknikal Jabatan</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="2"
                                {{ $pengguna->idlevel == '2' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio1">Teknikal BTMKN</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="idlevel" id="idlevel" value="1"
                                {{ $pengguna->idlevel == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio2">Penyelaras BTMKN</label>
                              </div>
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="nama">Nama</label>
                      <input type="text" name="nama" class="form-control" id="nama" value="{{$pengguna->nama}}" required>
                    </div>
                    <div class="form-group">
                        <label for="jawatan">Jawatan</label>
                        <input type="text" name="jawatan" class="form-control" id="jawatan" value="{{$pengguna->jawatan}}" required>
                    </div>
                    <div class="form-group">
                        <label for="Jabatan">Jabatan</label>
                        <select class="form-control dynamic" data-dependent="bahagian" name="idjab" style="width: 100%;" required>
                            <option value="{{$pengguna->idjab}}">{{$pengguna->jabatan}}</option>
                            @foreach($jabatan as $jab)
                            <option value="{{$jab->idjab}}"
                                >{{$jab->jabatan}}</option>
                            @endforeach
                          </select>
                          {{-- {{ $selectedvalue ?? '' == $jab->idjab ? 'selected="selected"' : '' }} --}}
                    </div>
                    <div class="form-group">
                        <label>Bahagian/Unit</label>
                        <select class="form-control" name="bahagian" id="bahagian" style="width: 100%;" required>

                          <option value="{{$pengguna->idbahagian}}">{{$pengguna->bahagian}}</option>
                           {{--  {{ $selectedvalue ?? '' == $bahagian->idbahagian ? 'selected="selected"' : '' }} --}}
                        </select>
                    </div>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="notel">No Pejabat</label>
                        <input type="text" minlength="9" maxlength="10" name="no_ofis" class="form-control" id="no_ofis" value="{{$pengguna->no_ofis}}" placeholder=" Masukkan No Pejabat" required>
                        <span class="text-danger">Sila masukkan nombor tanpa '-'</span>
                    </div>
                    <div class="form-group">
                        <label for="notel">No Telefon</label>
                        <input type="text" minlength="10" maxlength="13" name="notel" class="form-control" id="notel" value="{{$pengguna->notel}}" placeholder=" Masukkan No Telefon" required>
                        <span class="text-danger">Sila masukkan nombor tanpa '-'</span>
                    </div>
                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="{{$pengguna->email}}" placeholder="Masukkan Email" required>
                    </div>
                    <div class="form-group">
                        <label>Aktif</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="aktif" id="aktif" value="1"
                                {{ $pengguna->id == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio1">Ya</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="aktif" id="aktif" value="0"
                                {{ $pengguna->id == '0' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="inlineRadio2">Tidak</label>
                              </div>
                        </div>
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer">
                    <button type="submit" class="btn btn-success">Hantar</button>

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
        url:"{{ route('admin.listBah') }}",
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
