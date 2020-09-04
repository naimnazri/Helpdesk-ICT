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
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                      <h3 class="card-title">Maklumat Aduan</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <dl class="row">
                        <dd class="col-sm-4">No. Aduan</dd>
                        <dt class="col-sm-8">{{$aduan->no_aduan}}</dt>
                        <dd class="col-sm-4">Masalah</dd>
                        <dt class="col-sm-8">{{$aduan->masalah}}</dt>
                        <dd class="col-sm-4">Mesej Ralat</dd>
                        <dt class="col-sm-8">@if($aduan->errormsg == '')
                                            Tiada
                                             @else
                                                {{$aduan->errormsg}}
                                            @endif
                        </dt>
                        <dd class="col-sm-4">Lampiran</dd>
                            <dt class="col-sm-8">
                            @if($aduan->image == '')
                            Tiada
                            @else
                            <img id="gambar" src="{{asset('storage/'.$aduan->image.'')}}" width="50" height="50">
                            @endif
                        </dt>
                        <dd class="col-sm-4">Status</dd>
                        <dt class="col-sm-8"> @if($aduan->idstatus == '1')
                                                    <div class="text-primary"> {{$aduan->nama_status}}
                                            @elseif($aduan->idstatus == '4')
                                                    <div class="text-warning"> {{$aduan->nama_status}}
                                            @elseif($aduan->idstatus == '9')
                                                        <div class="text-warning"> {{$aduan->nama_status}}
                                            @elseif($aduan->idstatus == '3')
                                                    <div class="text-success"> {{$aduan->nama_status}}
                                            @elseif($aduan->idstatus == '10')
                                                        <div class="text-danger"> {{$aduan->nama_status}}
                                            @endif   </dt>
                        <dd class="col-sm-4">Tarikh Aduan</dd>
                        <dt class="col-sm-8">{{ date('d-m-Y', strtotime($aduan->tarikh_aduan))}}</dt>
                        <dd class="col-sm-4">Masa Aduan</dd>
                        <dt class="col-sm-8">{{$aduan->masa_aduan}}</dt>

                    </dl>
                    </div>
                    <!-- /.card-body -->
                    </div>
            </div>
        </div>

        @if(($aduan->idstatus == '1') && ($aduan->idstatus == '10'))

        @elseif(($aduan->idstatus == '3') && ($aduan->idstatus == '4') && ($aduan->idstatus == '9'))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Tindakan Aduan</h3><br>
                    </div>
                        <div class="card-body">
                                <dl class="row">
                                <dd class="col-sm-4">Kategori Aduan {{$aduan->tarikh_tindakan}}</dd>
                                <dt class="col-sm-8">{{$aduan->kategori}}</dt>
                                <dd class="col-sm-4">Jenis</dd>
                                <dt class="col-sm-8">{{$aduan->jenis_kategori}}</dt>
                                <dd class="col-sm-4">Model</dd>
                                <dt class="col-sm-8">{{$aduan->model}}</dt>
                                <dd class="col-sm-4">No. Inventori</dd>
                                <dt class="col-sm-8">{{$aduan->noinventori}}</dt>
                                <dd class="col-sm-4">Nombor Siri</dd>
                                <dt class="col-sm-8">{{$aduan->nosiri}}</dt>
                                <dd class="col-sm-4">Catatan</dd>
                                <dt class="col-sm-8">{{$aduan->maklumbalas}}</dt>
                                <dd class="col-sm-4">Tarikh Tindakan</dd>
                                <dt class="col-sm-8">{{date('d-m-Y', strtotime($aduan->tarikh_tindakan))}}</dt>
                                <dd class="col-sm-4">Masa Tindakan</dd>
                                <dt class="col-sm-8">{{$aduan->masa_tindakan}}</dt>
                            </div>

                        <!-- /.card-body -->
                    </div>
            </div>
        </div>
        @endif

        @if($aduan->no_aduan_feedback === null)
        @else
        @if($aduan->idstatus == '3')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Maklum Balas Pengadu</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <dd class="col-sm-4">Maklum Balas Pengadu</dd>
                            <dt class="col-sm-8">{{$aduan->respon_name}}</dt>
                            {{-- <dd class="col-sm-4">Tempoh Masa Diambil</dd>
                            <dt class="col-sm-8">{{$aduan->respon_masa}}</dt> --}}
                            <dd class="col-sm-4">Komen</dd>
                            <dt class="col-sm-8">
                                @if($aduan->catatan == '')
                                    Tiada
                                @else
                                    {{$aduan->catatan}}
                                @endif
                            </dt>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        @endif
        @endif

<!-- The Modal Gambar -->
<div id="gambar1" class="gambar1">
    <span class="close1">×</span>
    <img class="gambar1-content" id="img01">
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
