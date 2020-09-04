<!DOCTYPE html>
<html>
@include('layouts.head')

           {{--  <table class="table">
                <thead>
                    <th>Maklumat</th>
                    <th>Individu</th>
                    <th>Peranan</th>
                    <th>Tarikh</th>
                </thead>

                <tbody>
                    @foreach($kronologi as $kro)
                    <tr>
                        <td>{{$kro->nama_status}}</td>
                        <td>{{$kro->no_aduan}}</td>
                        <td>{{$kro->level}}</td>
                        <td>{{$kro->tarikh_masa_skrg}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table> --}}



            <dl class="row ">
                <dd class="col-sm-3 text-center border bg-light">Maklumat</dd>
                <dd class="col-sm-3 text-center border bg-light">Individu</dd>
                <dd class="col-sm-3 text-center border bg-light">Peranan</dd>
                <dd class="col-sm-3 text-center border bg-light">Tarikh</dd>
            </dl>
            @foreach($kronologi as $kro)
            <dl class="row ">
            <dd class="col-sm-3 text-center ">{{$kro->nama_status}} </dd>
                <dd class="col-sm-3 text-center ">{{$kro->no_aduan}}</dd>

                <dd class="col-sm-3 text-center ">{{$kro->level}}</dd>
                <dd class="col-sm-3 text-center ">{{$kro->tarikh_masa_skrg}}</dd>

            </dl>
            @endforeach






<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>




</html>
