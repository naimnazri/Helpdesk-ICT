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
              <li class="breadcrumb-item"><a href="{{url('t_home')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Senarai Pengguna</li>
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
                  <h3 class="card-title">Senarai Pengguna Jabatan</h3>
                  <br>
                  <div class="form-group mb-3">
                    <select type="text" class="form-control dynamic" data-dependent="bahagian" name="jabatan" id="idjab" required>
                        <option value="" name="jabatan" >Sila Pilih Jabatan</option>
                        @foreach($jabatan as $jab)
                        <option value="{{$jab->idjab}}">{{$jab->jabatan}}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group mb-3">
                    <select type="text" class="form-control dynamic2" name="bahagian" id="bahagian"  required>
                        <option  value="">Sila Pilih Bahagian </option>
                    </select>
                  </div>
                  <div class="form-group text-center" >
                    <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>

                    <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="list" class="table table-bordered table-hover table-resposive">
                    <thead class="bg-primary ">
                    <tr>
                      <th>No. KP</th>
                      <th>Nama</th>
                      <th>Aktif</th>
                      <th>Tindakan</th>
                    </tr>
                    </thead>
                  </table>
                  <br>

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

     $('.dynamic').change(function(){
      if($(this).val() != '')
      {
       var select = $(this).attr("id");
       var value = $(this).val();
       var dependent = $(this).data('dependent');
       var _token = $('input[name="_token"]').val();
       $.ajax({
        url:"{{ route('technician.list') }}",
        method:"POST",
        data:{select:select, value:value, _token:_token, dependent:dependent},
        success:function(result)
        {
         $('#'+dependent).html(result);
        }

       })
      }
     });

     $('#idjab').change(function(){
      $('#bahagian').val('');
    });

    });
</script>

<script>
    $(document).ready(function(){

        fill_datatable();

        function fill_datatable(filter_gender = '', filter_country = '')
        {
            var dataTable = $('#list').DataTable({
                processing: true,
                serverSide: true,
                ajax:{
                    url: "{{ route('technician.list2') }}",
                    data:{filter_gender:filter_gender, filter_country:filter_country}
                },
                columns: [
                    {
                        data:'no_kp',
                        name:'no_kp'
                    },
                    {
                        data:'nama',
                        name:'nama'
                    },
                    {
                        data:'aktif',
                        name:'aktif'
                    },
                    {
                        data: 'detail', name: 'detail', orderable: false, searchable: false
                    }
                ]
            });
        }

        $('#filter').click(function(){
            var filter_gender = $('#idjab').val();
            var filter_country = $('#bahagian').val();

            if(filter_gender != '' &&  filter_gender != '')
            {
                $('#list').DataTable().destroy();
                fill_datatable(filter_gender, filter_country);
            }
            else if(filter_gender == '' &&  filter_gender == ''){
                alert('takde');
            }
            else
            {
                alert('Select Both filter option');
            }
        });

        $('#reset').click(function(){
            $('#idjab').val('');
            $('#bahagian').val('');
            $('#list').DataTable().destroy();
            fill_datatable();
        });

    });
    </script>

</body>
</html>
