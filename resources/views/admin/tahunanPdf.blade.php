<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <title>Statistik Tahunan</title>
</head>
<body>
  <h1>Statistik Tahunan</h1>
  <div>
      <table class="table table-border">
          <thead>
              <tr>
                  <th>Tahun</th>
                  <th>Bilangan</th>
              </tr>
          </thead>
          <tbody>

            @foreach($totalAduan as $con)
              <tr>
                  <td>{{$con->year}}</td>
                  <td>{{$con->totalAduan}}</td>
                  <td>

                      @php $number = 0; @endphp
                      @foreach($baru as $keys => $b)
                      @if($con->no_aduan == $b->no_aduan)
                      @php $number++ @endphp
                      @endif
                      @endforeach
                      {{ $number }}

                  </td>
              </tr>
              @endforeach
          </tbody>
      </table>
  </div>
</body>
</body>
</html>
