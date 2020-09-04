<!DOCTYPE html>
<html>
@include('layouts.head')


<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
   {{--  @include('layout_admin.navbar') --}}

  {{-- <!-- Main Sidebar Container -->
  @include('layout_admin.sidebar') --}}


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
        @include('flash-message')
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-primary">
                    <h3 class="card-title">
                        Maklumat Pengadu
                    </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <dl class="row">
                        <dd class="col-sm-4">Nama</dd>
                        <dt class="col-sm-8">{{$aduan->nama}}</dt>
                        <dd class="col-sm-4">Jawatan</dd>
                        <dt class="col-sm-8">{{$aduan->jawatan}}</dt>
                        <dd class="col-sm-4">No. Telefon</dd>
                        <dt class="col-sm-8">{{$aduan->notel}}</dt>
                        <dd class="col-sm-4">Jabatan</dd>
                        <dt class="col-sm-8">{{$aduan->jabatan}}</dt>
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

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-info">
                    <h3 class="card-title">
                        Maklumat Aduan
                    </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <dl class="row">
                        <dd class="col-sm-4">No. Aduan</dd>
                        <dt class="col-sm-8">{{$aduan->no_aduan}}</dt>
                        <dd class="col-sm-4">Masalah</dd>
                        <dt class="col-sm-8">{{$aduan->masalah}}</dt>
                        <dd class="col-sm-4">Mesej Ralat</dd>
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
                        @if($aduan->nama !== $tinda)
                        <dd class="col-sm-4">Tindakan Pegawai</dd>
                        <dt class="col-sm-8">{{$tinda}}</dt>
                        @else
                        @endif
                    </dl>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>

        @if(Auth::user()->idlevel == 8)
        @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success">
                        <div class="card-title">
                            Agihan Aduan
                        </div>
                    </div>
                            <div class="card-body">
                                <table id="example2" class="table">
                                    <tbody>
                                    <form method="post" action="{{ url('agihan', $aduan->no_aduan)}}">
                                        @csrf
                                        <input name="id_pengadu" value="{{$aduan->id_pengadu}}" hidden>
                                        <input name="tindakan_pegawai" value="{{Auth::user()->nama}}" hidden>
                                        <input name="masalah" value="{{$aduan->masalah}}" hidden>
                                        {{-- <tr>
                                            <td>Kategori</td>
                                            <td>
                                              <select name="idkategori" class="form-control select2" style="width: 100%;">
                                                  <option name="idkategori" selected="selected">Sila Pilih Kategori</option>
                                                  @foreach($kategori as $kate)
                                                      <option name="idkategori" value="{{$kate->idkategori}}">{{$kate->kategori}}</option>
                                                  @endforeach
                                              </select>
                                            </td>
                                        </tr> --}}
                                        <tr>
                                        <td>Agihan Kepada</td>
                                        <td>
                                            <select name="id_pengguna" class="form-control select2" style="width: 100%;" required>
                                                <option name="id_pengguna" selected="selected">Sila Pilih Pegawai</option>
                                                @foreach($pegawai as $peg)
                                                    <option name="id_pengguna" value="{{$peg->idpengguna}}">{{$peg->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>Catatan</td>
                                        <td>
                                            <textarea name="reason_agihan" class="form-control" rows="3" placeholder="" required></textarea>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <button type="submit" class="btn btn-primary">Hantar</button>
                                            <a type="button" class="btn btn-info" href="{{ url('senaraiaduan') }}">
                                            Kembali
                                            </a>
                                        </form>
                                            {{-- <a type="button" class="btn btn-danger" href="{{ route('admin.tolak', $aduan->no_aduan)}}">
                                                Tolak
                                            </a> --}}
                                            <button type="button" id="myBtn" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger">
                                                Tolak
                                            </button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                  </table>
                            </div>
                        </div>
            </div>
        </div>
        @endif


        <div class="modal fade" id="myModal">
        <form method="POST" action="{{ url('reject/'.$aduan->no_aduan)}}">
        @csrf
            <div class="modal-dialog">
              <div class="modal-content ">
                <div class="modal-header bg-danger">
                <h4 class="modal-title">Tolak Aduan: {{$aduan->no_aduan}}</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input name="id_pengadu" value="{{$aduan->id_pengadu}}" hidden>
                    <input name="id_pengguna" value="{{Auth::user()->no_kp}}" hidden>
                    <input name="nama" value="{{Auth::user()->nama}}" hidden>
                    <div class="form-group">
                        <strong>Catatan :</strong>
                        <textarea class="form-control" name="maklumbalas" style="height: 50px">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Kembali</button>
                  <button type="submit" class="btn btn-danger">Hantar</button>
                </div>
              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </form>
          </div>

          <!-- The Modal Gambar -->
    <div id="gambar1" class="gambar1">
        <span class="close1">×</span>
        <img class="gambar1-content" id="img01">
    </div>

</body>

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
    $(document).ready(function(){
      $("#myBtn").click(function(){
        $("#myModal").modal();
      });
    });
    </script>

</body>
</html>
