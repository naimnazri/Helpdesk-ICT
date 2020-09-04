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
                <li class="breadcrumb-item active">Statistik Juruteknik</li>
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
                            Statistik Juruteknik
                           {{-- <table class="table">
                                <tr>
                                    <th>Tahun</th>
                                    <th>Jumlah</th>
                                </tr>
                                @foreach($query as $t)
                                <tr>
                                    <td>{{$t->kategori}}</td>
                                    <td>{{$t->totalKat}}</td>
                                </tr>
                                @endforeach
                                 <tr>
                                    <td>Jumlah</td>
                                    <td>
                                        @php $number=0; @endphp
                                        @foreach($totalAduan as $t)
                                        @php $number = $number + $t->totalAduan @endphp
                                        @endforeach
                                        {{$number}}
                                    </td>
                                </tr> --}}
                            </table>

                        </div>
                    </div>
                    <div class="card-body">
                        <table>
                            <tr>
                                <td>
                                <form>
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

                                    {{-- <input type="text" name="end" id="end" maxlength="4" class="form-control"/><p></p> --}}
                                    {{-- <p></p><input type="text" name="start" id="start" maxlength="4"  class="form-control" /> --}}
                                    <div class="form-group text-center" >
                                        <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
                                    </div>
                                </form>
                                </td>
                            </tr>
                        </table>
                        <br><br>
                        <div class="ajax1 ">
                            {{-- <h5>Statistik Helpdesk ICT Kategori</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Jumlah Diterima</th>
                                        <th>Baru</th>
                                        <th>Agihan</th>
                                        <th>Dalam Tindakan</th>
                                        <th>Selesai</th>
                                        <th>Prestasi (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="ajax1 ">
                                </tbody>
                            </table> --}}
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
            var idpengguna = $('#idpengguna').val();
            $.ajax({
            type : 'get',
            url : "{{ route('admin.technician') }}",
            data:{tahun:tahun, idpengguna:idpengguna},
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
