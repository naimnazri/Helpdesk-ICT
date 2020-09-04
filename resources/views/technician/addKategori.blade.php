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
            <h1 class="m-0 text-dark">Permohonan Aduan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('t_home')}}">Home</a></li>
              <li class="breadcrumb-item active">Tambah Kategori</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        @include('flash-message')
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header bg-primary">
                    <h3 class="card-title">Senarai Kategori </h3><br>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                      <div class="card-body">
                        <table id="tableAduan" class="table table-bordered table-hover">
                            <thead class="text-center bg-primary">
                                <tr>
                                    <th>Bil</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php $skipped = $kategori->firstItem() - 1; ?>
                                @foreach($kategori as $kat)
                                <tr>
                                    <td>{{ $loop->iteration + $skipped}}</td>
                                    <td>{{$kat->kategori}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $kategori->links() !!}
                      </div>

                    </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header bg-success">
                    <h3 class="card-title">Tambah Kategori </h3><br>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <form role="form" method="POST" action="{{ route('technician.storeKategori')}}" enctype="multipart/form-data">
                          @csrf
                        <div class="card-body">

                           <div class="form-group">
                             <label for="idpengguna">Kategori</span></label>
                             <input type="text" name="kategori" class="form-control" id="kategori" required>
                           </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                          <button type="submit" class="btn btn-success">Tambah</button>
                        </div>
                      </form>
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


</body>
</html>
