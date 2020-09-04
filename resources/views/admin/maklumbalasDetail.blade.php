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
                <li class="breadcrumb-item"><a href="{{url('/home')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Detail Kategori</li>
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
                    <div class="card-header bg-primary">
                        <div class="card-title text-bold">
                            Maklum Balas {{$tajuk}} | {{$bulan}} {{$tahun}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tableAduan" class="table table-bordered table-hover">
                            <thead class="thead-light text-center text-bold">
                                <tr>
                                    <th>No. Aduan</th>
                                    <th>Masalah</th>
                                    <th>Jabatan</th>
                                    <th>Tarikh Aduan</th>
                                    <th>Bilangan Hari</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $skipped = $aduan->firstItem() - 1; ?>
                                @foreach($aduan as $adu)
                                <tr>
                                    <td class="text-center">{{$adu->no_aduan}}</td>
                                    <td>{{$adu->masalah}}</td>
                                    <td>{{$adu->jabatan}}</td>
                                    <td>{{ date('d-m-Y', strtotime($adu->tarikh_aduan))}}</td>
                                    <td class="text-center">{{$diff = Carbon\Carbon::parse($adu->tarikh_aduan)->diffInDays(Carbon\Carbon::parse($adu->tarikh_tindakan))}}</td>
                                    <td>
                                        <a type="button" title="Maklumat Aduan" class="btn btn-block btn-info btn-sm"
                                            href="{{ url('maklumatAduan/'.$adu->id)}}">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        {!! $aduan->links() !!}
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


</body>
</html>
