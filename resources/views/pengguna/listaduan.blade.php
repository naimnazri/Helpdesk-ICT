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
            <h1 class="m-0 text-dark">Halaman Utama Pengadu</h1>
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
        @include('flash-message')
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Senarai Aduan Anda</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead class="thead-light ">
                    <tr>
                      <th>No Aduan</th>
                      <th>Masalah</th>
                      <th>Tarikh Aduan</th>
                      <th>Status</th>
                      <th>Tindakan</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $skipped = $aduan->firstItem() - 1; ?>
                    @foreach($aduan as $adu)
                    <tr>
                      <td>{{$adu->no_aduan}}</td>
                      <td>{{wordwrap($adu->masalah,20,"\n",TRUE)}}</td>
                      <td>{{ date('d-m-Y', strtotime($adu->tarikh_aduan)) }}</td>
                      <td>  @if($adu->idstatus == '1')
                                <div class="text-bold text-primary">{{ $adu->nama_status }}</div>
                            @elseif($adu->idstatus == '3')
                                <div class="text-bold text-success">{{ $adu->nama_status }}</div>
                            @elseif($adu->idstatus == '10')
                                <div class="text-bold text-danger">{{ $adu->nama_status }}</div>
                            @else
                                <div class="text-bold text-warning">{{ $adu->nama_status }}</div>
                            @endif
                      </td>
                      <td>
                        <a type="button" class="btn btn-block btn-success "
                           href="{{ URL::to('p_detailaduan/'. $adu->no_aduan)}}">
                            Maklumat Aduan
                        </a>
                      </td>
                    </tr>
                    @endforeach
                    </tr>
                  </table>
                  <br>

                   <div class="col-12 d-flex justify-content-center">
                        {{ $aduan->links() }}
                    </div>
                </div>
               {{--  <div class="card-footer">
                    <a type="button" class="btn btn-info"
                           href="{{ url('p_tambahaduan')}}">
                            Daftar Aduan
                    </a>
                </div> --}}
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


<script>
    $(function () {
      $("#example2").DataTable({
        "responsive": true,
        "autoWidth": true,
        "searching": true,
        "paging": true;


        "ordering": true,

      });

    });
  </script>



</body>
</html>
