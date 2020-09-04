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
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Profil Pengguna</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @include('flash-message')
                    <div class="row">
                        <table id="example2" class="table table-hover">
                            <tbody>
                                @foreach($profil as $pro)
                                <tr>
                                  <td>Nama</td>
                                  <td> :</td>
                                  <td>{{$pro->nama}}</td>
                                </tr>
                                <tr>
                                    <td>No KP/ ID Pengguna</td>
                                    <td> :</td>
                                    <td>{{$pro->no_kp}}</td>
                                  </tr>
                                  <tr>
                                    <td>No. Telefon/HP</td>
                                    <td> :</td>
                                    <td>{{$pro->notel}}</td>
                                  </tr>
                                  <tr>
                                    <td>Email</td>
                                    <td> :</td>
                                    <td>{{$pro->email}}</td>
                                  </tr>
                                  <tr>
                                    <td>Jawatan</td>
                                    <td> :</td>
                                    <td>{{$pro->jawatan}}</td>
                                  </tr>
                                  <tr>
                                    <td>Jabatan</td>
                                    <td> :</td>
                                    <td>{{$pro->jabatan}}</td>
                                  </tr>
                            </tfoot>
                          </table>
                        <a href="{{ URL::to('p_edit_profil/'.$pro->idpengguna)}}">
                            <button class="btn btn-block btn-warning">
                                Kemaskini Profil
                            </button>
                        </a>
                        @endforeach
                    </div>
                </div>
                <!-- /.card-body -->
                </div>
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
