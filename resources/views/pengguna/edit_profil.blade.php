<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layouts.navbar')

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar') --}}


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
        @include('flash-message')
      <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Kemaskini Profil Pengguna</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" method="POST" action="{{ url('p_storeProfil/'.$profil->idpengguna)}}">
                    @csrf
                  <div class="card-body">
                    <div class="form-group">
                        <label for="idpengguna">ID Pengguna</label>
                        <input type="text" name="idpengguna" class="form-control" id="idpengguna" value="{{$profil->no_kp}}" disabled>
                    </div>
                    <div class="form-group">
                      <label for="nama">Nama</label>
                      <input type="text" name="nama" class="form-control" id="nama" value="{{$profil->nama}}">
                    </div>
                    <div class="form-group">
                      <label for="katalaluan">Katalaluan</label>
                      <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="katalaluan">Pengesahan Katalaluan</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password" placeholder="Confirm Password">
                      </div>
                    <div class="form-group">
                        <label for="notel">No Telefon</label>
                        <input type="text" name="notel" class="form-control" id="notel" value="{{$profil->notel}}">
                    </div>
                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="{{$profil->email}}">
                    </div>
                    <div class="form-group">
                        <label for="jawatan">Jawatan</label>
                        <input type="text" name="jawatan" class="form-control" id="jawatan" value="{{$profil->jawatan}}">
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


</body>
</html>
