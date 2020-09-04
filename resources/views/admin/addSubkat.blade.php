<!DOCTYPE html>
<html>
@include('layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!--navbar -->
    {{-- @include('layout_admin.navbar') --}}

  <!-- Main Sidebar Container -->
  {{-- @include('layout_admin.sidebar') --}}


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
              <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
              <li class="breadcrumb-item active">Tambah SubKategori</li>
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
            <div class="col-6">
                <div class="card">
                    <div class="card-header bg-primary">
                    <h3 class="card-title">Senarai Subkategori </h3><br>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                      <div class="card-body">
                        <label for="">Kategori</label>
                        <select type="text" class="form-control"  name="kategori" id="kategori" required>
                            <option value="" name="kategori" >Sila Pilih Kategori</option>
                            @foreach($kategori as $kat)
                                    <option name="kategori" value="{{$kat->idkategori}}">{{$kat->kategori}}</option>
                            @endforeach
                        </select>
                        <br>
                        <div class="form-group text-center" >
                            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>

                            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
                        </div>
                        <br>
                        <table id="list" class="table table-bordered table-hover">
                            <thead class="text-center bg-primary">
                                <tr>
                                    <th>Subkategori</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                        </table>
                      </div>

                    </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header bg-success">
                    <h3 class="card-title">Tambah Subkategori </h3><br>
                    </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <form role="form" method="POST" action="{{ route('admin.storeSubkat')}}" enctype="multipart/form-data">
                          @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="idpengguna">Kategori</span></label>
                                <select class="form-control" name="kategori" id="kategori1">
                                    @foreach($kategori as $kat)
                                    <option name="kategori" value="{{$kat->idkategori}}">{{$kat->kategori}}</option>
                                    @endforeach
                                </select>
                            </div>
                           <div class="form-group">
                             <label for="idpengguna">Subkategori</span></label>
                             <input type="text" name="subkategori" class="form-control" id="subkategori" required>
                           </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                          <button type="submit" class="btn btn-success">Tambah</button>
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

<script>
    $(document).ready(function(){

        fill_datatable();

        function fill_datatable(filter_kategori = '')
        {
            var dataTable = $('#list').DataTable({
                processing: true,
                serverSide: true,
                ajax:{
                    url: "{{ route('admin.listKat') }}",
                    data:{filter_kategori:filter_kategori}
                },
                columns: [
                    {
                        data:'subkat',
                        name:'subkat'
                    },
                    {
                        data:'kategori',
                        name:'kategori'
                    }
                ]
            });
        }

        $('#filter').click(function(){
            var filter_kategori = $('#kategori').val();

            if(filter_kategori != '')
            {
                $('#list').DataTable().destroy();
                fill_datatable(filter_kategori);
            }
            else if(filter_kategori == ''){
                alert('takde');
            }
            else
            {
                alert('Select filter option');
            }
        });

        $('#reset').click(function(){
            $('#kategori').val('');
            $('#list').DataTable().destroy();
            fill_datatable();
        });

    });
</script>

</body>
</html>
