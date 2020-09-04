<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_technician.navbar') --}}

  <!-- Main Sidebar Container -->
  {{-- @include('layout_technician.sidebar') --}}


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
                <li class="breadcrumb-item"><a href="{{url('/t_home')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Semakan Rekod Aduan</li>
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
                  <h4><strong>Semakan Rekod Aduan</strong></h4>
                  <label for="">Masukkan No. KP: </label>
                  <input type="text" class="form-controller" id="rekod" name="rekod">
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <div class="row ">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    <th>Nama</th>
                                    <th>Jawatan</th>
                                    <th>Jabatan</th>
                                    <th>Bahagian</th>
                                    <th>No. Telefon</th>
                                    <th>Emel</th>
                                </tr>
                            </thead>
                            <tbody class="ajax1">
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <h4 align="center">Jumlah Aduan: <span id="total_records"></span></h4>
                    <div class="row">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    {{-- <th>Bil</th> --}}
                                    <th>No Aduan</th>
                                    <th>Tarikh Aduan</th>
                                    <th>Keterangan Aduan</th>
                                    <th>Tindakan Pegawai</th>
                                </tr>
                            </thead>
                            <tbody class="ajax2">
                            </tbody>
                        </table>
                    </div>

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

<script type="text/javascript">
    $('#rekod').on('keyup',function(){
    $value=$(this).val();
    $.ajax({
    type : 'get',
    url : '{{URL::to('t_rekod')}}',
    data:{'rekod':$value},
    success:function(data){
    $('.ajax1').html(data);
    }
    });
    })
</script>
<script type="text/javascript">
    $('#rekod').on('keyup',function(){
    $value=$(this).val();
    $.ajax({
    type : 'get',
    url : '{{URL::to('t_rekod2')}}',
    data:{'rekod':$value},
    success:function(data){
    $('.ajax2').html(data);

    var rowCount = $('.ajax2').html(data).find('tr').length;
    $('#total_records').html(rowCount);

    }
    });
    })
</script>


<script type="text/javascript">
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>




</body>
</html>
