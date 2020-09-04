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
          <h1 class="m-0 text-dark">Pengurusan Aduan </h1>
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
                  <h3 class="card-title">Senarai Aduan</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead class="thead-light ">
                    <tr>
                      <th>Bil</th>
                      <th>Nama Pengadu / No KP</th>
                      <th>Jabatan</th>
                      <th>Kategori dan Keterangan</th>
                      <th>Tarikh/No Aduan</th>
                      <th>Status</th>
                      <th>Tindakan</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $skipped = $aduan->firstItem() - 1; ?>
                    @foreach($aduan as $adu)
                    <tr>
                    <td>{{ $loop->iteration + $skipped}}</td>
                      <td><p>{{ $adu->nama }}</p>
                          <p>{{ $adu->id_pengadu }}</p>
                     </td>
                      <td>{{ $adu->jabatan }}</td>
                      <td><p>{{ $adu->kategori }}</p>
                          <p>{{ $adu->jenis_kategori }}</p>
                          <p>{{ $adu->masalah }}</p>
                      </td>
                      <td><p>{{ $adu->tarikh_aduan }}</p>
                          <p>{{ $adu->no_aduan }}</p>
                      </td>
                      <td>{{ $adu->nama_status }}</td>
                      <td>
                        <button type="button" title="Agihan Aduan" class="btn btn-block btn-success btn-sm ">
                            <i class="fas fa-edit"></i>
                        </button>

                        <a type="button" title="Kronologi Aduan" class="btn btn-block btn-primary btn-sm"
                           href="{{ url('kronologi')}}">
                            <i class="far fa-file-alt"></i>
                        </a>

                        <button type="button" title="Padam Aduan" class="btn btn-block btn-danger btn-sm">
                            <i class="far fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>

                    @endforeach
                    </tfoot>
                  </table>
                  <br>
                  {!! $aduan->links() !!}
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
