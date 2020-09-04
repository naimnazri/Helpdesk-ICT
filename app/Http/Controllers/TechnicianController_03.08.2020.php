<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use App\Aduan;
use Carbon\Carbon;
use Auth;

class TechnicianController extends Controller
{
    public function resetPass()
    {
        return view('technician.reset_pass');
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
            return redirect()->route('technician.dashboard')->with('Success', 'Password Berjaya direset');
        }
        else{
            return redirect()->route('technician.reset')->with('Danger', 'Password Gagal direset');
        }
    }

    public function technicianHome()
    {
        $baru =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '4')
                ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
                ->where('jabatan.f_pass', '=', '1')
                ->count();


        $proses =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '9')
                ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
                ->where('jabatan.f_pass', '=', '1')
                ->count();

        $pembekal = DB::table('aduan')
                    ->where('id_pengguna', '=', Auth::user()->idpengguna)
                    ->where('idstatus', '=', '9')
                    ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
                    ->where('jabatan.f_pass', '=', '1')
                    ->count();

        $selesai =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '3')
                ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
                ->where('jabatan.f_pass', '=', '1')
                ->count();

        $tolak =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '10')
                ->join('jabatan', 'aduan.idjab', '=', 'jabatan.idjab')
                ->where('jabatan.f_pass', '=', '1')
                ->count();





                /* tambah dua nilai count

                ->whereIn('idstatus',  ['4','3'])

                $test2 =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '4');
                $test1 =DB::table('aduan')
                ->where('id_pengguna', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '3');

                $test = $test2->union($test1)->count(); */

        return view('technician.dashboard' ,compact('baru','proses', 'pembekal', 'selesai','tolak'));
    }

    public function aduanBaru()
    {
        $aduan = DB::table('aduan')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->select(
                        'aduan.id',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan'
                    )
                    ->where('aduan.idstatus', '=', '4')
                    ->where('id_pengguna', '=', Auth::user()->idpengguna)
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $status = 'Baru';

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('technician.aduanbaru', compact('aduan','status', 'conn'));
    }

    public function aduanProses()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.id_pengadu')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->whereIn('aduan.idstatus', ['2','9'])
                    ->where('id_pengguna', '=', Auth::user()->idpengguna)
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('technician.aduanproses', compact('aduan', 'conn'));
    }

    public function aduanSelesai()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.id_pengadu')
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
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('id_pengguna', '=', Auth::user()->idpengguna)
                    ->where('aduan.idstatus', '=', '3')
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('technician.aduanselesai', compact('aduan', 'conn'));
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

        return view('technician.cariSelesai', compact('tahun_now', 'tahun_awal', 'technician'));
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


        return view('technician.maklumatSelesai', compact('aduan', 'pegawai', 'kronologi'));
    }

    public function aduanTolak()
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna', 'aduan.id_pengadu',
                        '=', 'pengguna.id_pengadu')
                    ->join('jabatan', 'aduan.idjab', '=',
                        'jabatan.idjab')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'pengguna.nama',
                        'aduan.id_pengadu',
                        'jabatan.jabatan',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.tarikh_tindakan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('aduan.idstatus', '=', '10')
                    ->where('id_pengguna', '=', Auth::user()->idpengguna)
                    ->where('jabatan.f_pass', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

                    $conn = mysqli_connect(env('DB_HOST'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_DATABASE'));

        return view('technician.aduantolak', compact('aduan', 'conn'));
    }

    public function createAduan()
    {
        return view('technician.add_aduan');
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


                        $mail->isSMTP();
                        $mail->Host       = env('MAIL_HOST');
                        $mail->SMTPAuth   = env('MAIL_AUTH');
                        $mail->Username   = env('MAIL_USERNAME');
                        $mail->Password   = env('MAIL_PASSWORD');
                        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                        $mail->Port       = env('MAIL_PORT');

                        //Recipients
                        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                         $mail->addAddress($to_email, $to_name);      // Add a recipient

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
                return redirect()->route('technician.aduanbaru')->with('success', 'Aduan Berjaya Ditambah');
            }else{
                return redirect()->route('technician.add_aduan')->with('error', 'Aduan Gagal Ditambah');
            }
    }

    public function listJabatan()
    {
        $jabatan = DB::table('jabatan')
                        ->select(
                            'idjab',
                            'jabatan'
                            )
                        ->get();

        return view('technician.list_pengguna', compact('jabatan'));
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
            return $return = '<a class="btn btn-primary" align="center" href="t_editpengguna/'.base64_encode($data->idpengguna).'" >Kemaskini</a>';
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

        return view('technician.edit_pengguna', compact('pengguna', 'jabatan', 'bahagian'));
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
                'no_ofis' => 'digits_between:9,13|numeric|required'
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
                'no_ofis' => 'digits_between:9,13|numeric|required'
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


        if(($detail) | ($detail == ''))
        {
            return redirect()->route('technician.list_pengguna')->with('success', 'Maklumat Pengguna Berjaya Dikemaskini');
        }else{
            return redirect()->url('t_editpengguna/'.$idpengguna)->with('error', 'Profil Pengguna Gagal Dikemaskini');
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

                    return view('technician.profil' ,compact('profil'));
    }

    public function editAduan($no_aduan)
    {
        $aduan = DB::table('aduan')
                    ->leftjoin('pengguna','aduan.id_pengadu',
                    '=', 'pengguna.id_pengadu')
                    ->join('jabatan', 'aduan.idjab',
                    '=', 'jabatan.idjab')
                    ->select(
                        'aduan.no_aduan',
                        'aduan.idstatus',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.maklumbalas',
                        'aduan.id_pengadu',
                        'aduan.tarikh_aduan',
                        'aduan.masa_aduan',
                        'aduan.image',
                        'pengguna.id_pengadu',
                        'pengguna.nama',
                        'pengguna.idjab',
                        'pengguna.notel',
                        'pengguna.jawatan',
                        'jabatan.jabatan'
                    )
                    ->where('no_aduan', '=', $no_aduan)->first();

        $kategori = DB::table('kategori')
                    ->select(
                        'idkategori',
                        'kategori'
                    )
                    ->get();

        $model = DB::table('model')
                ->select('idmodel', 'model_name')
                ->get();

        return view('technician.edit_aduan',compact('aduan', 'kategori', 'model'));
    }

    function subKat(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = DB::table('subkategori')
                ->select('idsubkat', 'subkat')
                ->where('idkategori', $value)
                /* ->groupBy($dependent) */
                ->get();
        $output = '<option value="">Sila Pilih Jenis</option>';
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->idsubkat.'">'.$row->subkat.'</option>';
        }
        echo $output;
    }

    public function editPembekal($no_aduan)
    {
        $aduan = DB::table('aduan')
                    ->join('pengguna','aduan.id_pengadu',
                    '=', 'pengguna.id_pengadu')
                    ->join('jabatan', 'aduan.idjab',
                    '=', 'jabatan.idjab')
                    ->join('kategori', 'aduan.idkategori',
                    '=', 'kategori.idkategori')
                    ->join('subkategori', 'aduan.jenis_kategori', '=',
                    'subkategori.idsubkat')
                    ->join('model', 'aduan.model', '=',
                    'model.idmodel')
                    ->select(
                        'aduan.no_aduan',
                        'aduan.idstatus',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.idkategori',
                        'aduan.maklumbalas',
                        'aduan.id_pengadu',
                        'aduan.tarikh_aduan',
                        'aduan.masa_aduan',
                        'aduan.noinventori',
                        'aduan.image',
                        'aduan.model',
                        'model.model_name',
                        'aduan.jenis_kategori',
                        'subkategori.subkat',
                        'aduan.nosiri',
                        'aduan.tarikh_tindakan',
                        'aduan.masa_tindakan',
                        'aduan.id_onsite',
                        'aduan.id_ganti',
                        'pengguna.id_pengadu',
                        'pengguna.nama',
                        'pengguna.idjab',
                        'pengguna.notel',
                        'pengguna.jawatan',
                        'jabatan.jabatan',
                        'kategori.kategori'
                    )
                    ->where('no_aduan', '=', $no_aduan)->first();



        return view('technician.editpembekal',compact('aduan'));
    }

    public function storePembekal(Request $request,$no_aduan)
    {
        $data = array();

        $data['maklumbalas'] = $request->maklumbalas;
        $data['tarikh_tindakan'] = date('Y-m-d', strtotime($request->tarikh_tindakan ));
        $data['masa_tindakan'] = date("H:i:s", strtotime( $request->masa_tindakan));
        $data['idstatus'] = $request->idstatus;

        $pembekal = DB::table('aduan')
                    ->where('no_aduan', $no_aduan)
                    ->update($data);

        //kronologi
        $dt = Carbon::now();

        $data1 = array();
        $data1['idstatus'] = $request->idstatus;
        $data1['no_aduan'] = $no_aduan;
        $data1['maklumbalas'] = $request->maklumbalas;
        $data1['tarikh_masa_skrg'] = $dt;
        $data1['id_pengadu'] = $request->id_pengadu;
        $data1['tindakan_pegawai'] = $request->tindakan_pegawai;

        $kronologi = DB::table('kronologi')->insert($data1);

        if($pembekal)
        {
            $url = base64_encode($no_aduan);

            $pengadu = DB::table('pengguna')
                        ->select('nama', 'email')
                        ->where('idpengguna', '=', $request->id_pengadu)
                        ->first();

            $to_name = $pengadu->nama;
            $to_email = $pengadu->email;

            $data3 = array(

                'no_aduan' => $no_aduan,
                'tajuk_aduan' => $request->masalah,
                'link' => $url
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
                         $mail->addAddress($to_email, $to_name);      // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN SELESAI ('.$no_aduan.')';
                        $mail->Body    = '<p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

                                        <p>Aduan berikut telah selesai:</p>

                                        <p>
                                            Tajuk Aduan: <strong>'.$request->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$no_aduan.'</strong>
                                        </p>

                                        <p>Sila klik pada pautan dibawah untuk maklumbalas pengguna

                                        <a href="http://developer2.penang.gov.my/helpdesk/public/maklumbalas/'.$url.'">Pautan</a>

                                        </p>

                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>
                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

            Mail::send('emails.aduanSelesai', $data3, function($message) use ($to_name, $to_email, $no_aduan) {
                $message->to($to_email, $to_name)
                        ->subject('HELPDESK ICT: ADUAN SELESAI ('.$no_aduan.')' );
                $message->from('mnizam@penang.gov.my','Admin Helpdesk');
            });

            return redirect()->route('technician.aduanselesai')->with('success', 'Aduan Berjaya Diselesaikan');
        }else{
            return redirect()->url('t_editpembekal/'.$no_aduan)->with('error', 'Aduan Gagal Diselesaikan');
        }

    }

    public function agihan(Request $request,$no_aduan)
    {
        $dt = Carbon::now();


        $data = array();

       /*  $data['noinventori'] = $request->noinventori; */
        $data['model'] = $request->model;
        $data['idkategori'] = $request->kategori;
        $data['jenis_kategori'] = $request->jenis_kategori;
        $data['nosiri'] = $request->nosiri;
        $data['maklumbalas'] = $request->maklumbalas;
        /* $data['tarikh_respon'] = date('Y-m-d', strtotime($request->tarikh_respon));
        $data['masa_respon'] =  date("H:i:s", strtotime($request->masa_respon));
        $data['tarikh_onsite'] = date('Y-m-d', strtotime($request->tarikh_onsite));
        $data['masa_onsite'] = date("H:i:s", strtotime($request->masa_onsite)); */
        $data['id_onsite'] = $request->id_onsite;
        $data['id_ganti'] = $request->id_ganti;
        $data['tarikh_tindakan'] = $dt->toDateString();
        $data['masa_tindakan'] = $dt->toTimeString();
        $data['idstatus'] = $request->idstatus;

        $agihan = DB::table('aduan')
                ->where('no_aduan', $no_aduan)
                ->update($data);

        //kronologi
        $dt = Carbon::now();

        $data1 = array();
        $data1['idstatus'] = $request->idstatus;
        $data1['no_aduan'] = $no_aduan;
        $data1['maklumbalas'] = $request->maklumbalas;
        $data1['tarikh_masa_skrg'] = $dt;
        $data1['id_pengadu'] = $request->id_pengadu;
        $data1['tindakan_pegawai'] = $request->tindakan_pegawai;

        $kronologi = DB::table('kronologi')->insert($data1);

        if($request->idstatus == 9)
        {
            $pengadu = DB::table('pengguna')
            ->select('nama', 'email')
            ->where('idpengguna', '=', $request->id_pengadu)
            ->first();

            // dd($pengadu->nama);
            $id_pengguna = $request->id_pengguna;
            $to_name = $pengadu->nama;
            $to_email = $pengadu->email;


            $tech = DB::table('pengguna')
                        ->select('nama','no_ofis')
                        ->where('idpengguna', '=', $id_pengguna)
                        ->first();
            /* dd($request->id_pengguna); */

            $data3 = array(

                'no_aduan' => $no_aduan,
                'tajuk_aduan' => $request->masalah,
                'tech_nama' => $tech->nama,
                'tech_ofis' => $tech->no_ofis

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
                        $mail->addAddress($to_email, $to_name);      // Add a recipient

                    // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN DALAM TINDAKAN PEMBEKAL ('.$no_aduan.')';
                        $mail->Body    = '<p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

                                        <p>Aduan berikut dalam tindakan pihak pembekal.</p>
                                        <p>
                                            Tajuk Aduan: <strong>'.$request->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$no_aduan.'</strong>
                                        </p>
                                        <p>Sebarang pertanyaan boleh hubungi:</p>
                                        <p>
                                            Nama Juruteknik: <strong>'.$tech->nama.'</strong><br>
                                            No. Telefon: <strong>'.$tech->no_ofis.'</strong>
                                        </p>

                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>
                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

                        Mail::send('emails.aduanPembekal', $data3, function($message) use ($to_name, $to_email, $no_aduan) {
                            $message->to($to_email, $to_name)
                                    ->subject('HELPDESK ICT: ADUAN DALAM TINDAKAN PEMBEKAL ('.$no_aduan.')' );
                            $message->from('mnizam@penang.gov.my','Admin Helpdesk');
                        });

            return redirect()->route('technician.aduanproses')->with('success', 'Aduan Berjaya Dimasukkan ke dalam Tindakan Pembekal');

        }elseif($request->idstatus == 10){

            return redirect()->route('technician.aduantolak')->with('success', 'Aduan Telah Ditolak');

        }elseif($request->idstatus == 3){

            $url = base64_encode($no_aduan);

            $pengadu = DB::table('pengguna')
                        ->select('nama', 'email')
                        ->where('idpengguna', '=', $request->id_pengadu)
                        ->first();

            $to_name = $pengadu->nama;
            $to_email = $pengadu->email;

            $data3 = array(

                'no_aduan' => $no_aduan,
                'tajuk_aduan' => $request->masalah,
                'link' => $url
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
                         $mail->addAddress($to_email, $to_name);      // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN SELESAI ('.$no_aduan.')';
                        $mail->Body    = '<p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

                                        <p>Aduan berikut telah selesai:</p>

                                        <p>
                                            Tajuk Aduan: <strong>'.$request->masalah.'</strong><br>
                                            No. Aduan: <strong>'.$no_aduan.'</strong>
                                        </p>

                                        <p>Sila klik pada pautan dibawah untuk maklumbalas pengguna

                                        <a href="http://developer2.penang.gov.my/helpdesk/public/maklumbalas/'.$url.'">Pautan</a>

                                        </p>

                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>
                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

            Mail::send('emails.aduanSelesai', $data3, function($message) use ($to_name, $to_email, $no_aduan) {
                $message->to($to_email, $to_name)
                        ->subject('HELPDESK ICT: ADUAN SELESAI ('.$no_aduan.')' );
                $message->from('mnizam@penang.gov.my','Admin Helpdesk');
            });


            return redirect()->route('technician.aduanselesai')->with('success', 'Aduan Telah Selesai');
        }else{
            return redirect()->url('t_editaduan/'.$no_aduan)->with('error', 'Aduan Gagal Diagih');
        }


    }

    public function reject(Request $request, $no_aduan)
    {
        /* $no_aduan = $request->no_aduan; */

        $dt = Carbon::now();

        $data = array();
        $data['maklumbalas'] = $request->maklumbalas;
        $data['id_pengguna'] = $request->id_pengguna;
        $data['tarikh_tindakan'] = $dt->toDateString();
        $data['masa_tindakan'] = $dt->toTimeString();
        $data['idstatus'] = 10;

        $reject = DB::table('aduan')
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

        if($reject)
        {
            $masalah = DB::table('aduan')->select('masalah')->where('no_aduan', $no_aduan)->first();
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
                         $mail->addAddress($to_email, $to_name);      // Add a recipient

                       // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'HELPDESK ICT: ADUAN DITOLAK ('.$no_aduan.')';
                        $mail->Body    = '<p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

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


            return redirect()->route('technician.aduantolak')->with('success', 'Aduan Berjaya Ditolak');
        }else
        {
            return redirect()->url('t_editaduan/'.$no_aduan)->with('error', 'Aduan Gagal Ditolak');
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
                    'aduan.masalah',
                    'aduan.errormsg',
                    'aduan.idstatus',
                    'aduan.image',
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
                    'aduan.idstatus',
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


        return view('technician.maklumatAduan', compact('aduan', 'pegawai'));
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

        return view('technician.edit_profil', compact('profil'));

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

        if(($profil) | ($profil)=='')
        {
            return redirect()->route('technician.profil')->with('success', 'Profil Berjaya Dikemaskini');
        }else{
            return redirect()->url('t_edit_profil/'.$idpengguna)->with('error', 'Profil Gagal Dikemaskini');
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

        return view('technician.add_pengguna', compact('jabatan'));
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
            $data['no_ofis'] = $request->no_ofis;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['aktif'] = $request->aktif;

            $pengguna = DB::table('pengguna')->insert($data);

            if($pengguna){

            return redirect()->route('technician.dashboard')->with('success', 'Pengguna Berjaya Didaftar');

        } else{

            return redirect()->route('technician.add_pengguna')->with('error', 'Pengguna Gagal Didaftar');

        }
        return redirect()->route('technician.dashboard')->with('success', 'Pengguna Berjaya Didaftar');
        }else{
            return redirect()->route('technician.add_pengguna')->with('error', 'Pengguna Telah Berdaftar');
        }
    }

    public function cariAduan()
    {
        return view('technician.cariAduan');
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
                href="t_maklumatAduan/'.$aduan->id.'">
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
        return view('technician.semakRekod');
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

        return view('technician.listTeknikal', compact('teknikal'));
    }

    public function addKategori()
    {
        $kategori = DB::table('kategori')
                    ->select('idkategori','kategori')
                    ->paginate(5);

        return view('technician.addKategori',compact('kategori'));
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
                return redirect()->route('technician.addKategori')->with('success', 'Berjaya Tambah Kategori');
            }
            else{
                return redirect()->route('technician.addKategori')->with('error', 'Gagal Tambah Kategori');
            }
        }else
        {
            return redirect()->route('technician.addKategori')->with('error', 'Kategori Telah Wujud');
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

        return view('technician.addSubkat',compact('kategori','subkat'));
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
                return redirect()->route('technician.addSubkat')->with('success', 'Berjaya Tambah SubKategori');
            }
            else{
                return redirect()->route('technician.addSubkat')->with('error', 'Gagal Tambah SubKategori');
            }
        }else
        {
            return redirect()->route('technician.addSubkat')->with('error', 'Subkategori Telah Wujud');
        }
    }

    public function addModel()
    {
        $model = DB::table('model')
                    ->select('idmodel','model_name')
                    ->paginate(5);

        return view('technician.addModel', compact('model'));
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
                return redirect()->route('technician.addModel')->with('success', 'Berjaya Tambah Model');
            }
            else{
                return redirect()->route('technician.addModel')->with('error', 'Gagal Tambah Model');
            }
        }else
        {
            return redirect()->route('technician.addModel')->with('error', 'Model Telah Wujud');
        }

    }

}
