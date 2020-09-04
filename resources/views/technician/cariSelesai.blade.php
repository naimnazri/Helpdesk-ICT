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
                <li class="breadcrumb-item"><a href="{{url('/t_home')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Carian Aduan Selesai</li>
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
                        <div class="card-title">
                            Carian Aduan Selesai
                        </div>
                    </div>
                    <div class="card-body">
                        <table>
                            <tr>
                                <td>
                                <form>
                                    @csrf
                                    <h6>Pilih Tahun:
                                    <p></p>
                                    <div class="form-group mb-3">
                                        <select class="form-control" name="tahun" id="tahun">
                                            <option  value="">Sila Pilih Tahun </option>
                                            @foreach ( range( $tahun_awal ,  $tahun_now ) as $i )
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p></p>Pilih Bulan::</h6>
                                    <div class="form-group mb-3">
                                    <select class="form-control" name="bulan" id="bulan">
                                        <option>Sila Pilih Bulan </option>
                                        <option  value="">Semua</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Mac</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Jun</option>
                                        <option value="07">Julai</option>
                                        <option value="08">Ogos</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Disember</option>
                                    </select>
                                    </div>
                                    <p></p>Juruteknik:</h6>
                                    <div class="form-group mb-3">
                                    <select class="form-control" name="technician" id="idpengguna">
                                        <option  value="">Sila Pilih Juruteknik </option>
                                        <option value="">Semua</option>
                                        @foreach ($technician as $tech )
                                        <option value="{{$tech->idpengguna}}">{{$tech->nama}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="form-group text-center" >
                                        <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
                                    </div>
                                </form>
                                </td>
                            </tr>
                        </table>
                        <br><br>
                        <div class="ajax1 ">
                        </div>

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

<script>
    $(document).ready(function(){

        $('#filter').click(function(){
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            var idpengguna = $('#idpengguna').val();
            $.ajax({
            type : 'get',
            url : "{{ route('technician.searchSelesai') }}",
            data:{tahun:tahun, bulan:bulan, idpengguna:idpengguna},
            success:function(data){
            $('.ajax1').html(data);
            }
            });
        });

    });
</script>

<script type="text/javascript">
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

</body>
</html>
