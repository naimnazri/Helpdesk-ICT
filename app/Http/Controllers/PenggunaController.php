<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailAduan;
use App\Aduan;
use Carbon\Carbon;
use Auth;

class PenggunaController extends Controller
{
    public function resetPass()
    {
        return view('pengguna.reset_pass');
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
            return redirect()->route('pengguna.listaduan')->with('success', 'Password Berjaya direset');
        }
        else{
            return redirect()->route('pengguna.reset')->with('error', 'Password Gagal direset');
        }
    }

    public function penggunaHome()
    {

        $baru =DB::table('aduan')
                ->where('id_pengadu', '=', Auth::user()->idpengguna)
                ->where('idstatus', '=', '1')

                ->count();

        $proses =DB::table('aduan')
                ->whereIn('idstatus',  ['4','3'])
                ->where('id_pengadu', '=', Auth::user()->idpengguna)
                ->count();

        $selesai =DB::table('aduan')
                ->where('idstatus', '=', '3')
                ->where('id_pengadu', '=', Auth::user()->idpengguna)
                ->count();

        $tolak =DB::table('aduan')
                ->where('idstatus', '=', '7')
                ->where('id_pengadu', '=', Auth::user()->idpengguna)
                ->count();

        return view('pengguna.dashboard', compact('baru','proses','selesai','tolak'));
    }

    public function listAduan()
    {
        $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->latest('no_aduan')
                    ->paginate(5);

        return view('pengguna.listaduan', compact('aduan'));
    }

    public function aduanBaru()
    {
        $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->where('aduan.idstatus', '=', '1')
                    ->latest('no_aduan')
                    ->paginate(10);

        return view('pengguna.aduanbaru', compact('aduan'));
    }

    public function aduanProses()
    {
        $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->whereIn('aduan.idstatus',  ['4','3'])
                    ->latest('no_aduan')
                    ->paginate(10);

        return view('pengguna.aduanproses', compact('aduan'));
    }

    public function aduanSelesai()
    {
        $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->where('aduan.idstatus', '=', '3')
                    ->latest('no_aduan')
                    ->paginate(10);

        return view('pengguna.aduanselesai', compact('aduan'));
    }

    public function aduanTolak()
    {
        $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->select(
                        'aduan.id',
                        'aduan.masalah',
                        'aduan.no_aduan',
                        'aduan.tarikh_aduan',
                        'aduan.idstatus',
                        'status.nama_status'
                    )
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->where('aduan.idstatus', '=', '7')
                    ->latest('no_aduan')
                    ->paginate(10);

        return view('pengguna.aduantolak', compact('aduan'));
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
                        'pengguna.notel',
                        'pengguna.email',
                        'pengguna.jawatan',
                        'jabatan.jabatan'
                    )
                    ->where('idpengguna','=',  Auth::user()->idpengguna)
                    ->get();

                    return view('pengguna.profil' ,compact('profil'));
    }

    public function create()
    {
        /* $m=Date("m");
        if (strlen($m)==1)
        {
            $m="0".$m;
        } else
        {
            $m=$m;
        }
        $y= Date("y");

        $dt = Carbon::now();

        $tahun = $dt->year;
        $bulan = $dt->month;

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
            $irno = $f.str_pad($newsuffix2, 4, 0, STR_PAD_LEFT); */

        /* $o = 0001;
        $oo = str_pad($o,4,'0', STR_PAD_LEFT);
        $code = $u."-".$oo;
        //code asas = 2005-0001

        //check code asas wujud ke tak
        $no = DB:: table('aduan')
                ->select('no_aduan')
                ->where('no_aduan', '=', $code)
                ->first();

        if($no === null){

            //code asas tak wujud

            $s = $y.$m;
            $f = $y.$m."-";
            $no2 = DB:: table('aduan')
                ->select('no_aduan')
                ->where('no_aduan', 'like', '%'.$s.'%')
                ->max('no_aduan');


                $suffix2 = substr($no2, -4);
                $newsuffix2 = intval($suffix2) + 1;
                $irno = $f.str_pad($newsuffix2, 4, 0, STR_PAD_LEFT);


            dd($irno);


        } else {

            //code asas dah ada

            $f = $y.$m."-";
            $test1 = $no->no_aduan;
            $suffix = substr($test1, -4);
            $newsuffix = intval($suffix) + 1;
            $irno = $f.str_pad($newsuffix, 4, 0, STR_PAD_LEFT);


            dd($irno);
        } */
        $pengguna = DB::table('pengguna')
                        ->join('jabatan', 'pengguna.idjab',
                        '=', 'jabatan.idjab')
                        ->join('bahagian', 'pengguna.idbahagian',
                        '=', 'bahagian.idbahagian')
                        ->select(
                            'pengguna.nama',
                            'pengguna.no_kp',
                            'pengguna.notel',
                            'pengguna.email',
                            'pengguna.jawatan',
                            'bahagian.bahagian',
                            'jabatan.jabatan'
                        )
                        ->where('idpengguna','=',  Auth::user()->idpengguna)
                        ->get();

        $kategori = DB::table('kategori')
                        ->select(
                            'idkategori',
                            'kategori'
                        )
                        ->get();

        return view('pengguna.add_aduan', compact('kategori','pengguna'));
    }

    public function Store(Request $request)
    {
        $dt = Carbon::now();
        $tahun = $dt->year;
        $bulan = $dt->month;

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



        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ]);

        $data = array();
        $data['id_pengadu'] = $request->id_pengadu;
        $data['no_aduan'] = $irno;
        $data['idjab'] = $request->idjab;
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

        $admins = DB::table('pengguna')
                ->select(
                    'nama',
                    'email'
                )
                ->whereIn('idlevel', ['1','7']) //penyelaras dan teknikal kanan
                ->get();

        //kronologi
        $data1 = array();
        $data1['idstatus'] = 1;
        $data1['no_aduan'] = $irno;
        $data1['tarikh_masa_skrg'] = $dt;
        $data1['id_pengadu'] = $request->id_pengadu;
        $data1['tindakan_pegawai'] = $request->nama;

        $kronologi = DB::table('kronologi')->insert($data1);

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

           /*  $mail = new PHPMailer(true);


                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = env('MAIL_HOST');                    // Set the SMTP server to send through
                $mail->SMTPAuth   = env('MAIL_AUTH');                                   // Enable SMTP authentication
                $mail->Username   = env('MAIL_USERNAME');                     // SMTP username
                $mail->Password   = env('MAIL_PASSWORD');                               // SMTP password
                $mail->SMTPSecure = env('MAIL_ENCRYPTION');         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = env('MAIL_PORT');                                     // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

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
            return redirect()->route('pengguna.listaduan')->with('success', 'Aduan Berjaya Ditambah');
        }else{
            return redirect()->route('pengguna.add_aduan')->with('error', 'Aduan Gagal Ditambah');
        }

    }


    public function detail($no_aduan)
    {
         $aduan = DB::table('aduan')
                    ->join('status', 'aduan.idstatus', '=',
                        'status.idstatus')
                    ->leftjoin('kategori', 'aduan.idkategori', '=',
                        'kategori.idkategori')
                    ->leftjoin('feedback', 'aduan.no_aduan', '=',
                        'feedback.no_aduan_feedback')
                    ->leftjoin('feedback_respon', 'feedback.respon_feedback', '=',
                        'feedback_respon.idrespon')
                    ->select(
                        'aduan.no_aduan',
                        'aduan.masalah',
                        'aduan.errormsg',
                        'aduan.idkategori',
                        'aduan.jenis_kategori',
                        'aduan.noinventori',
                        'aduan.model',
                        'aduan.tarikh_aduan',
                        'aduan.masa_aduan',
                        'aduan.idstatus',
                        'status.nama_status',
                        'aduan.maklumbalas',
                        'aduan.tarikh_tindakan',
                        'aduan.masa_tindakan',
                        'aduan.nosiri',
                        'aduan.maklumbalas',
                        'aduan.image',
                        'kategori.kategori',
                        'feedback_respon.respon_name',
                        'feedback.catatan',
                        'feedback.no_aduan_feedback'
                    )
                    ->where('no_aduan', '=', $no_aduan)
                    ->where('id_pengadu', '=', Auth::user()->idpengguna)
                    ->first();

        $format = date('d-m-Y', strtotime($aduan->tarikh_tindakan));

        return view('pengguna.detail_aduan', compact('aduan', 'format' ));
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
                        'pengguna.notel',
                        'pengguna.email',
                        'pengguna.jawatan'

                    )
                    ->where('idpengguna','=',  $idpengguna)
                    ->first();

        return view('pengguna.edit_profil', compact('profil'));

    }

    public function storeProfil(Request $request, $idpengguna)
    {
        $data = array();
        if(trim($request->password) == '')
        {
            $data['nama'] = $request->nama;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;

        } else {

            $request->validate([
                'password' => 'min:8|confirmed'
            ]);

            $password = $request->password;
            $pass = md5($password);
            $data['password'] = $pass;

            $data['nama'] = $request->nama;
            $data['notel'] = $request->notel;
            $data['email'] = $request->email;
            $data['jawatan'] = $request->jawatan;
        }


        $profil = DB::table('pengguna')
                    ->where('idpengguna', $idpengguna)
                    ->update($data);

        if(($profil) | ($profil == ''))
        {
            return redirect()->route('pengguna.profil')->with('success', 'Profil Berjaya Dikemaskini');
        }else
        {
            return redirect()->url('p_edit_profil/'.$idpengguna)->with('error', 'Profil Gagal Dikemaskini');
        }

    }

}
