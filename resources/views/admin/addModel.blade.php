<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_admin.navbar') --}}

  <!-- Main Sidebar Container -->
  {{-- @include('layout_admin.sidebar') --}}


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
              <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
              <li class="breadcrumb-item active">Tambah Model</li>
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
                    <h3 class="card-title">Senarai Model </h3><br>
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
                                <?php $skipped = $model->firstItem() - 1; ?>
                                @foreach($model as $m)
                                <tr>
                                    <td>{{ $loop->iteration + $skipped}}</td>
                                    <td>{{$m->model_name}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $model->links() !!}
                      </div>

                    </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header bg-success">
                    <h3 class="card-title">Tambah Model </h3><br>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <form role="form" method="POST" action="{{ route('admin.storeModel')}}" enctype="multipart/form-data">
                          @csrf
                        <div class="card-body">

                           <div class="form-group">
                             <label for="idpengguna">Model</span></label>
                             <input type="text" name="model" class="form-control" id="model" required>
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
