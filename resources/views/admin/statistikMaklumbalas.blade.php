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
                <li class="breadcrumb-item active">Statistik Maklum Balas</li>
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
                            Statistik Maklum Balas Pengadu
                        </div>
                    </div>
                    <div class="card-body">
                        <table>
                            <tr>
                                <td>
                                <form method="POST" {{-- action="{{url('maklumbalas')}}" --}}>
                                    @csrf
                                    <h6>Sila masukkan tahun:
                                    <p></p>
                                    <div class="form-group mb-3">
                                        <select class="form-control" name="tahun" id="tahun">
                                            <option  value="">Sila Pilih Tahun </option>
                                            @foreach ( range( $tahun_awal ,  $tahun_now ) as $i )
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <h6>Sila pilih juruteknik:
                                        <p></p>
                                        <div class="form-group mb-3">
                                            <select class="form-control" name="id_pengguna" id="id_pengguna">
                                                <option  value="">Sila Pilih Juruteknik </option>
                                                <option  value="">Semua</option>
                                                @foreach ($tech as $t)
                                                <option value="{{$t->idpengguna}}">{{$t->nama}}</option>
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
            var id_pengguna = $('#id_pengguna').val();
            $.ajax({
            type : 'get',
            url : "{{ route('admin.maklumbalas') }}",
            data:{tahun:tahun,id_pengguna:id_pengguna},
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
