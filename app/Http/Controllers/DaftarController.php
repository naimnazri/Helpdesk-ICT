<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\User;
use Auth;

class DaftarController extends Controller
{
    public function daftarPengguna()
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



        return view('daftar.daftarPengguna', compact('jabatan', 'bahagian'));
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
                    ->select('idpengguna', 'email')
                    ->where('idpengguna', '=', $request->no_kp)
                    ->where('email', '=', $request->email)
                    ->first();

        if($pengguna === null)
        {
            if(isset($request['submit']))
            {
            $request->validate([
                    'no_kp' => 'digits_between:12,12|numeric|required',
                    /* 'password' => 'min:8|confirmed', */
                    'nama' => 'required|string|max:255',
                    'jawatan' => 'required',
                    'jabatan' => 'required',
                    'bahagian' => 'required',
                    'notel' => 'digits_between:10,13|numeric|required',
                    'no_ofis' => 'digits_between:9,13|numeric|required',
                    'email' => 'email|required'
            ]);

            $data = array();
            $data['idpengguna'] = $request->no_kp;
            $data['id_pengadu'] = $request->no_kp;
            $data['no_kp'] = $request->no_kp;
            $data['username'] = $request->no_kp;
            /* $data['password'] = md5($request->password); */
            $data['idlevel'] = 4;
            $data['nama'] = $request->nama;
            $data['jawatan'] = $request->jawatan;
            $data['idjab'] = $request->jabatan;
            $data['idbahagian'] = $request->bahagian;
            $data['notel'] = $request->notel;
            $data['no_ofis'] = $request->no_ofis;
            $data['email'] = $request->email;
            $data['temp_pass'] = 0;
            $data['aktif'] = 0;

            $daftar = DB::table('pengguna')->insert($data);

            $admins = DB::table('pengguna')
                ->select(
                    'nama',
                    'email'
                )
                ->where('idlevel', '=', '1')
                ->get();

            if($daftar)
            {
            foreach($admins as $admin)
            {

            $to_name = $admin->nama;
            $to_email = $admin->email;

            $data3 = array(
                'admin' => $to_name ,
                'pengguna' => $request->nama,
                'email' => $request->email
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
                        $mail->Subject = 'HELPDESK ICT: PENGGUNA BARU';
                        $mail->Body    = '
                                        <p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

                                        <p>Pengguna baru telah berdaftar. Maklumat pengguna seperti :</p>

                                        <p>
                                            Nama: <strong>'.$request->nama.'</strong><br>
                                            Email: <strong>'.$request->email.'</strong><br>

                                        </p>
                                        <p>Sila klik pada pautan seperti di bawah untuk aktifkan aduan.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>

                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

            Mail::send('emails.penggunaBaru', $data3, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('HELPDESK ICT: PENGGUNA BARU' );
                $message->from('noreply@penang.gov.my','Admin Helpdesk');
            });
        }

            return redirect()->route('daftar.afterDaftar')->with('success', 'Pengguna Berjaya Mendaftar');
            }
        }else{
                return redirect()->route('daftar.daftarPengguna')->with('warning', 'Sila lengkapkan Maklumat!');
            }

        }else{
            return redirect()->route('daftar.dahDaftar')->with('error', 'Pengguna Telah Berdaftar');

        }
    }

    public function forgotPass(Request $request)
    {
        return view('daftar.forgotpass');
    }

    public function storeForgot(Request $request)
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


        $email = $request->email;

        $check = DB::table('pengguna')
                ->select('email')
                ->where('email', '=', $email)
                ->first();

        if($check === null)
        {
            return redirect()->route('daftar.daftarPengguna')->with('error', 'Email anda belum berdaftar');

        }else{
            $data = array();
            $data['password'] = $ran_md5;
            $data['temp_pass'] = 1;
            $insert = DB::table('pengguna')
                ->where('email', '=', $email)
                ->update($data);


            if($insert)
            {
                $nama = DB::table('pengguna')
                        ->select('nama')
                        ->where('email', '=', $email)
                        ->first();



                $to_name = $nama->nama;
                $to_email = $request->email;

                $data3 = array(
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
                        $mail->Subject = 'HELPDESK: Katalaluan Sementara';
                        $mail->Body    = '
                                        <p>Salam Sejahtera,</p>

                                        <p>YAB/YB. Dato/YB/YBhg. Dato/Tuan/Puan,</p>

                                        <p>Berikut merupakan katalaluan sementara:</p>

                                            <div class="text-center">
                                                <div class="card">
                                                    <div class="card-body bg-primary">
                                                        <h2><strong>'.$random.'</strong></h2>
                                                    </div>
                                                </div>



                                        <p>Sila klik pada pautan seperti di bawah untuk log masuk.
                                        <br>Pautan: https://helpdeskict.penang.gov.my/</p>

                                        ';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send(); */

                Mail::send('emails.forgotMail', $data3, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('HELPDESK: Katalaluan Sementara' );
                    $message->from('miymy08@gmail.com','Admin Helpdesk');
                });

                return redirect()->route('daftar.afterForgot')->with('success', 'Password Berjaya Dihantar');
            }else{
                return redirect()->route('daftar.forgotpass')->with('error', 'Password Gagal Dihantar');
            }

        }

    }


    public function resetPass(Request $request)
    {
        return redirect()->route('login')->with('success', 'Password Berjaya Ditukar');
    }

    public function afterDaftar()
    {
        return view('daftar.afterDaftar');
    }

    public function dahDaftar()
    {
        return view('daftar.dahDaftar');
    }


    public function afterForgot()
    {
        return view('daftar.afterForgot');
    }

    public function maklumbalas($aduan)
    {
        $no_aduan = base64_decode($aduan);

        $check = DB::table('feedback')
        ->select('no_aduan_feedback')
        ->where('no_aduan_feedback', '=', $no_aduan)
        ->first();

        if($check === null)
        {
            $respon_feedback = DB::table('feedback_respon')
                        ->select('idrespon', 'respon_name')
                        ->get();


            $no_aduan = base64_decode($aduan);

            return view('daftar.feedback', compact('no_aduan','respon_feedback'));

        }
        else
        {
            return redirect()->route('daftar.doneFeedback')->with('error', 'Maklumbalas Telah Diterima');
        }
    }

    public function storeMaklumbalas(Request $request)
    {

            $data = array();
            $data['no_aduan_feedback'] = $request->no_aduan;
            $data['respon_feedback'] = $request->respon;
            /* $data['respon_masa'] = $request->respon_masa; */
            $data['catatan'] = $request->catatan;

            $feedback = DB::table('feedback')->insert($data);

            if($feedback)
            {
                return redirect()->route('daftar.successFeedback')->with('success', 'Maklumat Berjaya Dihantar');
            }
            else
            {
                return redirect()->route('daftar.feedback', $request->no_aduan)->with('error', 'Maklumat Gagal Dihantar');
            }

    }

    public function maklumbalasBerjaya()
    {
        return view('daftar.successFeedback');
    }

    public function maklumbalasGagal()
    {
        return view('daftar.failFeedback');
    }
}
