<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use App\Aduan;
use Redirect;
use Carbon\Carbon;
use Auth;
use PDF;
use Excel;
use App;

/* use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; */

/* use App\Connect; */





class AdminController extends Controller
{
    public function resetPass()
    {
        return view('admin.reset_pass');
    }

    public function storePass(Request $request)
    {
        $idpengguna = $request->idpengguna;
        /* dd($request->password); */

            $request->validate([
                'password' => 'min:8'
            ]);

            $data = array();
            $data['password'] = md5($request->password);
            $data['temp_pass'] = 0;

            $pengguna = DB::table('pengguna')
                        ->where('idpengguna', '=', $idpengguna)
                        ->update($data);
        if($pengguna)
        {
            return redirect()->route('admin.dashboard')->with('Success', 'Password Berjaya direset');
        }
        /* else{
            return redirect()->route('admin.reset')->with('Danger', 'Password Gagal direset');
        } */
    }

    public function adminHome()
    {
        $baru =DB::table('aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->where('idstatus', '=', '1')
        ->where('jabatan.f_pass', '=', '1')->count();

        $proses =DB::table('aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->whereIn('idstatus',  ['4','2'])
        ->where('jabatan.f_pass', '=', '1')->count();

        $pembekal =DB::table('aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->where('idstatus', '=', '9')
        ->where('jabatan.f_pass', '=', '1')->count();

        $selesai =DB::table('aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->where('idstatus', '=', '3')
        ->where('jabatan.f_pass', '=', '1')->count();

        $tolak =DB::table('aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->where('idstatus', '=', '10')
        ->where('jabatan.f_pass', '=', '1')->count();

        $today = Carbon::now();
        $now = $today->toDateString();
        $tarikh = date('d-m-Y', strtotime($now));

        $tech = DB::table('pengguna')
                ->select('idpengguna', 'nama')
                ->whereIn('idlevel', ['2','7'])
                ->orderBy('nama', 'ASC')
                ->get();

               /*  $query = DB::table('pengguna')
                ->leftjoin('aduan', 'pengguna.idpengguna', '=', 'aduan.id_pengguna')
                ->select(['pengguna.nama', 'pengguna.idpengguna',
                    DB::raw('IFNULL(count(*),0) as bilangan')
                ])
                ->where('pengguna.idlevel', '=', '2')
                ->whereYear('aduan.tarikh_aduan', '=', '2020')
                ->groupBy('pengguna.idpengguna')
                ->orderBy('pengguna.idpengguna','desc')
                ->get(); */


                $query = DB::table('aduan')
                ->select('id_pengguna', 'no_aduan')
                ->whereDate('aduan.tarikh_aduan', '=', $now )
                ->get();


                /* dd($query); */

        return view('admin.dashboard' ,compact('baru','proses','selesai','tolak', 'pembekal', 'tarikh', 'query', 'tech'));
    }

    public function createAduan()
    {
        return view('admin.add_aduan');
    }

    public function storeAduan(Request $request)
    {
        $dt = Carbon::now();
        $tahun = $dt->year;
        $bulan = $dt->month;

        //generate no_aduan
        $m=Date("m");
        if (strlen($m)==1)
        {
            $m="0".$m;
        } else
        {
            $m=$m;
        }
        $y= Date("y");

        $s = $y.$m;
        $f = $y.$m."-";
        $no2 = DB:: table('aduan')
            ->select('no_aduan')
            ->where('no_aduan', 'like', '%'.$s.'%')
            ->whereYear('tarikh_aduan', '=', $tahun)
            ->whereMonth('tarikh_aduan', '=', $bulan)
            ->max('no_aduan');


            $suffix2 = substr($no2, -4);
            $newsuffix2 = intval($suffix2) + 1;
            $irno = $f.str_pad($newsuffix2, 4, 0, STR_PAD_LEFT);
        //endgenerate

            $jabatan = DB::table('pengguna')
                        ->select('idjab')
                        ->where('idpengguna', '=', $request->id_pengadu)
                        ->first();

            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            ]);

            $data = array();
            $data['id_pengadu'] = $request->id_pengadu;
            $data['no_aduan'] = $irno;
            $data['idjab'] = $jabatan->idjab;
            $data['masalah'] = $request->masalah;
            $data['errormsg'] = $request->errormsg;
            $data['idstatus'] = 1;
            $data['tarikh_aduan'] = $dt->toDateString();
            $data['masa_aduan'] = $dt->toTimeString();

            if($request->hasFile('image'))
            {
                $imageName = $irno.'.'.$request->image->extension();
                $request->image->move(public_path('storage'), $imageName);
                $data['image'] = $imageName;
            }

            $aduan = DB::table('aduan')->insert($data);

            //kronologi
            $data1 = array();
            $data1['idstatus'] = 1;
            $data1['no_aduan'] = $irno;
            $data1['tarikh_masa_skrg'] = $dt;
            $data1['id_pengadu'] = $request->id_pengadu;
            $data1['tindakan_pegawai'] = $request->nama;

            $kronologi = DB::table('kronologi')->insert($data1);

            //hantar email
            $admins = DB::table('pengguna')
                ->select(
                    'nama',
                    'email'
                )
                ->whereIn('idlevel', ['1','7']) //penyelaras dan teknikal kanan
                ->get();

            if($aduan)
            {
                foreach($admins as $admin)
                {
                $to_name = $admin->nama;
                $to_email = $admin->email;

                $data3 = array(
                    'admin' => $to_name ,
                    'no_aduan' => $irno,
                    'pengadu' => $request->nama,
                    'tajuk_aduan' => $request->masalah
                );

                /* $mail = new PHPMailer(true);

                        $mail->isSMTP();                                            // Send using SMTP
                        $mail->Host       = env('MAIL_HOST');                    // Set the SMTP server to send through
                        $mail->SMTPAuth   = env('MAIL_AUTH');                                   // Enable SMTP authentication
                        $mail->Username   = env('MAIL_USERNAME');                     // SMTP username
                        $mail->Password   = env('MAIL_PASSWORD');                               // SMTP password
                        $mail->SMTPSecure = env('MAIL_ENCRYPTION');         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                        $mail->Port       = env('MAIL_PORT');                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                        //Recipients
                        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $mail->addAddress($to_email, $to_name);     // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'ADUAN HELPDESK: BARU ('.$irno.')';
                        $mail->Body    = '<p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato /YB/YBhg. Dato/Tuan/Puan,</p>
                                        <p>Aduan baru telah ditambah seperti berikut:</p>
                                        <p>
                                            Nama: <strong>'.$request->nama.'</strong><br>
                                            Tajuk Aduan: <strong>'.$request->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$irno.'</strong>
                                        </p>
                                        <p>Sila klik pada pautan seperti di bawah untuk agihan aduan.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */


                Mail::send('emails.aduanBaru', $data3, function($message) use ($to_name, $to_email, $irno) {
                    $message->to($to_email, $to_name)
                            ->subject('ADUAN HELPDESK: BARU ('.$irno.')' );
                    $message->from('noreplyt@penang.gov.my','Admin Helpdesk');
                });
                }
                return redirect()->route('admin.aduanbaru')->with('success', 'Aduan Berjaya Ditambah');
            }else{
                return redirect()->route('admin.add_aduan')->with('error', 'Aduan Gagal Ditambah');
            }
    }

    public function listAduan()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->join('kategori', 'aduan.idkategori', '=',
                        'kategori.idkategori')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'kategori.kategori',
                        'aduan.jenis_kategori',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'status.nama_status'
                    )
                    ->paginate(10);

        return view('admin.listaduan', compact('aduan'));
    }

    public function aduanBaru()
    {
        $aduan = DB::table('aduan')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->select(
                        'aduan.id',
                        'aduan.no_aduan',
                        'aduan.idstatus',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.tarikh_aduan'
                    )
                    ->where('aduan.idstatus', '=', '1')
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(5);

        $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('admin.aduanbaru', compact('aduan', 'conn' ));
    }


    public function aduanProses()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengguna',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    /* ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->join('kategori', 'aduan.idkategori', '=',
                        'kategori.idkategori') */
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.jenis_kategori',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan'
                        /* 'aduan.idstatus' */
                        /* 'status.nama_status' */
                    )
                    ->whereIn('aduan.idstatus',  ['4'])
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);


                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('admin.aduanproses', compact('aduan','conn'));
    }

    public function aduanPembekal()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengguna',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->join('kategori', 'aduan.idkategori', '=',
                        'kategori.idkategori')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'kategori.kategori',
                        'aduan.jenis_kategori',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->whereIn('aduan.idstatus',  ['9'])
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('admin.aduanpembekal', compact('aduan', 'conn'));
    }

    public function aduanSelesai()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->join('kategori', 'aduan.idkategori', '=',
                        'kategori.idkategori')
                    ->leftjoin('feedback', 'aduan.no_aduan', '=', 'feedback.no_aduan_feedback')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'kategori.kategori',
                        'aduan.jenis_kategori',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('aduan.idstatus', '=', '3')
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    /* ->get(); */
                    ->paginate(10);

        /* $feedback = DB::table('feedback')
                    ->select('no_aduan')
                    ->get(); */

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('admin.aduanselesai', compact('aduan','conn'));
    }

    public function cariSelesai()
    {
        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2016';

        $technician = DB::table('pengguna')
                    ->select('idpengguna','nama')
                    ->whereIn('idlevel', ['2','7'])
                    ->get();

        return view('admin.cariSelesai', compact('tahun_now', 'tahun_awal', 'technician'));
    }

    public function searchSelesai(Request $request)
    {
        if($request->ajax())
        {
            $output="";
            $tahun = $request->tahun;
            $bulan = $request->bulan;
            $idpengguna = $request->idpengguna;

            if(($idpengguna != '') && ($bulan != '') )
            {
                $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->leftjoin('feedback', 'aduan.no_aduan', '=', 'feedback.no_aduan_feedback')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('aduan.idstatus', '=', '3')
                    ->where('jabatan.f_pass', '=', '1')
                    ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', $bulan)
                    ->where('aduan.id_pengguna', '=', $idpengguna)
                    ->latest('no_aduan')
                    /* ->paginate(10); */
                    ->get();
            }
            elseif($bulan != '')
            {
                $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->leftjoin('feedback', 'aduan.no_aduan', '=', 'feedback.no_aduan_feedback')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('aduan.idstatus', '=', '3')
                    ->where('jabatan.f_pass', '=', '1')
                    ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', $bulan)
                    ->latest('no_aduan')
                    /* ->paginate(10); */
                    ->get();
            }
            else
            {
                $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->leftjoin('feedback', 'aduan.no_aduan', '=', 'feedback.no_aduan_feedback')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('aduan.idstatus', '=', '3')
                    ->where('jabatan.f_pass', '=', '1')
                    ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                    ->latest('no_aduan')
                    /* ->paginate(10); */
                    ->get();
            }
           /*  dd($aduan); */
            $output.='
            <div class="text-success">Maklumbalas dah dijawab</div>
            <div class="text-primary">Maklumbalas belum dijawab</div>
            <br>
            <table id="tableAduan" class="table table-bordered table-hover">
                <thead class="thead-light ">
                    <tr>

                        <th>No Aduan</th>
                        <th>Masalah</th>
                        <th>Nama Pengadu</th>
                        <th>Jabatan</th>
                        <th>Tarikh Aduan</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    ';
            //foreach
            foreach($aduan as $adu)
            {
            $output.='
                    <tr>
                        <td>
                    ';
            //if
            if($adu->no_aduan_feedback === null){
            $output.=' <div class="text-primary text-bold">'.$adu->no_aduan.'</div>';
            }else{
            $output.=' <div class="text-success text-bold">'.$adu->no_aduan.'</div>';
            }
            //endif
            $output.='  </td>
                        <td>'.$adu->masalah.'</td>
                        <td>'.$adu->nama.'</td>
                        <td>'.$adu->jabatan.'</td>
                        <td>'.date('d-m-Y', strtotime($adu->tarikh_aduan)).'</td>
                        <td>
                        <a type="button" title="Maklumat Aduan" class="btn btn-block btn-info btn-sm"
                        href="maklumatSelesai/'.$adu->id.'" target="_blank">
                        <i class="fas fa-info"></i>
                    </a>
                        </td>
                    </tr>
                    ';
            }
            //endforeach
            $output.='
                    </tbody>
                    <table>
                    <!-- DataTables -->
                    <script src="{{asset("plugins/datatables/jquery.dataTables.min.js")}}"></script>
                    <script src="{{asset("plugins/datatables-bs4/js/dataTables.bootstrap4.min.js")}}"></script>
                    <script src="{{asset("plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>
                    <script src="{{asset("plugins/datatables-responsive/js/responsive.bootstrap4.min.js")}}"></script>

                    <!-- page script -->
                    <script>
                        $(function () {
                          $("#tableAduan").DataTable({
                            "paging": true,
                            "lengthChange": false,
                            "searching": true,
                            "ordering": false,
                            "info": false,
                            "autoWidth": false,
                            "responsive": true,
                          });
                        });
                      </script>

                    ';

            return Response($output);
        };
    }

    public function maklumatSelesai($id)
    {
        $aduan = DB::table('aduan')
                ->leftjoin('status', 'aduan.idstatus', '=',
                    'status.idstatus')
                ->leftjoin('pengguna', 'aduan.id_pengadu', '=',
                    'pengguna.id_pengadu')
                ->leftjoin('kategori', 'aduan.idkategori', '=',
                    'kategori.idkategori')
                ->leftjoin('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->leftjoin('subkategori', 'aduan.jenis_kategori', '=',
                    'subkategori.idsubkat')
                ->leftjoin('model', 'aduan.model', '=',
                    'model.idmodel')
                ->leftjoin('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->leftjoin('feedback_respon', 'feedback.respon_feedback', '=',
                    'feedback_respon.idrespon')
                ->select(
                    'aduan.no_aduan',
                    'aduan.maklumbalas',
                    'aduan.masalah',
                    'aduan.errormsg',
                    'aduan.idstatus',
                    'status.nama_status',
                    'pengguna.nama',
                    'pengguna.notel',
                    'pengguna.jawatan',
                    'jabatan.jabatan',
                    'aduan.jenis_kategori',
                    'subkategori.subkat',
                    'aduan.noinventori',
                    'aduan.model',
                    'model.model_name',
                    'aduan.tarikh_aduan',
                    'aduan.masa_aduan',
                    'aduan.maklumbalas',
                    'aduan.tarikh_tindakan',
                    'aduan.masa_tindakan',
                    'aduan.nosiri',
                    'aduan.id_pengguna',
                    'aduan.masa_respon',
                    'aduan.tarikh_respon',
                    'aduan.id_onsite',
                    'aduan.id_ganti',
                    'aduan.masa_onsite',
                    'aduan.tarikh_onsite',
                    'aduan.image',
                    'kategori.kategori',
                    'feedback_respon.respon_name',
                    'feedback.catatan',
                    'feedback.no_aduan_feedback'
                )
                ->where('id', '=', $id)
                ->first();

        $pegawai = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengguna', '=',
                        'pengguna.idpengguna')
                    ->select(
                        'pengguna.nama'
                    )
                    ->where('id', $id)
                    ->first();

        $kronologi = DB::table('kronologi')
                    ->leftjoin('pengguna', 'kronologi.tindakan_pegawai', '=',
                        'pengguna.nama')
                    ->leftjoin('level', 'pengguna.idlevel', '=',
                        'level.idlevel')
                    ->leftjoin('status', 'kronologi.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'kronologi.tarikh_masa_skrg',
                        'pengguna.nama',
                        'level.level',
                        'status.nama_status'
                    )
                    ->where('kronologi.no_aduan', '=', $aduan->no_aduan)
                    ->orderBy('kronologi.tarikh_masa_skrg', 'DESC')
                    ->get();


        return view('admin.maklumatSelesai', compact('aduan', 'pegawai', 'kronologi'));
    }

    public function aduanTolak()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.idpengguna')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.jenis_kategori',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('aduan.idstatus', '=', '10')
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('admin.aduantolak', compact('aduan', 'conn'));
    }

    public function listJabatan()
    {
        $jabatan = DB::table('jabatan')
                        ->select(
                            'idjab',
                            'jabatan'
                            )
                        ->paginate(10);

        return view('admin.list_pengguna', compact('jabatan'));
    }

    function list(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = DB::table('bahagian')
                ->select('idbahagian', 'bahagian')
                ->where('idjab', $value)
                /* ->groupBy($dependent) */
                ->get();
        $output = '<option value="">Sila Pilih Bahagian </option>';
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->idbahagian.'">'.$row->bahagian.'</option>';
        }
        echo $output;
    }

    function list2(Request $request)
    {
        if(request()->ajax())
        {
         if(!empty($request->filter_gender))
         {
          $data = DB::table('pengguna')
            ->join('aktif', 'pengguna.aktif', '=',
                'aktif.id')
            ->select('pengguna.no_kp', 'pengguna.nama', 'aktif.aktif', 'pengguna.idpengguna')
            ->where('idjab', $request->filter_gender)
            ->where('idbahagian', $request->filter_country)
            ->get();
         }
         else
         {
          $data = DB::table('pengguna')
          ->join('aktif', 'pengguna.aktif', '=',
                'aktif.id')
            ->select('pengguna.no_kp', 'pengguna.nama', 'aktif.aktif', 'pengguna.idpengguna')
            ->get();
         }
         return DataTables::of($data)
         ->addColumn('detail', function ($data)
        {
            return $return = '<a class="btn btn-primary" align="center" href="editpengguna/'.base64_encode($data->idpengguna).'" >Kemaskini</a>';
        })->rawColumns(['detail'])->make(true);
        }
    }

    public function detailPengguna(Request $request)
    {
        $no_kp = base64_decode($request->no_kp);
        $pengguna = DB::table('pengguna')
                    ->join('jabatan', 'pengguna.idjab', '=',
                        'jabatan.idjab')
                    ->join('bahagian', 'pengguna.idbahagian', '=',
                        'bahagian.idbahagian')
                    ->join('aktif', 'pengguna.aktif', '=',
                        'aktif.id')
                    ->select(
                        'pengguna.idpengguna',
                        'pengguna.nama',
                        'pengguna.no_ofis',
                        'pengguna.notel',
                        'pengguna.idlevel',
                        'pengguna.jawatan',
                        'jabatan.idjab',
                        'jabatan.jabatan',
                        'bahagian.idbahagian',
                        'bahagian.bahagian',
                        'pengguna.email',
                        'aktif.id',
                        'aktif.aktif'
                    )
                    ->where('no_kp', $no_kp)
                    ->first();

            $jabatan = DB::table('jabatan')
                        ->select('idjab','jabatan')
                        ->get();

            $bahagian = DB::table('bahagian')
                        ->select('idbahagian', 'bahagian')
                        ->first();

        return view('admin.edit_pengguna', compact('pengguna', 'jabatan', 'bahagian'));
    }

    function listBah(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = DB::table('bahagian')
                ->select('idbahagian', 'bahagian')
                ->where('idjab', $value)
                /* ->groupBy($dependent) */
                ->get();
        $output = '<option value="">Sila Pilih Bahagian </option>';
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->idbahagian.'">'.$row->bahagian.'</option>';
        }
        echo $output;
    }

    public function storeDetail(Request $request)
    {
        $idpengguna = $request->idpengguna;

        $no_kp = DB::table('pengguna')
                ->select('no_kp')
                ->where('idpengguna', $request->idpengguna)
                ->first();

        $data = array();
        if(trim($request->password) == '')
        {
            $request->validate([
                'notel' => 'digits_between:10,13|numeric|required',
                'no_ofis' => 'digits_between:9,13|numeric|required',
                'email' => 'email|required'
            ]);

            $data['nama'] = $request->nama;
            $data['idlevel'] = $request->idlevel;
            $data['no_ofis'] = $request->no_ofis;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;
            $data['idjab'] = $request->idjab;
            $data['idbahagian'] = $request->bahagian;
            $data['aktif'] = $request->aktif;

        } else {

            $request->validate([
                'password' => 'min:8|confirmed',
                'notel' => 'digits_between:10,13|numeric|required',
                'no_ofis' => 'digits_between:9,13|numeric|required',
                'email' => 'email|required'
            ]);

            $data['password'] = md5($request->password);

            $data['nama'] = $request->nama;
            $data['idlevel'] = $request->idlevel;
            $data['no_ofis'] = $request->no_ofis;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;
            $data['idjab'] = $request->idjab;
            $data['idbahagian'] = $request->bahagian;
            $data['aktif'] = $request->aktif;
        }

        $detail = DB::table('pengguna')
                    ->where('idpengguna','=', $idpengguna)
                    ->update($data);


        if(($detail) | ($detail)=='')
        {
            return redirect()->route('admin.list_pengguna')->with('success', 'Maklumat Pengguna Berjaya Dikemaskini');
        }else{
            return redirect()->url('editpengguna/'.$idpengguna)->with('error', 'Profil Pengguna Gagal Dikemaskini');
        }

    }

    public function profil()
    {
        $profil = DB::table('pengguna')
                    ->join('jabatan', 'pengguna.idjab',
                        '=', 'jabatan.idjab')
                    ->select(
                        'pengguna.idpengguna',
                        'pengguna.nama',
                        'pengguna.no_kp',
                        'pengguna.no_ofis',
                        'pengguna.notel',
                        'pengguna.email',
                        'pengguna.jawatan',
                        'jabatan.jabatan'
                    )
                    ->where('idpengguna','=',  Auth::user()->idpengguna)
                    ->get();

                    return view('admin.profil' ,compact('profil'));
    }

    public function editAduan($no_aduan)
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna','aduan.id_pengadu',
                    '=', 'pengguna.id_pengadu')
                    ->join('jabatan', 'aduan.idjab',
                    '=', 'jabatan.idjab')
                    ->select(
                        'aduan.no_aduan',
                        'aduan.image',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.idkategori',
                        'aduan.maklumbalas',
                        'aduan.id_pengadu',
                        'aduan.tarikh_aduan',
                        'aduan.masa_aduan',
                        'pengguna.id_pengadu',
                        'pengguna.nama',
                        'pengguna.idjab',
                        'pengguna.notel',
                        'pengguna.jawatan',
                        'jabatan.jabatan'

                    )
                    ->where('no_aduan',$no_aduan)->first();

        $tindakan = DB::table('kronologi')
                    ->select('tindakan_pegawai')
                    ->where('no_aduan', '=', $no_aduan)
                    ->where('idstatus', '=', '1')
                    ->first();

        $tinda = $tindakan->tindakan_pegawai;

        $pegawai = DB::table('pengguna')
                    ->select(
                        'idpengguna',
                        'nama'
                    )
                    ->where('idlevel', '=', '2')
                    ->get();


        return view('admin.edit_aduan', compact('aduan', 'pegawai', 'tinda'));
    }

    public function reject(Request $request, $no_aduan)
    {
        $dt = Carbon::now();

        $data = array();
        $data['maklumbalas'] = $request->maklumbalas;
        $data['id_pengguna'] = $request->id_pengguna;
        $data['tarikh_tindakan'] = $dt->toDateString();
        $data['masa_tindakan'] = $dt->toTimeString();
        $data['idstatus'] = 10;

        $aduan = DB::table('aduan')
                    ->where('no_aduan', $no_aduan)
                    ->update($data);

        //kronologi
        $data1 = array();
        $data1['idstatus'] = 10;
        $data1['maklumbalas'] = $request->maklumbalas;
        $data1['no_aduan'] = $no_aduan;
        $data1['tarikh_masa_skrg'] = $dt;
        $data1['id_pengadu'] = $request->id_pengadu;
        $data1['tindakan_pegawai'] = $request->nama;

        $kronologi = DB::table('kronologi')->insert($data1);

        if($aduan){

            $masalah = DB::table('aduan')->select('masalah')->where('no_aduan','=', $no_aduan)->first();
            $pengadu = DB::table('pengguna')
                        ->select('nama', 'email')
                        ->where('idpengguna', '=', $request->id_pengadu)
                        ->first();

            $to_name = $pengadu->nama;
            $to_email = $pengadu->email;

            $data3 = array(

                'no_aduan' => $no_aduan,
                'tajuk_aduan' => $masalah->masalah
            );

            /* $mail = new PHPMailer(true);


                        $mail->isSMTP();
                        $mail->Host       = env('MAIL_HOST');
                        $mail->SMTPAuth   = env('MAIL_AUTH');
                        $mail->Username   = env('MAIL_USERNAME');
                        $mail->Password   = env('MAIL_PASSWORD');
                        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                        $mail->Port       = env('MAIL_PORT');

                        //Recipients
                        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $mail->addAddress($to_email, $to_name);     // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN DITOLAK ('.$no_aduan.')';
                        $mail->Body    = '
                                        <p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato /YB/YBhg. Dato /Tuan/Puan,</p>

                                        <p>Aduan berikut telah ditolak:</p>

                                        <p>
                                            Tajuk Aduan: <strong>'.$masalah->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$no_aduan.'</strong>
                                        </p>

                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>
                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

            Mail::send('emails.tolakAduan', $data3, function($message) use ($to_name, $to_email, $no_aduan) {
                $message->to($to_email, $to_name)
                        ->subject('HELPDESK ICT: ADUAN DITOLAK ('.$no_aduan.')' );
                $message->from('noreply@penang.gov.my','Admin Helpdesk');
            });

            return redirect()->route('admin.aduantolak')->with('success', 'Aduan Berjaya Ditolak');
        }else{
            return redirect()->url('editaduan/'.$no_aduan)->with('error', 'Aduan Gagal Ditolak');
        }

    }

    public function agihan(Request $request, $no_aduan)
    {
        $dt = Carbon::now();

        $data = array();
        $data['idstatus'] = 4;
        $data['id_pengguna'] = $request->id_pengguna;
        $data['maklumbalas'] = $request->reason_agihan;

        $agihan = DB::table('aduan')
                    ->where('no_aduan', $no_aduan)
                    ->update($data);


        //kronologi
        $data1 = array();
        $data1['idstatus'] = 4;
        $data1['no_aduan'] = $no_aduan;
        $data1['maklumbalas'] = $request->reason_agihan;
        $data1['tarikh_masa_skrg'] = $dt;
        $data1['id_pengadu'] = $request->id_pengadu;
        $data1['tindakan_pegawai'] = $request->tindakan_pegawai;

        $kronologi = DB::table('kronologi')->insert($data1);


        $technician = DB::table('pengguna')
                    ->select('nama', 'email')
                    ->where('idpengguna', '=', $request->id_pengguna)
                    ->first();

        $pengadu = DB::table('pengguna')
                    ->select('nama')
                    ->where('idpengguna', '=', $request->id_pengadu)
                    ->first();


        if($agihan)
        {
            $to_name = $technician->nama;
            $to_email = $technician->email;

            $data3 = array(
                'technician' => $to_name,
                'pengadu' => $pengadu->nama,
                'no_aduan' => $no_aduan,
                'tajuk_aduan' => $request->masalah
            );

            /* $mail = new PHPMailer(true);


                        $mail->isSMTP();
                        $mail->Host       = env('MAIL_HOST');
                        $mail->SMTPAuth   = env('MAIL_AUTH');
                        $mail->Username   = env('MAIL_USERNAME');
                        $mail->Password   = env('MAIL_PASSWORD');
                        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                        $mail->Port       = env('MAIL_PORT');

                        //Recipients
                        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $mail->addAddress($to_email, $to_name);     // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN AGIHAN BARU ('.$no_aduan.')';
                        $mail->Body    = '
                                        <p>Salam Sejahtera,</p>
                                        <p>YAB/YB. Dato /YB/YBhg. Dato/Tuan/Puan,</p>
                                        <p>Aduan baru telah diagihkan kepada <strong>'.$to_name.'</strong> dan maklumat aduan seperti berikut:</p>
                                        <p>
                                            Nama: <strong>'.$pengadu->nama.'</strong><br>
                                            Tajuk Aduan: <strong>'.$request->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$no_aduan.'</strong>
                                        </p>
                                        <p>Sila klik pada pautan seperti di bawah untuk selesaikan aduan.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>

                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

            Mail::send('emails.agihTech', $data3, function($message) use ($to_name, $to_email, $no_aduan) {
                $message->to($to_email, $to_name)
                        ->subject('HELPDESK ICT: ADUAN AGIHAN BARU ('.$no_aduan.')' );
                $message->from('mnizam@penang.gov.my','Admin Helpdesk');
            });

            return redirect()->route('admin.aduanproses')->with('success', 'Aduan Berjaya Diagihkan');

        }else{
            return redirect()->route('admin.aduanbaru')->with('error', 'Aduan Gagal diTambah');
        }

    }

    public function maklumatAduan($id)
    {
        $aduan = DB::table('aduan')
                ->leftjoin('status', 'aduan.idstatus', '=',
                    'status.idstatus')
                ->leftjoin('pengguna', 'aduan.id_pengadu', '=',
                    'pengguna.id_pengadu')
                ->leftjoin('kategori', 'aduan.idkategori', '=',
                    'kategori.idkategori')
                ->leftjoin('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->leftjoin('subkategori', 'aduan.jenis_kategori', '=',
                    'subkategori.idsubkat')
                ->leftjoin('model', 'aduan.model', '=',
                    'model.idmodel')
                ->leftjoin('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->leftjoin('feedback_respon', 'feedback.respon_feedback', '=',
                    'feedback_respon.idrespon')
                ->select(
                    'aduan.no_aduan',
                    'aduan.maklumbalas',
                    'aduan.masalah',
                    'aduan.errormsg',
                    'aduan.idstatus',
                    'status.nama_status',
                    'pengguna.nama',
                    'pengguna.notel',
                    'pengguna.jawatan',
                    'jabatan.jabatan',
                    'aduan.jenis_kategori',
                    'subkategori.subkat',
                    'aduan.noinventori',
                    'aduan.model',
                    'model.model_name',
                    'aduan.tarikh_aduan',
                    'aduan.masa_aduan',
                    'aduan.maklumbalas',
                    'aduan.tarikh_tindakan',
                    'aduan.masa_tindakan',
                    'aduan.nosiri',
                    'aduan.id_pengguna',
                    'aduan.masa_respon',
                    'aduan.tarikh_respon',
                    'aduan.id_onsite',
                    'aduan.id_ganti',
                    'aduan.masa_onsite',
                    'aduan.tarikh_onsite',
                    'aduan.image',
                    'kategori.kategori',
                    'feedback_respon.respon_name',
                    'feedback.catatan',
                    'feedback.no_aduan_feedback'
                )
                ->where('id', '=', $id)
                ->first();

        $pegawai = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengguna', '=',
                        'pengguna.idpengguna')
                    ->select(
                        'pengguna.nama'
                    )
                    ->where('id', $id)
                    ->first();


        return view('admin.maklumatAduan', compact('aduan', 'pegawai'));
    }

    public function editProfil($idpengguna)
    {
        $profil = DB::table('pengguna')
                    ->join('jabatan', 'pengguna.idjab',
                        '=', 'jabatan.idjab')
                    ->select(
                        'pengguna.idpengguna',
                        'pengguna.nama',
                        'pengguna.no_kp',
                        'pengguna.no_ofis',
                        'pengguna.notel',
                        'pengguna.email',
                        'pengguna.jawatan'

                    )
                    ->where('idpengguna','=',  $idpengguna)
                    ->first();

        return view('admin.edit_profil', compact('profil'));

    }

    public function storeProfil(Request $request, $idpengguna)
    {
        $data = array();
        if(trim($request->password) == '')
        {
            $request->validate([
                'notel' => 'digits_between:10,13|numeric|required',
                'no_ofis' => 'digits_between:9,13|numeric|required'
            ]);

            $data['nama'] = $request->nama;
            $data['no_ofis'] = $request->no_ofis;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;

        } else {

            $request->validate([
                'password' => 'min:8|confirmed',
                'notel' => 'digits_between:10,13|numeric|required',
                'no_ofis' => 'digits_between:9,13|numeric|required'
            ]);


            $data['password'] = md5($request->password);
            $data['nama'] = $request->nama;
            $data['no_ofis'] = $request->no_ofis;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;
        }


        $profil = DB::table('pengguna')
                    ->where('idpengguna', $idpengguna)
                    ->update($data);

        if(($profil) | ($profil)==''){
            return redirect()->route('admin.profil')->with('success', 'Profil Anda Berjaya Dikemaskini');
        }else{
            return redirect()->url('edit_profil/'.$idpengguna)->with('error', 'Profil Anda Berjaya Dikemaskini');
        }

    }

    public function addPengguna()
    {
        $jabatan = DB::table('jabatan')
                    ->select(
                        'idjab',
                        'jabatan'
                    )
                    ->get();

        $bahagian = DB::table('bahagian')
                    ->select(
                        'idbahagian',
                        'bahagian'
                    )
                    ->get();

        return view('admin.add_pengguna', compact('jabatan', 'bahagian'));
    }

    function fetch(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = DB::table('bahagian')
                ->select('idbahagian', 'bahagian')
                ->where('idjab', $value)
                /* ->groupBy($dependent) */
                ->get();
        $output = '<option value="">Sila Pilih Bahagian </option>';
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->idbahagian.'">'.$row->bahagian.'</option>';
        }
        echo $output;
    }

    public function storePengguna(Request $request)
    {

        $pengguna = DB::table('pengguna')
                    ->select('idpengguna')
                    ->where('idpengguna', '=', $request->idpengguna)
                    ->first();


        if($pengguna === null)
        {
            $request->validate([
                'idpengguna' => 'digits_between:12,12|numeric|required',
                'password' => 'min:8|confirmed',
                'nama' => 'required',
                'jawatan' => 'required',
                'idjab' => 'required',
                'idbahagian' => 'required',
                'notel' => 'digits_between:10,13|numeric|required',
                'no_ofis' => 'digits_between:9,13|numeric|required',
                'email' => 'email|required'
            ]);

            $data = array();
            $data['idpengguna'] = $request->idpengguna;
            $data['id_pengadu'] = $request->idpengguna;
            $data['no_kp'] = $request->idpengguna;
            $data['username'] = $request->idpengguna;
            $data['password'] = md5($request->password);
            $data['idlevel'] = $request->idlevel;
            $data['nama'] = $request->nama;
            $data['jawatan'] = $request->jawatan;
            $data['idjab'] = $request->idjab;
            $data['idbahagian'] = $request->idbahagian;
            $data['notel'] = $request->notel;
            $data['no_ofis'] = $request->no_ofis;
            $data['email'] = $request->email;
            $data['aktif'] = $request->aktif;

            $pengguna = DB::table('pengguna')->insert($data);

        if($pengguna){

            return redirect()->route('admin.dashboard')->with('success', 'Pengguna Berjaya Didaftar');

        } else{

            return redirect()->route('admin.add_pengguna')->with('error', 'Pengguna Gagal Didaftar');

        }
        return redirect()->route('admin.dashboard')->with('success', 'Pengguna Berjaya Didaftar');
        }else{
            return redirect()->route('admin.add_pengguna')->with('error', 'Pengguna Telah Berdaftar');
        }
    }

    public function aktifPengguna()
    {
        $pengguna = DB::table('pengguna')
                    ->select(
                        'idpengguna',
                        'nama',
                        'email',
                        'aktif'
                    )
                    ->where('aktif', '=', '0')
                    ->latest('idpengguna')
                    ->paginate(5);

        return view('admin.aktifPengguna', compact('pengguna'));
    }

    public function aktif(Request $request)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $random = implode($pass); //turn the array into a string
        $ran_md5 = md5($random);



        $idpengguna = $request->idpengguna;
        $nama = $request->nama;
        $email = $request->email;

        $data = array();
        $data['password'] = $ran_md5;
        $data['temp_pass'] = 1;
        $data['aktif'] = $request->aktif;
        /* dd($idpengguna); */

        $aktif = DB::table('pengguna')
                    ->where('idpengguna', $idpengguna)
                    ->update($data);

        if($aktif)
            {

                $to_name = $nama;
                $to_email = $email;

                $data3 = array(
                    'nama' => $nama,
                    'email' => $email,
                    'pass' => $random

                );

                /* $mail = new PHPMailer(true);


                        $mail->isSMTP();
                        $mail->Host       = env('MAIL_HOST');
                        $mail->SMTPAuth   = env('MAIL_AUTH');
                        $mail->Username   = env('MAIL_USERNAME');
                        $mail->Password   = env('MAIL_PASSWORD');
                        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                        $mail->Port       = env('MAIL_PORT');

                        //Recipients
                        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $mail->addAddress($to_email, $to_name);     // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: AKAUN AKTIF';
                        $mail->Body    = '
                                        <p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>
                                        <p>Akaun pengguna telah diaktifkan. Selamat datang ke Helpdesk ICT. <br>
                                        Maklumat pengguna seperti :</p>

                                        <p>
                                            Nama: <strong>'.$nama.'</strong><br>
                                            Emel: <strong>'.$email.'</strong><br>
                                            Katalaluan: <strong>'.$random.'</strong><br>

                                        </p>
                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>

                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

                Mail::send('emails.aktifPengguna', $data3, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('HELPDESK ICT: AKAUN AKTIF' );
                    $message->from('miymy08@gmail.com','Admin Helpdesk');
                });

                return redirect()->route('admin.aktifPengguna')->with('success', 'Pengguna Berjaya Diaktifkan');
            }else{
                return redirect()->route('admin.aktifPengguna')->with('error', 'Pengguna Gagal Diaktifkan');
            }
    }

    public function cariAduan()
    {
        return view('admin.cariAduan');
    }

    public function search(Request $request)
    {
        if($request->ajax())
        {
        $output="";
        $aduans=DB::table('aduan')->select('aduan.id','aduan.no_aduan', 'aduan.masalah', 'jabatan.jabatan', 'aduan.tarikh_aduan')
        ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
        ->where('no_aduan','LIKE','%'.$request->search."%")
        ->orwhere('masalah','LIKE','%'.$request->search."%")
        ->get();
        if($aduans)
        {
        foreach ($aduans as $key => $aduan) {
        $output.='<tr>'.
        '<td>'.$aduan->no_aduan.'</td>'.
        '<td>'.$aduan->masalah.'</td>'.
        '<td>
            <a type="button" title="Maklumat Aduan" class="btn btn-block btn-info btn-sm"
                href="maklumatAduan/'.$aduan->id.'">
                <i class="fas fa-info"></i>
            </a>
        </td>'.
        '</tr>';
        }
        return Response($output);
        }
        };
    }

    public function semakRekod()
    {
        return view('admin.semakRekod');
    }

    public function rekod(Request $request)
    {
        if($request->ajax())
        {
            $output="";
            $penggunas=DB::table('pengguna')
                    ->leftjoin('jabatan', 'pengguna.idjab', '=',
                     'jabatan.idjab')
                     ->leftjoin('bahagian', 'pengguna.idbahagian', '=',
                     'bahagian.idbahagian')
                    ->select(
                        'pengguna.idpengguna',
                        'pengguna.nama',
                        'pengguna.jawatan',
                        'pengguna.notel',
                        'pengguna.email',
                        'jabatan.jabatan',
                        'bahagian.bahagian'
                        )
                    ->where('aktif', '=', '1')
                    ->where('idpengguna','LIKE','%'.$request->rekod."%")->get();

            $rekod = $request->rekod;

            if($penggunas)
            {
            foreach ($penggunas as $key => $pengguna) {
            $output.=
            '
             <tr>'.
            '<td>'.$pengguna->nama.'</td>'.
            '<td>'.$pengguna->jawatan.'</td>'.
            '<td>'.$pengguna->jabatan.'</td>'.
            '<td>'.$pengguna->bahagian.'</td>'.
            '<td>'.$pengguna->notel.'</td>'.
            '<td>'.$pengguna->email.'</td>'.
            '</tr>
            ';
            }
            return Response($output);
            }
        };
    }

    public function rekod2(Request $request)
    {
        if($request->ajax())
        {
            $output="";
            $kronologi=DB::table('pengguna')
                    ->leftjoin('kronologi', 'pengguna.id_pengadu', '=',
                     'kronologi.id_pengadu')
                     ->leftjoin('aduan', 'kronologi.no_aduan', '=',
                     'aduan.no_aduan')
                    ->select(
                        'kronologi.no_aduan',
                        'kronologi.tarikh_masa_skrg',
                        'kronologi.tindakan_pegawai',
                        'aduan.masalah'
                        )
                    ->where('aktif', '=', '1')
                    ->where('kronologi.idstatus', '=', '3')
                    ->where('idpengguna','LIKE','%'.$request->rekod."%")
                    ->latest('kronologi.no_aduan')
                    ->get();

            if($kronologi)
            {

            foreach ($kronologi as $key => $kro) {

            $output.=
            '<tr>'.
            '<td>'.$kro->no_aduan.'</td>'.
            '<td>'.$kro->tarikh_masa_skrg.'</td>'.
            '<td>'.$kro->masalah.'</td>'.
            '<td>'.$kro->tindakan_pegawai.'</td>'.
            '</tr>
            <br>'
            ;
            }
            return Response($output);
            }

        };
    }

    public function listTeknikal()
    {
        $teknikal = DB::table('pengguna')
                    ->join('jabatan', 'pengguna.idjab', '=',
                        'jabatan.idjab')
                    ->select(
                        'pengguna.nama',
                        'pengguna.notel',
                        'pengguna.email',
                        'pengguna.jawatan',
                        'jabatan.jabatan'
                    )
                    ->whereIn('idlevel', ['2','6'])
                    ->where('aktif', '1')
                    ->paginate(10);

        return view('admin.listTeknikal', compact('teknikal'));
    }

    public function statistikTahunan()
    {
        $start = 2017;
        $end = 2021;
        $i = 1;
        $m_end = $end - $i;

        //count aduan by tahun
        $query = DB::table('aduan')
                    ->select('*')
                    ->whereyear('tarikh_aduan','>=', $start)
                    ->whereyear('tarikh_aduan','<=', $end)
                    ->count();

                    //
                    /* $query = DB::table('aduan')
                    ->select('*')
                    ->whereyear('tarikh_aduan','>=', $start)
                    ->whereyear('tarikh_aduan','<=', $end)
                    ->get(); */

        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2016';

       /*  $totalAduan = DB::table('aduan')
                    ->select([
                        DB::raw('YEAR(tarikh_aduan) as year'),
                        DB::raw('COUNT(*) as totalAduan')
                        ])
                    ->whereyear('tarikh_aduan','>=', $start)
                    ->whereyear('tarikh_aduan','<=', $end)
                    ->where('idstatus','=', '3')
                    ->GROUPBY('year')
                    ->get(); */


        return view('admin.statistikTahunan', compact('query', 'tahun_now', 'tahun_awal'));
    }

    public function tahunan(Request $request)
    {
        if($request->ajax()){

            $output="";
            $tahun_mula = $request->start;
            $tahun_akhir = $request->end;



            if ($tahun_mula=='' && $tahun_akhir=='') {

                $tahun_mula=$year;
                $tahun_akhir=$year;
                }

                elseif ($tahun_mula!='' && $tahun_akhir=='') {
                $tahun_mula=$tahun_mula;
                $tahun_akhir=$tahun_mula;

                }

                elseif ($tahun_mula=='' && $tahun_akhir!='') {
                $tahun_mula=$tahun_akhir;
                $tahun_akhir=$tahun_akhir;
                }



        $totalAduan = DB::table('aduan')
                    ->select([
                    DB::raw('YEAR(tarikh_aduan) as year'),
                    DB::raw('COUNT(*) as totalAduan')
                    ])
                    ->whereyear('tarikh_aduan','>=', $tahun_mula)
                    ->whereyear('tarikh_aduan','<=', $tahun_akhir)
                    ->GROUPBY('year')
                    ->get();

        $jumlah = 0;

        $totalBaru =0;
        $totalAgihan =0;
        $totalTindakan =0;
        $totalSelesai =0;
        $performance = 0;
        $totalPerformance = 0;
                    /* dd($status); */

        $output.=
                '
                <h5>Statistik Helpdesk ICT Tahunan '.$tahun_mula.' - '.$tahun_akhir.'</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Jumlah Diterima</th>
                                        <th>Baru</th>
                                        <th>Agihan</th>
                                        <th>Dalam Tindakan/Pembekal</th>
                                        <th>Selesai</th>
                                        <th>Prestasi (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';



        foreach ($totalAduan as $total) {

            $baru = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '1')
                    ->count();

            $agihan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '4')
                    ->count();

            $tindakan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '9')
                    ->count();

            $selesai = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '3')
                    ->count();


                $jumlah = $jumlah + $total->totalAduan;
                $totalBaru= $totalBaru + $baru;
                $totalAgihan= $totalAgihan + $agihan;
                $totalTindakan= $totalTindakan + $tindakan;
                $totalSelesai= $totalSelesai + $selesai;

                $performance = ($selesai/$total->totalAduan)*100;
                $totalPerformance = ($totalSelesai/$jumlah)*100;

        $output.=
            '
            <tr class="text-center">'.
            '<td>'.$total->year.'</td>'.
            '<td>'.$total->totalAduan.'</td>
            <td>'.$baru.'</td>
            <td>'.$agihan.'</td>
            <td>'.$tindakan.'</td>
            <td>'.$selesai.'</td>
            <td>'.round($performance).'</td>
            ';
        }

        $output.=
            '
            </tr>
            ';

        $output.=
            '
            <tr class="text-center">'.
            '<td>Jumlah</td>'.
            '<td>'.$jumlah.'</td>'.
            '<td>'.$totalBaru.'</td>'.
            '<td>'.$totalAgihan.'</td>'.
            '<td>'.$totalTindakan.'</td>'.
            '<td>'.$totalSelesai.'</td>'.
            '<td>'.round($totalPerformance).'</td>'.
            '</tr>
            ';

        $output .=
                '
                </tbody>
                </table>
                <br>
                <a href="statistikTahunan/pdf/'.$tahun_mula.'/'.$tahun_akhir.'" target="_blank" class="btn btn-danger">Convert into PDF</a>
                <a href="statistikTahunan/csv/'.$tahun_mula.'/'.$tahun_akhir.'" target="_blank" class="btn btn-success">Convert into CSV</a>
                ';

        return Response($output);

        //Aduan semua by tahun
        /* $totalAduan = DB::table('aduan')
                    ->select([
                        DB::raw('count(*) as totalAduan','year(tarikh_aduan)')])
                    ->whereyear('tarikh_aduan','>=', $tahun_mula)
                    ->whereyear('tarikh_aduan','<=', $tahun_akhir)
                    ->get(); */

        //bilangan aduan baru by tahun
        /* $totalBaru = DB::table('aduan')
                    ->select([DB::raw('count(*) as totalBaru')])
                    ->where('idstatus', '=', '1')
                    ->whereyear('tarikh_aduan','>=', $tahun_mula)
                    ->whereyear('tarikh_aduan','<=', $tahun_akhir)
                    ->get(); */

        };
    }

    public function pdf($tahun_mula,$tahun_akhir)
    {

        $output= '';
        $totalAduan = DB::table('aduan')
                    ->select([
                    DB::raw('YEAR(tarikh_aduan) as year'),
                    DB::raw('COUNT(*) as totalAduan')
                    ])
                    ->whereyear('tarikh_aduan','>=', $tahun_mula)
                    ->whereyear('tarikh_aduan','<=', $tahun_akhir)
                    ->GROUPBY('year')
                    ->get();

        $jumlah = 0;

        $totalBaru =0;
        $totalAgihan =0;
        $totalTindakan =0;
        $totalSelesai =0;
        $performance = 0;
        $totalPerformance = 0;
                    /* dd($status); */



        $output.=
                '
                <style>
                .h3,h3{font-size:1.75rem}
                .table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    color: #212529;
                    border-collapse: collapse;
                    background-color: transparent;
                  }

                  .table th,
                  .table td {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                  }

                  .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #dee2e6;
                  }

                  .table tbody + tbody {
                    border-top: 1px solid #dee2e6;
                  }
                .table-bordered {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered th,
                  .table-bordered td {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered thead th,
                  .table-bordered thead td {
                    border-bottom-width: 2px;
                  }
                  .text-center {
                    text-align: center !important;
                  }
                  .table.text-center,
                  .table.text-center td,
                  .table.text-center th {
                   text-align: center;
                  }
                </style>
                <center><h3>Statistik Helpdesk ICT Tahunan '.$tahun_mula.' - '.$tahun_akhir.'</h3></center>
                            <br>
                                <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Jumlah Diterima</th>
                                        <th>Baru</th>
                                        <th>Agihan</th>
                                        <th>Dalam Tindakan/Pembekal</th>
                                        <th>Selesai</th>
                                        <th>Prestasi (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';



        foreach ($totalAduan as $total) {

            $baru = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '1')
                    ->count();

            $agihan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '4')
                    ->count();

            $tindakan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '9')
                    ->count();

            $selesai = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '3')
                    ->count();


                $jumlah = $jumlah + $total->totalAduan;
                $totalBaru= $totalBaru + $baru;
                $totalAgihan= $totalAgihan + $agihan;
                $totalTindakan= $totalTindakan + $tindakan;
                $totalSelesai= $totalSelesai + $selesai;

                $performance = ($selesai/$total->totalAduan)*100;
                $totalPerformance = ($totalSelesai/$jumlah)*100;

        $output.=
            '
            <tr class="text-center">'.
            '<td>'.$total->year.'</td>'.
            '<td>'.$total->totalAduan.'</td>
            <td>'.$baru.'</td>
            <td>'.$agihan.'</td>
            <td>'.$tindakan.'</td>
            <td>'.$selesai.'</td>
            <td>'.round($performance).'</td>
            ';
        }

        $output.=
            '
            </tr>
            ';

        $output.=
            '
            <tr class="text-center">'.
            '<td>Jumlah</td>'.
            '<td>'.$jumlah.'</td>'.
            '<td>'.$totalBaru.'</td>'.
            '<td>'.$totalAgihan.'</td>'.
            '<td>'.$totalTindakan.'</td>'.
            '<td>'.$totalSelesai.'</td>'.
            '<td>'.round($totalPerformance).'</td>'.
            '</tr>
            ';

        $output .=
                '
                </tbody>
                </table>

                ';
        $title = 'Statistik Helpdesk ICT Tahun';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($output);
        return $pdf->stream($title.' '.$tahun_mula.'-'.$tahun_akhir.'.pdf');

    }

    public function csv($tahun_mula,$tahun_akhir)
    {
        $totalAduan = DB::table('aduan')
                    ->select([
                    DB::raw('YEAR(tarikh_aduan) as year'),
                    DB::raw('COUNT(*) as totalAduan')
                    ])
                    ->whereyear('tarikh_aduan','>=', $tahun_mula)
                    ->whereyear('tarikh_aduan','<=', $tahun_akhir)
                    ->GROUPBY('year')
                    ->get();


        $fileName = 'Statistik Helpdesk ICT Tahun '.$tahun_mula.'-'.$tahun_akhir.'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tahun', 'Jumlah Diterima', 'Baru', 'Agihan', 'Dalam Tindakan/Pembekal', 'Selesai', 'Prestasi (%)');
        $callback = function() use($columns,$totalAduan) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        $jumlah = 0;

        $totalBaru =0;
        $totalAgihan =0;
        $totalTindakan =0;
        $totalSelesai =0;
        $performance = 0;
        $totalPerformance = 0;

        foreach($totalAduan as $total){

            $baru = DB::table('aduan')
            ->select('idstatus')
            ->whereyear('tarikh_aduan','=', $total->year)
            ->Where('idstatus', '=', '1')
            ->count();

            $agihan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '4')
                    ->count();

            $tindakan = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '9')
                    ->count();

            $selesai = DB::table('aduan')
                    ->select('idstatus')
                    ->whereyear('tarikh_aduan','=', $total->year)
                    ->Where('idstatus', '=', '3')
                    ->count();

                $jumlah = $jumlah + $total->totalAduan;
                $totalBaru= $totalBaru + $baru;
                $totalAgihan= $totalAgihan + $agihan;
                $totalTindakan= $totalTindakan + $tindakan;
                $totalSelesai= $totalSelesai + $selesai;

                $performance = ($selesai/$total->totalAduan)*100;
                $totalPerformance = ($totalSelesai/$jumlah)*100;


        fputcsv($file, array($total->year, $total->totalAduan, $baru, $agihan, $tindakan, $selesai, round($performance)));
        }

        fputcsv($file, array('Jumlah', $jumlah, $totalBaru, $totalAgihan, $totalTindakan, $totalSelesai, round($totalPerformance)));

        fclose($file);

        };

        return response()->stream($callback, 200, $headers);
    }

    public function statistikKategori()
    {
        $start = 2017;
        $end = 2021;
        $i = 1;
        $m_end = $end - $i;

        //count aduan by tahun
        /* $query = DB::table('aduan')
                    ->select('*')
                    ->whereyear('tarikh_aduan','>=', $start)
                    ->whereyear('tarikh_aduan','<=', $end)
                    ->count(); */

                    //
                    /* $query = DB::table('aduan')
                    ->select('*')
                    ->whereyear('tarikh_aduan','>=', $start)
                    ->whereyear('tarikh_aduan','<=', $end)
                    ->get(); */

                    /* $query = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                        'kategori.idkategori')
                    ->select([ 'kategori.kategori', 'aduan.idkategori',
                    DB::raw('COUNT(aduan.id) as totalKat')
                    ])
                    ->whereyear('tarikh_aduan','=', $start)
                    ->GROUPBY('aduan.idkategori')
                    ->ORDERBY('totalKat', 'DESC')
                    ->get(); */

        $kategori = DB::table('kategori')
                    ->select('idkategori','kategori')
                    ->get();

        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2016';

        return view('admin.statistikKategori', compact('tahun_now', 'tahun_awal', 'kategori'));
    }

    public function sublist(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = DB::table('subkategori')
                ->select('idsubkat', 'subkat')
                ->where('idkategori', $value)
                /* ->groupBy($dependent) */
                ->get();
        $output = '<option value="">Sila Pilih Subkategori </option>';
        $output .= '<option value="Semua">Semua </option>';
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->idsubkat.'">'.$row->subkat.'</option>';
        }
        echo $output;
    }

    public function kategori(Request $request)
    {
        if($request->ajax()){

            $output="";
            $tahun = $request->tahun;
            $idkategori = $request->idkategori;


            if($idkategori != '')
            {

                $totalKategori = DB::table('subkategori')
                            ->join('aduan', 'aduan.jenis_kategori', '=',
                                'subkategori.idsubkat')
                            ->select([ 'subkategori.subkat AS kategori', 'aduan.idkategori',
                            DB::raw('COUNT(*) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idkategori', '=', $idkategori)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('subkategori.subkat')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();

                $test = DB::table('kategori')
                        ->select('kategori')
                        ->where('idkategori', '=', $idkategori)
                        ->first();
                $kategori = $idkategori;
                $jenis = $test->kategori;

            }else{
                $jenis = 'Semua';
                $kategori= '0';
                $totalKategori = DB::table('kategori')
                            ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                            ->select([ 'kategori.kategori', 'aduan.idkategori',
                            DB::raw('COUNT(aduan.id) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('kategori.kategori')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();
            }

        $output.=
                '
                <h5>Statistik Helpdesk ICT Kategori '.$jenis.' Tahun '.$tahun.'</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Kategori</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';
            $bln1 = '01';   $bln7 = '07';
            $bln2 = '02';   $bln8 = '08';
            $bln3 = '03';   $bln9 = '09';
            $bln4 = '04';   $bln10 = '10';
            $bln5 = '05';   $bln11 = '11';
            $bln6 = '06';   $bln12 = '12';

        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalKategori as $total) {

            $jan = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

            $feb = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mac = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $apr = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mei = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $jun = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $julai = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $ogos = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $sep = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $okt = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $nov = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $dis = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalKat;



        $output.=
            '
            <tr>'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->kategori.'</td>
            <div class="text-center">
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$jan.'</a></td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$feb.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$mac.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$apr.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$mei.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$jun.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$julai.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$ogos.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$sep.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$okt.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$nov.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$dis.'</td>
            <td class="text-center"><a href="kategoriDetail/'.$total->idkategori.'/'.$tahun.'/'.$bln.'" target="_blank">'.$total->totalKat.'</td>
            </div>
            ';
        }

        $output.=
            '
            </tr>
            ';
        $kat0 = 0;
            $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$totalJan.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$totalFeb.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$totalMac.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$totalApr.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$totalMei.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$totalJun.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$totalJul.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$totalOgo.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$totalSep.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$totalOkt.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$totalNov.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$totalDis.'</td>'.
            '<td><a href="kategoriDetail/'.$kat0.'/'.$tahun.'/'.$bln.'" target="_blank">'.$totalJumlah.'</td>'.
            '</tr>
            ';

        $output .=
                '
                </tbody>
                </table>
                <a href="statistikKategori/pdf/'.$tahun.'/'.$kategori.'" target="_blank" class="btn btn-danger">Convert into PDF</a>
                <a href="statistikKategori/csv/'.$tahun.'/'.$kategori.'" target="_blank" class="btn btn-success">Convert into CSV</a>
                ';

        return Response($output);

        };
    }

    public function pdfKategori($tahun,$kategori)
    {
        $output = '';
        $idkategori = $kategori;

        if($idkategori != '0')
            {

                $totalKategori = DB::table('subkategori')
                            ->join('aduan', 'aduan.jenis_kategori', '=',
                                'subkategori.idsubkat')
                            ->select([ 'subkategori.subkat AS kategori', 'aduan.idkategori',
                            DB::raw('COUNT(*) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idkategori', '=', $idkategori)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('subkategori.subkat')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();

                $test = DB::table('kategori')
                        ->select('kategori')
                        ->where('idkategori', '=', $idkategori)
                        ->first();
                $kategori = $idkategori;
                $jenis = $test->kategori;

            }else{
                $jenis = 'Semua';
                $kategori= '0';
                $totalKategori = DB::table('kategori')
                            ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                            ->select([ 'kategori.kategori', 'aduan.idkategori',
                            DB::raw('COUNT(aduan.id) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('kategori.kategori')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();
            }

        $output.=
        '
        <style>
                .h3,h3{font-size:1.75rem}
                .table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    color: #212529;
                    border-collapse: collapse;
                    background-color: transparent;
                  }

                  .table th,
                  .table td {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                  }

                  .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #dee2e6;
                  }

                  .table tbody + tbody {
                    border-top: 1px solid #dee2e6;
                  }
                .table-bordered {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered th,
                  .table-bordered td {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered thead th,
                  .table-bordered thead td {
                    border-bottom-width: 2px;
                  }
                  .text-center {
                    text-align: center !important;
                  }
                  .table.text-center,
                  .table.text-center td,
                  .table.text-center th {
                   text-align: center;
                  }
                </style>
        <center><h3>Statistik Helpdesk ICT Kategori '.$jenis.' Tahun '.$tahun.'</h3></center>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>Bil</th>
                                <th>Kategori</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAC</th>
                                <th>APR</th>
                                <th>MEI</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>OGOS</th>
                                <th>SEP</th>
                                <th>OKT</th>
                                <th>NOV</th>
                                <th>DIS</th>
                                <th>JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
        ';


        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;


        foreach ($totalKategori as $total) {

            $jan = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

            $feb = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mac = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $apr = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mei = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $jun = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $julai = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $ogos = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $sep = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $okt = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $nov = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $dis = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalKat;



        $output.=
            '
            <tr>'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->kategori.'</td>
            <td class="text-center">'.$jan.'</a></td>
            <td class="text-center">'.$feb.'</td>
            <td class="text-center">'.$mac.'</td>
            <td class="text-center">'.$apr.'</td>
            <td class="text-center">'.$mei.'</td>
            <td class="text-center">'.$jun.'</td>
            <td class="text-center">'.$julai.'</td>
            <td class="text-center">'.$ogos.'</td>
            <td class="text-center">'.$sep.'</td>
            <td class="text-center">'.$okt.'</td>
            <td class="text-center">'.$nov.'</td>
            <td class="text-center">'.$dis.'</td>
            <td class="text-center">'.$total->totalKat.'</td>
            </tr>
            ';
        }

        $kat0 = 0;
            $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td>'.$totalJan.'</td>'.
            '<td>'.$totalFeb.'</td>'.
            '<td>'.$totalMac.'</td>'.
            '<td>'.$totalApr.'</td>'.
            '<td>'.$totalMei.'</td>'.
            '<td>'.$totalJun.'</td>'.
            '<td>'.$totalJul.'</td>'.
            '<td>'.$totalOgo.'</td>'.
            '<td>'.$totalSep.'</td>'.
            '<td>'.$totalOkt.'</td>'.
            '<td>'.$totalNov.'</td>'.
            '<td>'.$totalDis.'</td>'.
            '<td>'.$totalJumlah.'</td>'.
            '</tr>
            </tbody>
            </table>
            ';


        $title = 'Statistik Helpdesk ICT Kategori Tahun';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($output)->setPaper('a4', 'landscape');
        return $pdf->stream($title.' '.$tahun.'.pdf');
    }

    public function csvKategori($tahun,$kategori)
    {

        $idkategori = $kategori;

        if($idkategori != '0')
            {

                $totalKategori = DB::table('subkategori')
                            ->join('aduan', 'aduan.jenis_kategori', '=',
                                'subkategori.idsubkat')
                            ->select([ 'subkategori.subkat AS kategori', 'aduan.idkategori',
                            DB::raw('COUNT(*) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idkategori', '=', $idkategori)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('subkategori.subkat')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();

                $test = DB::table('kategori')
                        ->select('kategori')
                        ->where('idkategori', '=', $idkategori)
                        ->first();
                $kategori = $idkategori;
                $jenis = $test->kategori;

            }else{
                $jenis = 'Semua';
                $kategori= '0';
                $totalKategori = DB::table('kategori')
                            ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                            ->select([ 'kategori.kategori', 'aduan.idkategori',
                            DB::raw('COUNT(aduan.id) as totalKat')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idkategori')
                            ->GROUPBY('kategori.kategori')
                            ->ORDERBY('totalKat', 'DESC')
                            ->get();
            }



        $fileName = 'Statistik Helpdesk ICT Kategori '.$jenis.' Tahun '.$tahun.'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
                        'BIL', 'KATEGORI', 'JAN', 'FEB', 'MAC',
                        'APR', 'MEI', 'JUN', 'JUL', 'OGOS',
                        'SEP', 'OKT', 'NOV', 'DIS', 'JUMLAH'
                    );

        $callback = function() use($columns,$totalKategori,$tahun) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

            $bil =0;

            $totalJan =0;  $totalJul =0;
            $totalFeb =0;  $totalOgo =0;
            $totalMac =0;  $totalSep =0;
            $totalApr =0;  $totalOkt =0;
            $totalMei =0;  $totalNov =0;
            $totalJun =0;  $totalDis =0;
            $totalJumlah = 0;


        foreach($totalKategori as $total){

                    $jan = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $feb = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mac = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $apr = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $mei = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $jun = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $julai = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $ogos = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $sep = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $okt = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $nov = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();

                    $dis = DB::table('kategori')
                    ->join('aduan', 'aduan.idkategori', '=',
                                'kategori.idkategori')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idkategori', '=', $total->idkategori)
                    ->count();


        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalKat;


        fputcsv($file, array(
                            $bil, $total->kategori, $jan, $feb, $mac,
                            $apr, $mei, $jun, $julai, $ogos,
                            $sep, $okt, $nov, $dis, $total->totalKat
                        ));
        }

        fputcsv($file, array(
                            '', 'Jumlah', $totalJan, $totalFeb, $totalMac,
                            $totalApr, $totalMei, $totalJun, $totalJul, $totalOgo,
                            $totalSep, $totalOkt, $totalNov, $totalDis, $totalJumlah

                        ));

        fclose($file);

        };

        return response()->stream($callback, 200, $headers);
    }

    public function kategoriDetail($idkategori, $tahun, $bln)
    {

        if(($idkategori == 0) && ($bln == 0))
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan',
                    'jabatan.jabatan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $bulan = '';
                $tajuk = 'Semua';
        }
        elseif($idkategori == 0)
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan',
                    'jabatan.jabatan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tajuk = 'Semua';

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}

        }
        elseif($bln == 0)
        {
            $aduan = DB::table('aduan')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->select(
                        'aduan.id',
                        'aduan.no_aduan',
                        'aduan.masalah',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'jabatan.jabatan'
                    )
                    ->where('aduan.idkategori','=',$idkategori)
                    ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                    ->orderBy('aduan.no_aduan', 'DESC')
                    ->paginate(5);

            $kategori = DB::table('kategori')
                        ->select('kategori')
                        ->where('idkategori', '=', $idkategori)
                        ->first();

            $bulan = '';
            $tajuk = $kategori->kategori;
        }
        else
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan',
                    'jabatan.jabatan'
                )
                ->where('aduan.idkategori','=',$idkategori)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $kategori = DB::table('kategori')
                            ->select('kategori')
                            ->where('idkategori', '=', $idkategori)
                            ->first();

                $tajuk = $kategori->kategori;

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}

        }

        return view('admin.kategoriDetail', compact('aduan', 'tajuk', 'tahun', 'bulan'));
    }

    public function statistikJabatan()
    {
        $start = 2017;
        $end = 2021;
        $i = 1;
        $m_end = $end - $i;

        $jabatan = DB::table('jabatan')
                    ->select('idjab','jabatan')
                    ->get();

        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2016';

        return view('admin.statistikJabatan', compact('tahun_now', 'tahun_awal', 'jabatan'));
    }

    public function jabatan(Request $request)
    {
        if($request->ajax()){

            $output="";

            $tahun = $request->tahun;
            $idjab = $request->idjab;

            if($idjab != '')
            {

                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idjab', '=', $idjab)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();

                $test = DB::table('jabatan')
                        ->select('jabatan')
                        ->where('idjab', '=', $idjab)
                        ->first();

                $jenis = $test->jabatan;
                $jabatan = $idjab;

            }else{
                $jenis = 'Semua Jabatan';
                $jabatan = '0';
                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();
            }

        $output.=
                '
                <h5>Statistik Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Jabatan</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';

            $bln1 = '01';   $bln7 = '07';
            $bln2 = '02';   $bln8 = '08';
            $bln3 = '03';   $bln9 = '09';
            $bln4 = '04';   $bln10 = '10';
            $bln5 = '05';   $bln11 = '11';
            $bln6 = '06';   $bln12 = '12';

        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalJabatan as $total) {

            $jan = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $feb = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mac = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $apr = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mei = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $jun = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $julai = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $ogos = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $sep = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $okt = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $nov = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $dis = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalJab;

        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->jabatan.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$jan.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$feb.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$mac.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$apr.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$mei.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$jun.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$julai.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$ogos.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$sep.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$okt.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$nov.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$dis.'</td>
            <td class="text-center"><a href="jabatanDetail/'.$total->idjab.'/'.$tahun.'/'.$bln.'" target="_blank">'.$total->totalJab.'</td>
            </tr>
            ';
        }

        $kat0 = 0;
            $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$totalJan.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$totalFeb.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$totalMac.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$totalApr.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$totalMei.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$totalJun.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$totalJul.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$totalOgo.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$totalSep.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$totalOkt.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$totalNov.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$totalDis.'</td>'.
            '<td><a href="jabatanDetail/'.$kat0.'/'.$tahun.'/'.$bln.'" target="_blank">'.$totalJumlah.'</td>'.
            '</tr>
            ';

        $output .=
                '
                </tbody>
                </table>
                <a href="statistikJabatan/pdf/'.$tahun.'/'.$jabatan.'" target="_blank" class="btn btn-danger">Convert into PDF</a>
                <a href="statistikJabatan/csv/'.$tahun.'/'.$jabatan.'" target="_blank" class="btn btn-success">Convert into CSV</a>
                ';

        return Response($output);

        };
    }

    public function pdfJabatan($tahun,$jabatan)
    {
        $output = '';
        $idjab = $jabatan;
        if($idjab != '0')
            {

                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idjab', '=', $idjab)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();

                $test = DB::table('jabatan')
                        ->select('jabatan')
                        ->where('idjab', '=', $idjab)
                        ->first();

                $jenis = $test->jabatan;
                $jabatan = $idjab;

            }else{
                $jenis = 'Semua Jabatan';
                $jabatan = '0';
                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();
            }

        $output.=
                '
                <style>
                .h3,h3{font-size:1.75rem}
                .table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    color: #212529;
                    border-collapse: collapse;
                    background-color: transparent;
                  }

                  .table th,
                  .table td {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                  }

                  .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #dee2e6;
                  }

                  .table tbody + tbody {
                    border-top: 1px solid #dee2e6;
                  }
                .table-bordered {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered th,
                  .table-bordered td {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered thead th,
                  .table-bordered thead td {
                    border-bottom-width: 2px;
                  }
                  .text-center {
                    text-align: center !important;
                  }
                  .table.text-center,
                  .table.text-center td,
                  .table.text-center th {
                   text-align: center;
                  }
                </style>
                <center><h3>Statistik Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h3></center>
                <br>
                            <table class="table table-bordered ">
                                <thead >
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Jabatan</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';

            $bln1 = '01';   $bln7 = '07';
            $bln2 = '02';   $bln8 = '08';
            $bln3 = '03';   $bln9 = '09';
            $bln4 = '04';   $bln10 = '10';
            $bln5 = '05';   $bln11 = '11';
            $bln6 = '06';   $bln12 = '12';

        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalJabatan as $total) {

            $jan = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $feb = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mac = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $apr = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mei = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $jun = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $julai = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $ogos = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $sep = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $okt = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $nov = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $dis = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalJab;

        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->jabatan.'</td>
            <td class="text-center">'.$jan.'</td>
            <td class="text-center">'.$feb.'</td>
            <td class="text-center">'.$mac.'</td>
            <td class="text-center">'.$apr.'</td>
            <td class="text-center">'.$mei.'</td>
            <td class="text-center">'.$jun.'</td>
            <td class="text-center">'.$julai.'</td>
            <td class="text-center">'.$ogos.'</td>
            <td class="text-center">'.$sep.'</td>
            <td class="text-center">'.$okt.'</td>
            <td class="text-center">'.$nov.'</td>
            <td class="text-center">'.$dis.'</td>
            <td class="text-center">'.$total->totalJab.'</td>
            </tr>
            ';
        }

        $kat0 = 0;
            $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td>'.$totalJan.'</td>'.
            '<td>'.$totalFeb.'</td>'.
            '<td>'.$totalMac.'</td>'.
            '<td>'.$totalApr.'</td>'.
            '<td>'.$totalMei.'</td>'.
            '<td>'.$totalJun.'</td>'.
            '<td>'.$totalJul.'</td>'.
            '<td>'.$totalOgo.'</td>'.
            '<td>'.$totalSep.'</td>'.
            '<td>'.$totalOkt.'</td>'.
            '<td>'.$totalNov.'</td>'.
            '<td>'.$totalDis.'</td>'.
            '<td>'.$totalJumlah.'</td>'.
            '</tr>
            </tbody>
            </table>
            ';


        $title = 'Statistik Helpdesk ICT Jabatan Tahun';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($output)->setPaper('a4', 'landscape');
        return $pdf->stream($title.' '.$tahun.'.pdf');
    }

    public function csvJabatan($tahun,$jabatan)
    {
        $idjab = $jabatan;
        if($idjab != '0')
            {

                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->where('aduan.idjab', '=', $idjab)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();

                $test = DB::table('jabatan')
                        ->select('jabatan')
                        ->where('idjab', '=', $idjab)
                        ->first();

                $jenis = $test->jabatan;
                $jabatan = $idjab;

            }else{
                $jenis = 'Semua Jabatan';
                $jabatan = '0';
                $totalJabatan = DB::table('jabatan')
                            ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                            ->select([ 'jabatan.jabatan', 'aduan.idjab',
                            DB::raw('COUNT(aduan.id) as totalJab')
                            ])
                            ->whereyear('tarikh_aduan','=', $tahun)
                            ->GROUPBY('aduan.idjab')
                            ->GROUPBY('jabatan.jabatan')
                            ->ORDERBY('totalJab', 'DESC')
                            ->get();
            }

        $fileName = 'Statistik Helpdesk ICT '.$jenis.' Tahun '.$tahun.'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
                        'BIL', 'JABATAN', 'JAN', 'FEB', 'MAC',
                        'APR', 'MEI', 'JUN', 'JUL', 'OGOS',
                        'SEP', 'OKT', 'NOV', 'DIS', 'JUMLAH'
                    );

        $callback = function() use($columns,$totalJabatan,$tahun) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

            $bil =0;

            $totalJan =0;  $totalJul =0;
            $totalFeb =0;  $totalOgo =0;
            $totalMac =0;  $totalSep =0;
            $totalApr =0;  $totalOkt =0;
            $totalMei =0;  $totalNov =0;
            $totalJun =0;  $totalDis =0;
            $totalJumlah = 0;

            foreach($totalJabatan as $total){

                $jan = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $feb = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mac = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $apr = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $mei = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $jun = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $julai = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $ogos = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $sep = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $okt = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $nov = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

            $dis = DB::table('jabatan')
                    ->join('aduan', 'aduan.idjab', '=',
                                'jabatan.idjab')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.idjab', '=', $total->idjab)
                    ->count();

                $bil = $bil+1;

                $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
                $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
                $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
                $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
                $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
                $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

                $totalJumlah = $totalJumlah + $total->totalJab;


                fputcsv($file, array(
                                    $bil, $total->jabatan, $jan, $feb, $mac,
                                    $apr, $mei, $jun, $julai, $ogos,
                                    $sep, $okt, $nov, $dis, $total->totalJab
                                ));
            }

            fputcsv($file, array(
                                '', 'Jumlah', $totalJan, $totalFeb, $totalMac,
                                $totalApr, $totalMei, $totalJun, $totalJul, $totalOgo,
                                $totalSep, $totalOkt, $totalNov, $totalDis, $totalJumlah

            ));

            fclose($file);

        };

            return response()->stream($callback, 200, $headers);
    }

    public function jabatanDetail($idjab, $tahun, $bln)
    {
        if(($idjab == 0) && ($bln == 0))
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

            $bulan = '';
            $tajuk = 'Semua Jabatan';

        }
        elseif($idjab == 0)
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tajuk = 'Semua Jabatan';

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}

        }
        elseif($bln == 0)
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('aduan.idjab','=',$idjab)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $jabatan = DB::table('jabatan')
                            ->select('jabatan')
                            ->where('idjab', '=', $idjab)
                            ->first();

                $tajuk = $jabatan->jabatan;
                $bulan = '';
        }
        else
        {
            $aduan = DB::table('aduan')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('aduan.idjab','=',$idjab)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $jabatan = DB::table('jabatan')
                            ->select('jabatan')
                            ->where('idjab', '=', $idjab)
                            ->first();

                $tajuk = $jabatan->jabatan;

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}
        }


        return view('admin.jabatanDetail', compact('aduan', 'tajuk', 'tahun', 'bulan'));
    }

    public function statistikTechnician()
    {
        $start = 2017;
        $end = 2021;
        $i = 1;
        $m_end = $end - $i;

        $technician = DB::table('pengguna')
                    ->select('idpengguna','nama')
                    ->whereIn('idlevel', ['2','7'])
                    ->get();

        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2016';

        return view('admin.statistikTechnician', compact('tahun_now', 'tahun_awal', 'technician'));
    }

    public function technician(Request $request)
    {
        if($request->ajax()){

            $output="";
            $tahun = $request->tahun;
            $idpengguna = $request->idpengguna;

            if($idpengguna != '')
            {

                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->where('aduan.id_pengguna', '=', $idpengguna)
                            //->where('pengguna.idlevel', '=', '2')
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();

                $test = DB::table('pengguna')
                        ->select('nama')
                        ->where('idpengguna', '=', $idpengguna)
                        ->whereIn('pengguna.idlevel', ['2','7'])
                        ->first();

                $jenis = $test->nama;
                $pengguna = $idpengguna;

            }else{
                $jenis = 'Semua Juruteknik';
                $pengguna = '0';
                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->whereIn('pengguna.idlevel', ['2','7'])
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();
            }

        $output.=
                '
                <h5>Statistik Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Nama</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';


                $bln1 = '01';   $bln7 = '07';
                $bln2 = '02';   $bln8 = '08';
                $bln3 = '03';   $bln9 = '09';
                $bln4 = '04';   $bln10 = '10';
                $bln5 = '05';   $bln11 = '11';
                $bln6 = '06';   $bln12 = '12';

        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalTechnician as $total) {

            $jan = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $feb = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mac = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $apr = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mei = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $jun = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $julai = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $ogos = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $sep = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $okt = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $nov = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $dis = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalTech;

        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->nama.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$jan.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$feb.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$mac.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$apr.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$mei.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$jun.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$julai.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$ogos.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$sep.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$okt.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$nov.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$dis.'</td>
            <td class="text-center"><a href="techDetail/'.$total->id_pengguna.'/'.$tahun.'/'.$bln.'" target="_blank">'.$total->totalTech.'</td>
            </tr>
            ';
        }

        $kat0 = 0;
        $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$totalJan.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$totalFeb.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$totalMac.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$totalApr.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$totalMei.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$totalJun.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$totalJul.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$totalOgo.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$totalSep.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$totalOkt.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$totalNov.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$totalDis.'</td>'.
            '<td><a href="techDetail/'.$kat0.'/'.$tahun.'/'.$bln.'" target="_blank">'.$totalJumlah.'</td>'.
            '</tr>
            ';

        $output .=
                '
                </tbody>
                </table>
                <a href="statistikTech/pdf/'.$tahun.'/'.$pengguna.'" target="_blank" class="btn btn-danger">Convert into PDF</a>
                <a href="statistikTech/csv/'.$tahun.'/'.$pengguna.'" target="_blank" class="btn btn-success">Convert into CSV</a>
                ';

        return Response($output);

        };
    }

    public function pdfTech($tahun,$pengguna)
    {
        $output = '';
        $idpengguna = $pengguna;
        if($idpengguna != '0')
            {

                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->where('aduan.id_pengguna', '=', $idpengguna)
                            //->where('pengguna.idlevel', '=', '2')
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();

                $test = DB::table('pengguna')
                        ->select('nama')
                        ->where('idpengguna', '=', $idpengguna)
                        ->whereIn('pengguna.idlevel', ['2','7'])
                        ->first();

                $jenis = $test->nama;
                $pengguna = $idpengguna;

            }else{
                $jenis = 'Semua Juruteknik';
                $pengguna = '0';
                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->whereIn('pengguna.idlevel', ['2','7'])
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();
            }

                $output.=
                '
                <style>
                .h3,h3{font-size:1.75rem}
                .table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    color: #212529;
                    border-collapse: collapse;
                    background-color: transparent;
                  }

                  .table th,
                  .table td {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                  }

                  .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #dee2e6;
                  }

                  .table tbody + tbody {
                    border-top: 1px solid #dee2e6;
                  }
                .table-bordered {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered th,
                  .table-bordered td {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered thead th,
                  .table-bordered thead td {
                    border-bottom-width: 2px;
                  }
                  .text-center {
                    text-align: center !important;
                  }
                  .table.text-center,
                  .table.text-center td,
                  .table.text-center th {
                   text-align: center;
                  }
                </style>
                <center><h3>Statistik Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h3></center>
                            <table  class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Nama</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';


        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalTechnician as $total) {

            $jan = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $feb = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mac = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $apr = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mei = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $jun = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $julai = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $ogos = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $sep = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $okt = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $nov = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $dis = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalTech;

        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->nama.'</td>
            <td class="text-center">'.$jan.'</td>
            <td class="text-center">'.$feb.'</td>
            <td class="text-center">'.$mac.'</td>
            <td class="text-center">'.$apr.'</td>
            <td class="text-center">'.$mei.'</td>
            <td class="text-center">'.$jun.'</td>
            <td class="text-center">'.$julai.'</td>
            <td class="text-center">'.$ogos.'</td>
            <td class="text-center">'.$sep.'</td>
            <td class="text-center">'.$okt.'</td>
            <td class="text-center">'.$nov.'</td>
            <td class="text-center">'.$dis.'</td>
            <td class="text-center">'.$total->totalTech.'</td>
            </tr>
            ';
        }

        $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td>'.$totalJan.'</td>'.
            '<td>'.$totalFeb.'</td>'.
            '<td>'.$totalMac.'</td>'.
            '<td>'.$totalApr.'</td>'.
            '<td>'.$totalMei.'</td>'.
            '<td>'.$totalJun.'</td>'.
            '<td>'.$totalJul.'</td>'.
            '<td>'.$totalOgo.'</td>'.
            '<td>'.$totalSep.'</td>'.
            '<td>'.$totalOkt.'</td>'.
            '<td>'.$totalNov.'</td>'.
            '<td>'.$totalDis.'</td>'.
            '<td>'.$totalJumlah.'</td>'.
            '</tr>
            </tbody>
            </table>
            ';

            $title = 'Statistik Helpdesk ICT Juruteknik Tahun';
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($output)->setPaper('a4', 'landscape');
            return $pdf->stream($title.' '.$tahun.'.pdf');

    }

    public function csvTech($tahun,$pengguna)
    {
        $idpengguna = $pengguna;
        if($idpengguna != '0')
            {

                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->where('aduan.id_pengguna', '=', $idpengguna)
                            //->where('pengguna.idlevel', '=', '2')
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();

                $test = DB::table('pengguna')
                        ->select('nama')
                        ->where('idpengguna', '=', $idpengguna)
                        ->whereIn('pengguna.idlevel', ['2','7'])
                        ->first();

                $jenis = $test->nama;
                $pengguna = $idpengguna;

            }else{
                $jenis = 'Semua Juruteknik';
                $pengguna = '0';
                $totalTechnician = DB::table('pengguna')
                            ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                            ->select([ 'pengguna.nama', 'aduan.id_pengguna',
                            DB::raw('COUNT(aduan.id) as totalTech')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', $tahun)
                            ->whereIn('pengguna.idlevel', ['2','7'])
                            ->GROUPBY('aduan.id_pengguna')
                            ->GROUPBY('pengguna.nama')
                            ->ORDERBY('totalTech', 'DESC')
                            ->get();
            }

        $fileName = 'Statistik Helpdesk ICT '.$jenis.' Tahun '.$tahun.'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
                        'BIL', 'JURUTEKNIK', 'JAN', 'FEB', 'MAC',
                        'APR', 'MEI', 'JUN', 'JUL', 'OGOS',
                        'SEP', 'OKT', 'NOV', 'DIS', 'JUMLAH'
                    );

        $callback = function() use($columns,$totalTechnician,$tahun) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);



            $bil =0;
            $totalJan =0;  $totalJul =0;
            $totalFeb =0;  $totalOgo =0;
            $totalMac =0;  $totalSep =0;
            $totalApr =0;  $totalOkt =0;
            $totalMei =0;  $totalNov =0;
            $totalJun =0;  $totalDis =0;
            $totalJumlah = 0;

            foreach($totalTechnician as $total){

                $jan = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

                    $feb = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mac = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $apr = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $mei = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $jun = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $julai = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $ogos = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $sep = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $okt = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                   // ->where('pengguna.idlevel', '=', '2')
                    ->count();

            $nov = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

            $dis = DB::table('pengguna')
                    ->join('aduan', 'aduan.id_pengguna', '=',
                                'pengguna.idpengguna')
                    ->select('aduan.id')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('aduan.id_pengguna', '=', $total->id_pengguna)
                    //->where('pengguna.idlevel', '=', '2')
                    ->count();

                $bil = $bil+1;

                $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
                $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
                $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
                $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
                $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
                $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalTech;

                fputcsv($file, array(
                                    $bil, $total->nama, $jan, $feb, $mac,
                                    $apr, $mei, $jun, $julai, $ogos,
                                    $sep, $okt, $nov, $dis, $total->totalTech
                                ));
            }

            fputcsv($file, array(
                                '', 'Jumlah',  $totalJan, $totalFeb, $totalMac,
                                $totalApr, $totalMei, $totalJun, $totalJul, $totalOgo,
                                $totalSep, $totalOkt, $totalNov, $totalDis, $totalJumlah
            ));

            fclose($file);

        };

            return response()->stream($callback, 200, $headers);
    }

    public function techDetail($id_pengguna, $tahun, $bln)
    {
        if(($id_pengguna == 0) && ($bln == 0))
        {
            $aduan = DB::table('aduan')
                ->join('pengguna', 'aduan.id_pengguna', '=',
                    'pengguna.idpengguna')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

            $tajuk = 'Semua Juruteknik';
            $bulan = '';

        }
        elseif($id_pengguna == 0)
        {
            $aduan = DB::table('aduan')
                ->join('pengguna', 'aduan.id_pengguna', '=',
                    'pengguna.idpengguna')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tajuk = 'Semua Juruteknik';

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}


        }
        elseif($bln == 0)
        {
            $aduan = DB::table('aduan')
                ->join('pengguna', 'aduan.id_pengguna', '=',
                    'pengguna.idpengguna')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('aduan.id_pengguna','=',$id_pengguna)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tech = DB::table('pengguna')
                            ->select('nama')
                            ->where('idpengguna', '=', $id_pengguna)
                            ->first();

                $tajuk = $tech->nama;
                $bulan = '';
        }
        else
        {
            $aduan = DB::table('aduan')
                ->join('pengguna', 'aduan.id_pengguna', '=',
                    'pengguna.idpengguna')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('aduan.id_pengguna','=',$id_pengguna)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tech = DB::table('pengguna')
                            ->select('nama')
                            ->where('idpengguna', '=', $id_pengguna)
                            ->first();

                $tajuk = $tech->nama;

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}
        }


        return view('admin.techDetail', compact('aduan', 'tajuk', 'tahun', 'bulan'));
    }

    public function statistikMaklumbalas()
    {
        $start = 2017;
        $end = 2021;
        $i = 1;
        $m_end = $end - $i;
        $dt = Carbon::now();
        $tahun_now = $dt->year;
        $tahun_awal = '2020';

        $query = DB::table('feedback')
                            ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                'feedback_respon.idrespon')
                            ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                'aduan.no_aduan')
                            ->select([ 'feedback_respon.respon_name',
                            DB::raw('COUNT(*) as totalFeed')
                            ])
                            ->whereyear('aduan.tarikh_aduan','=', '2020')
                            ->GROUPBY('feedback_respon.respon_name')
                            ->GROUPBY('feedback_respon.idrespon')
                            ->ORDERBY('feedback_respon.idrespon', 'ASC')
                            ->get();

        $tech = DB::table('pengguna')
                ->select('idpengguna', 'nama')
                ->whereIn('idlevel', ['2','7'])
                ->get();


        return view('admin.statistikMaklumbalas', compact('tahun_now', 'tahun_awal', 'query', 'tech'));
    }

    public function maklumbalas(Request $request)
    {
        if($request->ajax()){

            $output="";
            $tahun = $request->tahun;
            $id_pengguna = $request->id_pengguna;



            if($id_pengguna != '')
            {
                $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->where('aduan.id_pengguna', '=', $id_pengguna)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();

                $test = DB::table('pengguna')
                                ->select('nama')
                                ->where('idpengguna', '=', $id_pengguna)
                                ->whereIn('pengguna.idlevel', ['2','7'])
                                ->first();

                $jenis = $test->nama;
                $pengguna = $id_pengguna;
            }else{
                $jenis = '';
                $pengguna = '0';
                $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();
            }




        $output.=
                '
                <h5>Statistik Maklumbalas Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h5>
                            <table id="list" class="table">
                                <thead class="bg-success">
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Maklumbalas</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';
                $bln1 = '01';   $bln7 = '07';
                $bln2 = '02';   $bln8 = '08';
                $bln3 = '03';   $bln9 = '09';
                $bln4 = '04';   $bln10 = '10';
                $bln5 = '05';   $bln11 = '11';
                $bln6 = '06';   $bln12 = '12';

        $bil =0;
        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalMaklumbalas as $total) {

            $jan = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $feb = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mac = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $apr = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mei = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $jun = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $julai = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $ogos = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $sep = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $okt = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $nov = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $dis = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalFeed;
        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->respon_name.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$jan.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$feb.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$mac.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$apr.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$mei.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$jun.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$julai.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$ogos.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$sep.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$okt.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$nov.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$dis.'</td>
            <td class="text-center"><a href="maklumbalasDetail/'.$total->respon_feedback.'/'.$tahun.'/'.$bln.'" target="_blank">'.$total->totalFeed.'</td>
            </tr>
            ';

        }

        $kat0 = 0;
        $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln1.'" target="_blank">'.$totalJan.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln2.'" target="_blank">'.$totalFeb.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln3.'" target="_blank">'.$totalMac.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln4.'" target="_blank">'.$totalApr.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln5.'" target="_blank">'.$totalMei.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln6.'" target="_blank">'.$totalJun.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln7.'" target="_blank">'.$totalJul.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln8.'" target="_blank">'.$totalOgo.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln9.'" target="_blank">'.$totalSep.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln10.'" target="_blank">'.$totalOkt.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln11.'" target="_blank">'.$totalNov.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln12.'" target="_blank">'.$totalDis.'</td>'.
            '<td><a href="maklumbalasDetail/'.$kat0.'/'.$tahun.'/'.$bln.'" target="_blank">'.$totalJumlah.'</td>'.
            '</tr>
            ';

        $output.=
            '
            </tbody>
            </table>
            <a href="statistikMaklumbalas/pdf/'.$tahun.'/'.$pengguna.'" target="_blank" class="btn btn-danger">Convert into PDF</a>
            <a href="statistikMaklumbalas/csv/'.$tahun.'/'.$pengguna.'" target="_blank" class="btn btn-success">Convert into CSV</a>
                ';


        return Response($output);

        };

    }

    public function pdfFeed($tahun, $pengguna)
    {
        $output = '';
        $id_pengguna = $pengguna;
        if($id_pengguna != '0')
        {
            $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->where('aduan.id_pengguna', '=', $id_pengguna)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();

                        $test = DB::table('pengguna')
                                ->select('nama')
                                ->where('idpengguna', '=', $id_pengguna)
                                ->whereIn('pengguna.idlevel', ['2','7'])
                                ->first();

                $jenis = $test->nama;
                $pengguna = $id_pengguna;
        }else{
            $jenis = '';

            $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();
        }



        $output.=
                '
                <style>
                .h3,h3{font-size:1.75rem}
                .table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    color: #212529;
                    border-collapse: collapse;
                    background-color: transparent;
                  }

                  .table th,
                  .table td {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                  }

                  .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #dee2e6;
                  }

                  .table tbody + tbody {
                    border-top: 1px solid #dee2e6;
                  }
                .table-bordered {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered th,
                  .table-bordered td {
                    border: 1px solid #dee2e6;
                  }

                  .table-bordered thead th,
                  .table-bordered thead td {
                    border-bottom-width: 2px;
                  }
                  .text-center {
                    text-align: center !important;
                  }
                  .table.text-center,
                  .table.text-center td,
                  .table.text-center th {
                   text-align: center;
                  }
                </style>
                <center><h3>Statistik Maklumbalas Helpdesk ICT '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'</h3></center>
                <br>
                <table  class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Bil</th>
                                        <th>Maklumbalas</th>
                                        <th>JAN</th>
                                        <th>FEB</th>
                                        <th>MAC</th>
                                        <th>APR</th>
                                        <th>MEI</th>
                                        <th>JUN</th>
                                        <th>JUL</th>
                                        <th>OGOS</th>
                                        <th>SEP</th>
                                        <th>OKT</th>
                                        <th>NOV</th>
                                        <th>DIS</th>
                                        <th>JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';

        $bil =0;
        $bil =0;
        $totalJan =0;  $totalJul =0;
        $totalFeb =0;  $totalOgo =0;
        $totalMac =0;  $totalSep =0;
        $totalApr =0;  $totalOkt =0;
        $totalMei =0;  $totalNov =0;
        $totalJun =0;  $totalDis =0;
        $totalJumlah = 0;
        $bln =0;

        foreach ($totalMaklumbalas as $total) {

            $jan = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $feb = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mac = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $apr = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mei = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $jun = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $julai = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $ogos = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $sep = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $okt = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $nov = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $dis = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalFeed;
        $output.=
            '
            <tr >'.
            '<td class="text-center">'.$bil.'</td>'.
            '<td>'.$total->respon_name.'</td>
            <td class="text-center">'.$jan.'</td>
            <td class="text-center">'.$feb.'</td>
            <td class="text-center">'.$mac.'</td>
            <td class="text-center">'.$apr.'</td>
            <td class="text-center">'.$mei.'</td>
            <td class="text-center">'.$jun.'</td>
            <td class="text-center">'.$julai.'</td>
            <td class="text-center">'.$ogos.'</td>
            <td class="text-center">'.$sep.'</td>
            <td class="text-center">'.$okt.'</td>
            <td class="text-center">'.$nov.'</td>
            <td class="text-center">'.$dis.'</td>
            <td class="text-center">'.$total->totalFeed.'</td>
            </tr>
            ';

        }

        $output.=
            '
            <tr class="text-center">'.
            '<td></td>'.
            '<td>Jumlah</td>'.
            '<td>'.$totalJan.'</td>'.
            '<td>'.$totalFeb.'</td>'.
            '<td>'.$totalMac.'</td>'.
            '<td>'.$totalApr.'</td>'.
            '<td>'.$totalMei.'</td>'.
            '<td>'.$totalJun.'</td>'.
            '<td>'.$totalJul.'</td>'.
            '<td>'.$totalOgo.'</td>'.
            '<td>'.$totalSep.'</td>'.
            '<td>'.$totalOkt.'</td>'.
            '<td>'.$totalNov.'</td>'.
            '<td>'.$totalDis.'</td>'.
            '<td>'.$totalJumlah.'</td>'.
            '</tr>
            </tbody>
            </table>
            ';

        $title = 'Statistik Helpdesk ICT Maklum Balas Tahun';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($output)->setPaper('a4', 'landscape');
        return $pdf->stream($title.' '.$tahun.'.pdf');
    }

    public function csvFeed($tahun,$pengguna)
    {
        $id_pengguna = $pengguna;
        if($id_pengguna != '0')
        {
            $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->where('aduan.id_pengguna', '=', $id_pengguna)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();


                                $test = DB::table('pengguna')
                                ->select('nama')
                                ->where('idpengguna', '=', $id_pengguna)
                                ->whereIn('pengguna.idlevel', ['2','7'])
                                ->first();

                $jenis = $test->nama;
        }else{
            $jenis = '';
            $totalMaklumbalas = DB::table('feedback')
                                ->join('feedback_respon', 'feedback.respon_feedback', '=',
                                    'feedback_respon.idrespon')
                                ->join('aduan', 'feedback.no_aduan_feedback', '=',
                                    'aduan.no_aduan')
                                ->select([ 'feedback_respon.respon_name', 'feedback.respon_feedback',
                                DB::raw('COUNT(*) as totalFeed')
                                ])
                                ->whereyear('aduan.tarikh_aduan','=', $tahun)
                                ->GROUPBY('feedback_respon.respon_name')
                                ->GROUPBY('feedback.respon_feedback')
                                ->ORDERBY('feedback_respon.idrespon', 'ASC')
                                ->get();
        }


        $fileName = 'Statistik Helpdesk ICT Maklum Balas '.ucwords(strtolower($jenis)).' Tahun '.$tahun.'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
                        'BIL', 'JURUTEKNIK', 'JAN', 'FEB', 'MAC',
                        'APR', 'MEI', 'JUN', 'JUL', 'OGOS',
                        'SEP', 'OKT', 'NOV', 'DIS', 'JUMLAH'
                    );

        $callback = function() use($columns,$totalMaklumbalas,$tahun) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);



            $bil =0;
            $totalJan =0;  $totalJul =0;
            $totalFeb =0;  $totalOgo =0;
            $totalMac =0;  $totalSep =0;
            $totalApr =0;  $totalOkt =0;
            $totalMei =0;  $totalNov =0;
            $totalJun =0;  $totalDis =0;
            $totalJumlah = 0;

            foreach($totalMaklumbalas as $total){

                $jan = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '01')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $feb = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '02')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mac = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '03')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $apr = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '04')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $mei = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '05')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $jun = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '06')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $julai = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '07')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $ogos = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '08')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $sep = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '09')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $okt = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '10')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $nov = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '11')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

            $dis = DB::table('aduan')
                    ->join('feedback', 'aduan.no_aduan', '=',
                                'feedback.no_aduan_feedback')
                    ->whereYear('aduan.tarikh_aduan','=', $tahun)
                    ->whereMonth('aduan.tarikh_aduan', '=', '12')
                    ->where('feedback.respon_feedback', '=', $total->respon_feedback)
                    ->count();

        $bil = $bil+1;

        $totalJan = $totalJan + $jan;   $totalJul = $totalJul + $julai;
        $totalFeb = $totalFeb + $feb;   $totalOgo = $totalOgo + $ogos;
        $totalMac = $totalMac + $mac;   $totalSep = $totalSep + $sep;
        $totalApr = $totalApr + $apr;   $totalOkt = $totalOkt + $okt;
        $totalMei = $totalMei + $mei;   $totalNov = $totalNov + $nov;
        $totalJun = $totalJun + $jun;   $totalDis = $totalDis + $dis;

        $totalJumlah = $totalJumlah + $total->totalFeed;


                fputcsv($file, array(
                                    $bil, $total->respon_name, $jan, $feb, $mac,
                                    $apr, $mei, $jun, $julai, $ogos,
                                    $sep, $okt, $nov, $dis, $total->totalFeed
                                ));
            }

            fputcsv($file, array(
                                '', 'Jumlah',  $totalJan, $totalFeb, $totalMac,
                                $totalApr, $totalMei, $totalJun, $totalJul, $totalOgo,
                                $totalSep, $totalOkt, $totalNov, $totalDis, $totalJumlah
            ));

            fclose($file);

        };

            return response()->stream($callback, 200, $headers);
    }

    public function maklumbalasDetail($respon_feedback, $tahun, $bln)
    {
        if(($respon_feedback == 0) && ($bln == 0))
        {
            $aduan = DB::table('aduan')
                ->join('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tajuk = 'Semua Maklum Balas';
                $bulan = '';
        }
        elseif($respon_feedback == 0)
        {
            $aduan = DB::table('aduan')
                ->join('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $tajuk = 'Semua Maklum Balas';
                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}
        }
        elseif($bln == 0)
        {
            $aduan = DB::table('aduan')
                ->join('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('feedback.respon_feedback','=',$respon_feedback)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $feed = DB::table('feedback_respon')
                            ->select('respon_name')
                            ->where('idrespon', '=', $respon_feedback)
                            ->first();

                $tajuk = $feed->respon_name;
                $bulan = '';
        }
        else
        {
            $aduan = DB::table('aduan')
                ->join('feedback', 'aduan.no_aduan', '=',
                    'feedback.no_aduan_feedback')
                ->join('jabatan', 'aduan.idjab', '=',
                    'jabatan.idjab')
                ->select(
                    'aduan.id',
                    'aduan.no_aduan',
                    'aduan.masalah',
                    'jabatan.jabatan',
                    'aduan.tarikh_aduan',
                    'aduan.tarikh_tindakan'
                )
                ->where('feedback.respon_feedback','=',$respon_feedback)
                ->whereYear('aduan.tarikh_aduan', '=', $tahun)
                ->whereMonth('aduan.tarikh_aduan', '=', $bln)
                ->orderBy('aduan.no_aduan', 'DESC')
                ->paginate(5);

                $feed = DB::table('feedback_respon')
                            ->select('respon_name')
                            ->where('idrespon', '=', $respon_feedback)
                            ->first();

                $tajuk = $feed->respon_name;

                if($bln == '01'){ $bulan = 'Januari';}
                elseif($bln == '02'){ $bulan = 'Februari';}
                elseif($bln == '03'){ $bulan = 'Mac';}
                elseif($bln == '04'){ $bulan = 'April';}
                elseif($bln == '05'){ $bulan = 'Mei';}
                elseif($bln == '06'){ $bulan = 'Jun';}
                elseif($bln == '07'){ $bulan = 'Julai';}
                elseif($bln == '08'){ $bulan = 'Ogos';}
                elseif($bln == '09'){ $bulan = 'September';}
                elseif($bln == '10'){ $bulan = 'Oktober';}
                elseif($bln == '11'){ $bulan = 'November';}
                elseif($bln == '12'){ $bulan = 'Disember';}
        }


        return view('admin.maklumbalasDetail', compact('aduan', 'tajuk', 'tahun', 'bulan'));
    }

    public function addKategori()
    {
        $kategori = DB::table('kategori')
                    ->select('idkategori','kategori')
                    ->paginate(5);

        return view('admin.addKategori',compact('kategori'));
    }

    public function storeKategori(Request $request)
    {
        $check = DB::table('kategori')
                ->select('kategori')
                ->where('kategori', '=', $request->kategori)
                ->first();

        if($check === null)
        {
            $data = array();
            $data['kategori'] = $request->kategori;
            $submit = DB::table('kategori')->insert($data);

            if($submit)
            {
                return redirect()->route('admin.addKategori')->with('success', 'Berjaya Tambah Kategori');
            }
            else{
                return redirect()->route('admin.addKategori')->with('error', 'Gagal Tambah Kategori');
            }
        }else
        {
            return redirect()->route('admin.addKategori')->with('error', 'Kategori Telah Wujud');
        }

    }

    public function addSubkat()
    {
        $kategori = DB::table('kategori')
                    ->select('idkategori','kategori')
                    ->get();

        $subkat = DB::table('subkategori')
                    ->join('kategori', 'subkategori.idkategori', '=',
                        'kategori.idkategori')
                    ->select('subkategori.idsubkat', 'subkategori.subkat', 'kategori.kategori')
                    ->paginate();

        return view('admin.addSubkat',compact('kategori','subkat'));
    }

    function listKat(Request $request)
    {
        if(request()->ajax())
        {
         if(!empty($request->filter_kategori))
         {
          $data = DB::table('subkategori')
            ->join('kategori', 'subkategori.idkategori', '=',
                'kategori.idkategori')
            ->select('subkategori.subkat', 'kategori.kategori')
            ->where('kategori.idkategori', $request->filter_kategori)
            ->get();
         }
         else
         {
          $data = DB::table('subkategori')
          ->join('kategori', 'subkategori.idkategori', '=',
                'kategori.idkategori')
                ->select('subkategori.subkat', 'kategori.kategori')

            ->get();
         }
         return DataTables::of($data)->make(true);
        }
    }

    public function storeSubkat(Request $request)
    {
        $check = DB::table('subkategori')
                ->select('subkat')
                ->where('subkat', '=', $request->subkategori)
                ->first();

        if($check === null)
        {
            $data = array();
            $data['idkategori'] = $request->kategori;
            $data['subkat'] = $request->subkategori;

            $subkategori = DB::table('subkategori')->insert($data);

            if($subkategori)
            {
                return redirect()->route('admin.addSubkat')->with('success', 'Berjaya Tambah SubKategori');
            }
            else{
                return redirect()->route('admin.addSubkat')->with('error', 'Gagal Tambah SubKategori');
            }
        }else
        {
            return redirect()->route('admin.addSubkat')->with('error', 'Subkategori Telah Wujud');
        }
    }

    public function addModel()
    {
        $model = DB::table('model')
                    ->select('idmodel','model_name')
                    ->paginate(5);

        return view('admin.addModel', compact('model'));
    }

    public function storeModel(Request $request)
    {

        $check = DB::table('model')
                ->select('model_name')
                ->where('model_name', '=', $request->model)
                ->first();

        if($check === null)
        {
            $data = array();
            $data['model_name'] = $request->model;

            $model = DB::table('model')->insert($data);

            if($model)
            {
                return redirect()->route('admin.addModel')->with('success', 'Berjaya Tambah Model');
            }
            else{
                return redirect()->route('admin.addModel')->with('error', 'Gagal Tambah Model');
            }
        }else
        {
            return redirect()->route('admin.addModel')->with('error', 'Model Telah Wujud');
        }

    }

}
