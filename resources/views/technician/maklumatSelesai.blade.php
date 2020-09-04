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
                <li class="breadcrumb-item"><a href="{{url('t_home')}}">Dashboard</a></li>
                @if($aduan->idstatus == 4 )
                <li class="breadcrumb-item"><a href="{{url('/t_aduanproses')}}">Aduan Dalam Tindakan</a></li>
                @elseif($aduan->idstatus == 9)
                <li class="breadcrumb-item"><a href="{{url('/t_aduanproses')}}">Aduan Dalam Tindakan</a></li>
                @elseif($aduan->idstatus == 3)
                <li class="breadcrumb-item"><a href="{{url('/t_aduanselesai')}}">Aduan Selesai</a></li>
                @elseif($aduan->idstatus == 10)
                <li class="breadcrumb-item"><a href="{{url('/t_aduantolak')}}">Aduan Tolak</a></li>
                @endif
                <li class="breadcrumb-item active">Maklumat Aduan</li>
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
                        <h3 class="card-title">Maklumat Pengadu</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <dd class="col-sm-4">Nama</dd>
                            <dt class="col-sm-8">{{$aduan->nama}}</dt>
                            <dd class="col-sm-4">No. Telefon</dd>
                            <dt class="col-sm-8">{{$aduan->notel}}</dt>
                            <dd class="col-sm-4">Jawatan</dd>
                            <dt class="col-sm-8">{{$aduan->jawatan}}</dt>
                            <dd class="col-sm-4">Jabatan</dd>
                            <dt class="col-sm-8">{{$aduan->jabatan}}</dt>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Maklumat Aduan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <dd class="col-sm-4">Status Aduan </dd>

                                @if($aduan->idstatus == 1)
                                <dt class="col-sm-8 text-primary text-bold">{{$aduan->nama_status}}</dt>
                                @elseif(($aduan->idstatus == 4) || ($aduan->idstatus == 9))
                                <dt class="col-sm-8 text-warning text-bold">{{$aduan->nama_status}}</dt>
                                @elseif($aduan->idstatus == 3)
                                <dt class="col-sm-8 text-success text-bold">{{$aduan->nama_status}}</dt>
                                @elseif($aduan->idstatus == 10)
                                <dt class="col-sm-8 text-danger text-bold">{{$aduan->nama_status}}</dt>
                                @endif

                            <dd class="col-sm-4">No. Aduan</dd>
                            <dt class="col-sm-8">{{$aduan->no_aduan}}</dt>
                            @if(($aduan->idstatus == '9') || ($aduan->idstatus == '3'))
                            <dd class="col-sm-4">Kategori</dd>
                            <dt class="col-sm-8">{{$aduan->kategori}}</dt>
                            @else
                            @endif
                            <dd class="col-sm-4">Keterangan Aduan</dd>
                            <dt class="col-sm-8">{{$aduan->masalah}}</dt>
                            <dd class="col-sm-4">Mesej Ralat (jika ada)</dd>
                            <dt class="col-sm-8">
                            @if($aduan->errormsg == '')
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
                            @if($aduan->idstatus == '1')
                            @else
                            <dd class="col-sm-4">Tindakan Pegawai</dd>
                            <dt class="col-sm-8">{{$pegawai->nama}}</dt>
                            @endif
                            <dd class="col-sm-4">Tarikh Aduan</dd>
                            <dt class="col-sm-8">{{ date('d-m-Y', strtotime($aduan->tarikh_aduan))}}</dt>
                            <dd class="col-sm-4">Masa Aduan</dd>
                            <dt class="col-sm-8">{{$aduan->masa_aduan}}</dt>
                            <dd  class="col-sm-4">Bilangan Hari</dd>
                            <dt class="col-sm-8">{{$diff = Carbon\Carbon::parse($aduan->tarikh_aduan)->diffInDays(Carbon\Carbon::parse($aduan->tarikh_tindakan))}} Hari</dt>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($aduan->idstatus == '1')
        @elseif($aduan->idstatus == '4')
        @elseif($aduan->idstatus == '10')
        @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">Maklumat Tambahan Aduan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($aduan->jenis_kategori == '')
                            @else
                            <dd class="col-sm-4">Perkakasan</dd>
                            <dt class="col-sm-8">{{$aduan->subkat ?? $aduan->jenis_kategori}}</dt>
                            @endif
                            <dd class="col-sm-4">Model</dd>
                            <dt class="col-sm-8">{{$aduan->model_name ?? $aduan->model  }}</dt>
                            {{-- <dd class="col-sm-4">No Inventori</dd>
                            <dt class="col-sm-8">{{$aduan->noinventori}}</dt> --}}
                            <dd class="col-sm-4">No Siri</dd>
                            <dt class="col-sm-8">{{$aduan->nosiri}}</dt>
                            {{-- <dd class="col-sm-4">Tindakan Pegawai</dd>
                            <dt class="col-sm-8">{{$pegawai->nama}}</dt> --}}
                            <dd class="col-sm-4">Tarikh Tindakan</dd>
                            @if($aduan->tarikh_tindakan == '')
                            <dt class="col-sm-8"></dt>
                            @else
                            <dt class="col-sm-8">{{ date('d-m-Y', strtotime($aduan->tarikh_tindakan))}}</dt>
                            @endif
                            <dd class="col-sm-4">Masa Tindakan</dd>
                            <dt class="col-sm-8">{{$aduan->masa_tindakan}}</dt>
                           {{--  <dd class="col-sm-4">Tarikh Onsite</dd>
                            @if($aduan->tarikh_tindakan == '')
                            <dt class="col-sm-8"></dt>
                            @else
                            <dt class="col-sm-8">{{ date('d-m-Y', strtotime($aduan->tarikh_onsite))}}</dt>
                            @endif
                            <dd class="col-sm-4">Masa Onsite</dd>
                            <dt class="col-sm-8">{{$aduan->masa_onsite}}</dt> --}}
                            <dd class="col-sm-4">Perlu Onsite</dd>
                            @if(($aduan->id_onsite == '1') || ($aduan->id_onsite == 'Y') )
                            <dt class="col-sm-8">Ya</dt>
                            @elseif(($aduan->id_onsite == '2') || ($aduan->id_onsite == 'T'))
                            <dt class="col-sm-8">Tidak</dt>
                            @else
                            <dt class="col-sm-8"></dt>
                            @endif
                            <dd class="col-sm-4">Gantian Perkakasan</dd>
                            @if(($aduan->id_ganti == '1') || ($aduan->id_ganti == 'Y'))
                            <dt class="col-sm-8">Ya</dt>
                            @elseif(($aduan->id_ganti == '2') || ($aduan->id_ganti == 'T'))
                            <dt class="col-sm-8">Tidak</dt>
                            @else
                            <dt class="col-sm-8"></dt>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($aduan->idstatus == '10')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h3 class="card-title">Maklumat Aduan Tolak</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <dd class="col-sm-4">Tindakan Pegawai</dd>
                            <dt class="col-sm-8">{{$pegawai->nama}}</dt>
                            <dd class="col-sm-4">Maklumbalas</dd>
                            <dt class="col-sm-8">{{$aduan->maklumbalas}}</dt>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Kronologi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <dl class="row ">
                                <dd class="col-sm-3 text-center border bg-light" >Maklumat</dd>
                                <dd class="col-sm-3 text-center border bg-light">Individu</dd>
                                <dd class="col-sm-3 text-center border bg-light">Peranan</dd>
                                <dd class="col-sm-3 text-center border bg-light">Tarikh</dd>

                                @foreach($kronologi as $kro)
                                    <dd class="col-sm-3 text-center ">{{ $kro->nama_status}}</dd>
                                    <dd class="col-sm-3 text-center ">{{ $kro->nama}}</dd>
                                    <dd class="col-sm-3 text-center ">{{ $kro->level}}</dd>
                                    <dd class="col-sm-3 text-center ">{{ $kro->tarikh_masa_skrg}}</dd>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
