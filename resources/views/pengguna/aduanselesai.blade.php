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
            <h1 class="m-0 text-dark">Permohonan Aduan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
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
                  <h3 class="card-title">Senarai Aduan Selesai Anda</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead class="thead-light ">
                    <tr>
                        <th>No Aduan</th>
                        <th>Masalah</th>
                        <th>Tarikh Aduan</th>
                        {{-- <th>Status</th> --}}
                        <th>Tindakan</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($aduan as $adu)
                    <tr>
                        <td>{{$adu->no_aduan}}</td>
                        <td>{{$adu->masalah}}</td>
                        <td>{{$adu->tarikh_aduan}}</td>
                        {{-- <td>{{$adu->nama_status}}</td> --}}
                      <td>
{{--                         <button type="button" title="Agihan Aduan" class="btn btn-block btn-success btn-sm ">
                            <i class="fas fa-edit"></i>
                        </button> --}}

                        <a type="button" class="btn btn-block btn-primary btn-sm"
                           href="{{ url('p_detailaduan')}}">
                            Maklumat Aduan
                        </a>
                      </td>
                    </tr>
                    @endforeach

                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
                </div>
              </div>
              <!-- /.card -->
        </div>
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
