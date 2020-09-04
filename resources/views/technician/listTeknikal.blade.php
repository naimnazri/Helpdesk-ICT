<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_admin.navbar')

  <!-- Main Sidebar Container -->
  @include('layout_admin.sidebar') --}}


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pengurusan Aduan </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('/home')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Senarai Teknikal Jabatan</li>
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
                        <div class="card-title">
                            Senarai Teknikal Jabatan
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tableAduan" class="table table-bordered table-hover">
                            <thead class="text-center bg-primary">
                                <tr>
                                    <th>Bil</th>
                                    <th>Nama</th>
                                    <th>No. Telefon</th>
                                    <th>Email</th>
                                    <th>Jawatan</th>
                                    <th>Jabatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $skipped = $teknikal->firstItem() - 1; ?>
                                @foreach($teknikal as $tek)
                                <tr>
                                    <td>{{ $loop->iteration + $skipped}}</td>
                                    <td>{{$tek->nama}}</td>
                                    <td>{{$tek->notel}}</td>
                                    <td>{{$tek->email}}</td>
                                    <td>{{$tek->jawatan}}</td>
                                    <td>{{$tek->jabatan}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        {!! $teknikal->links() !!}
                    </div>
                </div>
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


{{-- <script>
    $(document).ready(function(){
      $("#kro").click(function(){
        $("#test1").modal();
      });
    });
  </script> --}}

</body>
</html>
