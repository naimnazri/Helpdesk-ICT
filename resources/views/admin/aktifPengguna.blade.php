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
                <li class="breadcrumb-item active">Aduan Baru</li>
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
                <div class="card-header">
                  <h3 class="card-title">Senarai Pengguna Baru</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="tableAduan" class="table table-bordered table-hover">
                    <thead class="bg-primary ">
                    <tr>
                      <th>Bil</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Tindakan</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $skipped = $pengguna->firstItem() - 1; ?>
                    @foreach($pengguna as $pen)
                    <tr>
                      <td>{{ $loop->iteration + $skipped}}</td>
                      <td>{{$pen->nama}}</td>
                      <td>{{$pen->email}}</td>
                      <td>
                        <a type="button" title="Aktif"  class="btn btn-block btn-success btn-sm" data-toggle="modal"
                      href="#" data-target="#test1{{$pen->idpengguna}}" >
                                <i class="fas fa-toggle-on"></i>
                        </a>
                      </td>

                    <div class="modal fade" id="test1{{$pen->idpengguna}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <form method="POST" action="{{ url('aktif/'.$pen->idpengguna)}}">
                            @csrf
                            <div class="modal-dialog  modal-dialog-centered ">
                              <div class="modal-content ">
                                <div class="modal-header bg-primary">
                                <h4 class="modal-title">{{$pen->idpengguna}} Aktif Pengguna: <br> {{$pen->nama}}</h4>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <input name="idpengguna" value="{{$pen->idpengguna}}" hidden>
                                    <input name="email" value="{{$pen->email}}" hidden>
                                    <input name="nama" value="{{$pen->nama}}" hidden>
                                    <div class="form-group">
                                        <dl>
                                        <dd> Status Pengguna: {{$pen->aktif}}</dd>
                                        <dd>
                                            <div class="form-check">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="aktif" id="idlevel" value="1"
                                                    {{ $pen->aktif == '1' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="inlineRadio1">Aktif</label>
                                                  </div>
                                                  {{-- <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="aktif" id="idlevel" value="0"
                                                    {{ $pen->aktif == '0' ? 'checked' : '' }}required>
                                                    <label class="form-check-label" for="inlineRadio2">Tidak Aktif</label>
                                                  </div> --}}
                                            </div>
                                        </dd>

                                    </dl>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Kembali</button>
                                    <button type="submit" class="btn btn-primary">Hantar</button>
                                  </div>
                                </div>
                              </div>
                              <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </form>
                          </div>
                          @endforeach







                    </tr>
                    </tfoot>
                  </table>
                  <br>
                  {!! $pengguna->links() !!}
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


<script>
    $(document).ready(function(){
      $("#kro").click(function(){
        $("#test1").modal();
      });
    });
  </script>

</body>
</html>
