<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_admin.navbar') --}}

  <!-- Main Sidebar Container -->
 {{--  @include('layout_admin.sidebar') --}}


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0 text-dark">Halaman Utama Penyelaras</h1>
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
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$baru}}</h3>

                <p>Aduan Baru</p>
              </div>
              <div class="icon">
                <i class="fas fa-folder-plus"></i>
              </div>
            <a href="{{ url('aduanbaru') }}" class="small-box-footer">Lihat Aduan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
              <h3>{{$proses}}</h3>

                <p>Dalam Tindakan</p>
              </div>
              <div class="icon">
                <i class="fas fa-tools"></i>
              </div>
              <a href="{{ url('aduanproses') }}" class="small-box-footer">Lihat Aduan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3>{{$pembekal}}</h3>

                <p>Tindakan Pembekal</p>
              </div>
              <div class="icon">
                <i class="fas fa-archive"></i>
              </div>
              <a href="{{ url('aduanpembekal') }}" class="small-box-footer">Lihat Aduan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <!-- ./col -->
          <div class="col-lg col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{$selesai}}</h3>

                <p>Selesai</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-square"></i>
              </div>
              <a href="{{ url('aduanselesai') }}" class="small-box-footer">Lihat Aduan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{$tolak}}</h3>

                <p>Ditolak</p>
              </div>
              <div class="icon">
                <i class="fas fa-trash"></i>
              </div>
              <a href="{{ url('aduantolak') }}" class="small-box-footer">Lihat Aduan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg connectedSortable">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="card-title">Bilangan Aduan Harian  ( {{$tarikh}} )</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="text-center bg-primary">
                            <tr>
                            <th>Nama Juruteknik </th>
                                <th>Bilangan Aduan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tech as $t)

                            <tr >
                                <td>{{$t->nama}}</td>
                                <td class="text-center">
                                    @php $number = 0; @endphp
                                    @foreach($query as $keys => $q)
                                    @if($t->idpengguna == $q->id_pengguna)
                                    {{-- {{ count(explode(",",$q->id_pengguna)) }} --}}
                                    {{-- {{ $keys.=+1 }} --}} @php $number++ @endphp
                                    @endif
                                    @endforeach
                                    {{ $number }}
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
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
