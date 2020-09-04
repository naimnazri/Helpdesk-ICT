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
              <li class="breadcrumb-item"><a href="{{url('/t_aduanbaru')}}">Aduan Baru</a></li>
              <li class="breadcrumb-item active">Jawab Aduan</li>
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
                    <div class="card-header bg-success">
                      <h3 class="card-title">Jawab Aduan: Tindakan</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form method="post" action="{{ url('t_agihan', $aduan->no_aduan)}}">
                            @csrf
                        <table id="example2" class="table">
                            <tbody>
                                <tr class="form-group">

                                <input name="id_pengadu" value="{{$aduan->id_pengadu}}" hidden>
                                <input name="masalah" value="{{$aduan->masalah}}" hidden>
                                <input name="tindakan_pegawai" value="{{Auth::user()->nama}}" hidden>
                                <input name="id_pengguna" value="{{Auth::user()->idpengguna}}" hidden>

                                <tr class="form-group">
                                    {{-- <td><label>No Inventori</label></td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="noinventori" required>
                                    </td> --}}
                                    <td><label>Kategori <span class="text-red ">*</span></label></td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-control dynamic" data-dependent="subkat" name="kategori"
                                        id="kategori" style="width: 100%;" required>
                                            <option selected="selected">Sila Pilih Jenis Kategori</option>
                                            @foreach($kategori as $kat)
                                            <option name="kategori" value="{{$kat->idkategori}}">{{$kat->kategori}}</option>
                                            @endforeach
                                          </select>

                                    </td>
                                    <td><label>Jenis <span class="text-red dynamic">*</span></label></td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-control " name="jenis_kategori" id="subkat" style="width: 100%;" required>
                                            <option selected="selected">Sila Pilih Jenis</option>
                                          </select>

                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <td><label>Model <span class="text-red">*</span></label></td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-control select2" name="model" style="width: 100%;" required>
                                            <option selected="selected">Sila Pilih Model</option>
                                            @foreach($model as $m)
                                            <option name="model" value="{{$m->idmodel}}">{{$m->model_name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><label>No Siri</label></td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="nosiri" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table">
                            <tbody>
                                <tr class="form-group">
                                    <td><label>Catatan <span class="text-red">*</span></label></td>
                                    <td>:</td>
                                    <td>
                                        <textarea class="form-control" name="maklumbalas" rows="3" cols="7" required></textarea>
                                        <span class="text-red">*Peringatan: Sila nyatakan Punca Masalah dan Cara Penyelesaian yang Dilaksanakan</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="example2" class="table">
                            <tbody>
                                {{-- <div class="bootstrap-timepicker">
                                    <tr class="form-group">
                                        <td><label>Tarikh Respon</label></td>
                                        <td>:</td>
                                        <td>
                                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text" name="tarikh_tindakan" class="form-control datetimepicker-input" data-target="#datetimepicker1" required/>
                                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><label>Masa Respon</label></td>
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
                                </div> --}}

                                {{-- <tr class="form-group">
                                    <td><label>Tarikh Onsite</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                            <input type="text" name="tarikh_onsite" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                                            <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><label>Masa Onsite</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="input-group date" id="datetimepicker5" data-target-input="nearest">
                                            <input type="text" name="masa_onsite" class="form-control datetimepicker-input" data-target="#datetimepicker5"/>
                                            <div class="input-group-append" data-target="#datetimepicker5" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr> --}}
                                <tr class="form-group">
                                    <td><label>Perlu Onsite</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="form-check">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="id_onsite" id="idlevel" value="Y" required>
                                                <label class="form-check-label" for="inlineRadio1">Ya</label>
                                              </div>
                                              <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="id_onsite" id="idlevel" value="T" required>
                                                <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                              </div>
                                        </div>
                                    </td>
                                    <td><label>Gantian Perkakasan</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="form-check">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="id_ganti" id="id_ganti" value="Y" required>
                                                <label class="form-check-label" for="inlineRadio1">Ya</label>
                                              </div>
                                              <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="id_ganti" id="id_ganti" value="T" required>
                                                <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                              </div>
                                        </div>
                                    </td>
                                </tr>
                                {{-- <tr class="form-group">
                                    <td><label>Tarikh Tindakan</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                            <input type="text" name="tarikh_tindakan" class="form-control datetimepicker-input" data-target="#datetimepicker3"/>
                                            <div class="input-group-append" data-target="#datetimepicker3" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><label>Masa Tindakan</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="input-group date" id="datetimepicker6" data-target-input="nearest">
                                            <input type="text" name="masa_tindakan" class="form-control datetimepicker-input" data-target="#datetimepicker6"/>
                                            <div class="input-group-append" data-target="#datetimepicker6" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr> --}}
                                <tr class="form-group">
                                    <td><label>Tindakan Pembekal</label></td>
                                    <td>:</td>
                                    <td>
                                        <div class="form-check">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="idstatus" id="distatus" value="9" required>
                                                <label class="form-check-label" for="inlineRadio1">Ya</label>
                                              </div>
                                              <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="idstatus" id="idstatus" value="3" required>
                                                <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                              </div>
                                        </div>
                                    </td>
                                    <td><label></label></td>
                                    <td></td>
                                    <td>
                                        <div class="form-check">
                                            <div class="form-check form-check-inline">

                                              </div>
                                              <div class="form-check form-check-inline">

                                              </div>
                                        </div>
                                    </td>
                                </tr>
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
                                    {{-- <a type="button" class="btn btn-info" href="{{ url('t_senaraiaduan') }}">
                                    Kembali
                                    </a> --}}
                                    <button type="button" id="myBtn" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger">
                                        Tolak
                                    </button>
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
<script>
    $(document).ready(function(){

     $('.dynamic').change(function(){
      if($(this).val() != '')
      {
       var select = $(this).attr("id");
       var value = $(this).val();
       var dependent = $(this).data('dependent');
       var _token = $('input[name="_token"]').val();
       $.ajax({
        url:"{{ route('technician.subkat') }}",
        method:"POST",
        data:{select:select, value:value, _token:_token, dependent:dependent},
        success:function(result)
        {
         $('#'+dependent).html(result);
        }
       })
      }
     });
     $('#kategori').change(function(){
      $('#subkat').val('');
     });
    });
    </script>
</body>
</html>
