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
        @include('flash-message')
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
           {{--  <div class="col-6">
                <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Maklumat Pengguna</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table id="example2" class="table  table-hover">
                        <tbody>
                        @foreach($pengguna as $p)
                        <tr>
                          <td>No KP</td>
                          <td> : </td>
                        <td>{{ $p->no_kp}}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td> : </td>
                            <td>{{$p->nama}}</td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td> : </td>
                            <td>{{ $p->jabatan }}</td>
                        </tr>
                        <tr>
                            <td>Bahagian</td>
                            <td> : </td>
                            <td>{{$p->bahagian}}</td>
                        </tr>
                        <tr>
                            <td>Jawatan</td>
                            <td> : </td>
                            <td>{{$p->jawatan}}</td>
                        </tr>
                        <tr>
                            <td>No Telefon</td>
                            <td> : </td>
                            <td>{{$p->notel}}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td> : </td>
                            <td>{{$p->email}}</td>
                        </tr>
                        @endforeach
                        </tfoot>
                      </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{url('p_senaraiaduan')}}" class="btn btn-info">
                            <i class="nav-icon fas fa-search"></i>Semak Aduan
                        </a>
                      </div>
                    <!-- /.card-body -->
                    </div>

            </div> --}}

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Maklumat Aduan </h3><br>
                        <span class="text-red"><strong>Arahan</strong>: Bertanda * mesti atau pilih</span>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <form role="form" method="POST" action="{{ route('aduan.store')}}" enctype="multipart/form-data">
                          @csrf
                        <div class="card-body">
                        <input name="id_pengadu" value="{{Auth::user()->no_kp}}" hidden>
                        <input name="idjab" value="{{Auth::user()->idjab}}" hidden>
                        <input name="nama" value="{{Auth::user()->nama}}" hidden>

                           {{--  <div class="form-group">
                                <label for="no_aduan">No Aduan</label>
                                <input class="form-control" name="no_aduan" for="no_aduan" value="{{$irno}}" readonly>
                              </div> --}}
                           {{--  <div class="form-group">
                                <label>Pilih Kategori Aduan <span class="text-red"> * </span></label>
                                <select name="idkategori" class="form-control select2" style="width: 100%;">
                                  <option selected="selected">Sila Pilih</option>
                                  @foreach($kategori as $kat)
                                    <option name="idkategori" value="{{$kat->idkategori}}">{{$kat->kategori}}</option>
                                  @endforeach
                                </select>
                            </div> --}}
                            <div class="form-group">
                                <label for="nama">Keterangan Aduan <span class="text-red"> * </span></label>
                                <input type="text" name="masalah" class="form-control" id="nama" required>
                            </div>
                            <div class="form-group">
                              <label for="jawatan">Mesej Ralat (jika ada)</label>
                              <textarea type="text" name="errormsg" class="form-control" id="errormsg"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="Image">Gambar</label>
                                <input type="file" accept="image/*" class="form-control" name="image" id="image">
                            </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                          <button type="submit" class="btn btn-danger">Hantar Aduan</button>
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
