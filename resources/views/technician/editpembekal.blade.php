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
              <li class="breadcrumb-item"><a href="{{url('/t_home')}}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{url('/t_aduanproses')}}">Aduan Dalam Tindakan</a></li>
              <li class="breadcrumb-item active">Jawab Aduan</li>
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
                <div class="card-header bg-primary">
                  <h3 class="card-title">Maklumat Pengadu</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <dl class="row">
                        <dd class="col-sm-3">Nama</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{$aduan->nama}}</dt>
                        <dd class="col-sm-3">Jawatan</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{$aduan->jawatan}}</dt>
                        <dd class="col-sm-3">No. Telefon</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{$aduan->notel}}</dt>
                        <dd class="col-sm-3">Jabatan</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{$aduan->jabatan}}</dt>
                        <dd class="col-sm-3">Tarikh Aduan</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{ date('d-m-Y', strtotime($aduan->tarikh_aduan))}}</dt>
                        <dd class="col-sm-3">Masa Aduan</dd>
                        <dd class="col-sm-1">:</dd>
                        <dt class="col-sm-8">{{$aduan->masa_aduan}}</dt>
                    </dl>
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
                <!-- /.card-header -->
                <div class="card-body">
                        <dl class="row">
                            <dd class="col-sm-3">No. Aduan</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">{{$aduan->no_aduan}}</dt>
                            <dd class="col-sm-3">Kategori</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">{{$aduan->kategori}}</dt>
                            <dd class="col-sm-3">Masalah</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">{{$aduan->masalah}}</dt>
                            <dd class="col-sm-3">Mesej Ralat</dd>
                            <dd class="col-sm-1">:</dd>
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
                        </dl>
                </div>
            </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                  <h3 class="card-title">Tindakan Pembekal Aduan</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                        <dl class="row">
                            {{-- <dd class="col-sm-3">No Inventori</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->noinventori == '')
                                Tiada
                                @else
                                {{$aduan->noinventori}}
                                @endif
                            </dt> --}}
                            <dd class="col-sm-3">Model</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->model == '')
                                Tiada
                                @else
                                {{$aduan->model_name ?? $aduan->model}}
                                @endif</dt>
                            <dd class="col-sm-3">Jenis</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->jenis_kategori == '')
                                Tiada
                                @else
                                {{$aduan->subkat ?? $aduan->jenis_kategori}}
                                @endif
                            </dt>
                            <dd class="col-sm-3">No Siri</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->nosiri == '')
                                Tiada
                                @else
                                {{$aduan->nosiri}}
                                @endif
                            </dt>
                            <dd class="col-sm-3">Catatan</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->maklumbalas == '')
                                Tiada
                                @else
                                {{$aduan->maklumbalas}}
                                @endif
                            </dt>
                            <dd class="col-sm-3">Tarikh Respon</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->tarikh_tindakan == '')
                                @else
                                {{ date('d-m-Y', strtotime($aduan->tarikh_tindakan))}}
                                @endif
                            </dt>
                            <dd class="col-sm-3">Masa Respon</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->masa_tindakan == '')
                                Tiada
                                @else
                                {{$aduan->masa_tindakan}}
                                @endif
                            </dt>
                            <dd class="col-sm-3">Perlu Onsite</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->id_onsite == 'Y')
                                Ya
                                @elseif($aduan->id_onsite == 'T')
                                Tidak
                                @else
                                Tiada
                                @endif
                            </dt>
                            <dd class="col-sm-3">Gantian Perkakasan</dd>
                            <dd class="col-sm-1">:</dd>
                            <dt class="col-sm-8">
                                @if($aduan->id_ganti == 'Y')
                                Ya
                                @elseif($aduan->id_ganti == 'T')
                                Tidak
                                @else
                                Tiada
                                @endif
                            </dt>
                        </dl>
                </div>
            </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success">
                      <h3 class="card-title">Jawab Aduan: Tindakan Pembekal</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form method="post" action="{{ url('t_pembekal', $aduan->no_aduan)}}">
                            @csrf
                        <input name="id_pengadu" value="{{$aduan->id_pengadu}}" hidden>
                        <input name="idstatus" value="3" hidden>
                        <input name="masalah" value="{{$aduan->masalah}}" hidden>
                         <input name="tindakan_pegawai" value="{{Auth::user()->nama}}" hidden> <tr class="form-group">

                        <table class="table">
                            <tbody>
                                <tr class="form-group">
                                    <td><label>Catatan <span class="text-red">*</span></label></td>
                                    <td>:</td>
                                    <td>
                                        <textarea class="form-control" name="maklumbalas" rows="3"  width="100%" required></textarea>

                                    </td>
                                </tr>
                                <div class="bootstrap-timepicker">
                                    <tr class="form-group">
                                        <td><label>Tarikh</label></td>
                                        <td>:</td>
                                        <td>
                                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text"  name="tarikh_tindakan" class="form-control datetimepicker-input" data-target="#datetimepicker1" required/>
                                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label>Masa</label></td>
                                        <td>:</td>
                                        <td>
                                            <div class="input-group date" id="datetimepicker4" data-target-input="nearest">
                                                <input type="text" name="masa_tindakan" class="form-control datetimepicker-input" data-target="#datetimepicker4" required/>
                                                <div class="input-group-append" data-target="#datetimepicker4" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </div>
                            </tfoot>
                          </table>
                    </div>
                    <div class="row">
                        <table id="example2" class="table">
                            <tbody>
                               <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="submit" class="btn btn-primary">Hantar</button>
                                    <a type="button" class="btn btn-info" href="{{ url('t_senaraiaduan') }}">
                                    Kembali
                                    </a>
                                    {{-- <button type="button" id="myBtn" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger">
                                        Tolak
                                    </button> --}}
                                </td>
                            </tr>
                            </tfoot>
                          </table>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
                </div>
              </div>
              <!-- /.card -->

              <div class="modal fade" id="myModal">
                <form method="POST" action="{{ url('t_reject/'.$aduan->no_aduan)}}">
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


            <!--wrapper -->
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



<script type="text/javascript">
    $(function () {
        $('#datetimepicker1').datetimepicker({format: 'L'});
        $('#datetimepicker2').datetimepicker({format: 'L'});
        $('#datetimepicker3').datetimepicker({format: 'L'});


        $('#datetimepicker4').datetimepicker({format: 'LT'});
        $('#datetimepicker5').datetimepicker({format: 'LT'});
        $('#datetimepicker6').datetimepicker({format: 'LT'});
    });
</script>
<script>
    $(document).ready(function(){
      $("#myBtn").click(function(){
        $("#myModal").modal();
      });
    });
</script>

</body>
</html>
