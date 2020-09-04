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
                    <tr>
                      <td>1</td>
                      <td><p>Muhamad Naim Bin Mohd Nazri</p>
                          <p>970827025335</p>
                     </td>
                      <td>PEJABAT DAERAH DAN TANAH DAERAH SEBERANG PERAI UTARA</td>
                      <td>Perkakasan Mouse (tetikus) tidak dapat berfungsi dengan baik</td>
                      <td><p>2020-03-17</p>
                          <p>2003-4932</p>
                      </td>
                      <td>Agihan (Tindakan BTMKN)</td>
                      <td>
                        <a type="button" title="Kronologi Aduan" class="btn btn-block btn-success btn-sm"
                        href="{{ url('t_editaduan')}}">
                         <i class="fas fa-edit"></i>
                        </a>

                        <a type="button" title="Kronologi Aduan" class="btn btn-block btn-primary btn-sm"
                           href="{{ url('t_kronologi')}}">
                            <i class="far fa-file-alt"></i>
                        </a>

                        <button type="button" title="Padam Aduan" class="btn btn-block btn-danger btn-sm">
                            <i class="far fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>

                    </tr>
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
