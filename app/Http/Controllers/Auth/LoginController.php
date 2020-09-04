<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'idpengguna' => 'required',
            'password' => 'required',
        ]);

        if(auth()->attempt(array(
            'idpengguna' => $input['idpengguna'],
            'password' => $input['password'],
            'aktif' => 1
            )))
        {

            if ((auth()->user()->idlevel == 1) && (auth()->user()->temp_pass == 0)) {
                return redirect()->route('admin.dashboard')
                ->with('success','Anda Berjaya Log Masuk.');
            }
            elseif ((auth()->user()->idlevel == 1) && (auth()->user()->temp_pass == 1)) {
                return redirect()->route('admin.reset')
                ->with('success','Anda Berjaya Log Masuk. Sila Reset Katalaluan');
            }
            elseif ((auth()->user()->idlevel == 7) && (auth()->user()->temp_pass == 0)) {
                return redirect()->route('admin.dashboard')
                ->with('success','Anda Berjaya Log Masuk.');
            }
            elseif ((auth()->user()->idlevel == 7) && (auth()->user()->temp_pass == 1)) {
                return redirect()->route('admin.reset')
                ->with('success','Anda Berjaya Log Masuk. Sila Reset Katalaluan');
            }
            elseif ((auth()->user()->idlevel == 8) && (auth()->user()->temp_pass == 0)) {
                return redirect()->route('admin.dashboard')
                ->with('success','Anda Berjaya Log Masuk.');
            }
            elseif ((auth()->user()->idlevel == 8) && (auth()->user()->temp_pass == 1)) {
                return redirect()->route('admin.reset')
                ->with('success','Anda Berjaya Log Masuk. Sila Reset Katalaluan');
            }
            elseif ((auth()->user()->idlevel == 2) && (auth()->user()->temp_pass == 0))
            {
                return redirect()->route('technician.dashboard')
                ->with('success','Anda Berjaya Log Masuk.');
            }
            elseif ((auth()->user()->idlevel == 2) && (auth()->user()->temp_pass == 1))
            {
                return redirect()->route('technician.reset')
                ->with('success','Anda Berjaya Log Masuk. Sila Reset Katalaluan');
            }
            elseif ((auth()->user()->idlevel == 4) && (auth()->user()->temp_pass == 0)) {
                return redirect()->route('pengguna.listaduan')
                ->with('success','Anda Berjaya Log Masuk.');
            }
            elseif ((auth()->user()->idlevel == 4) && (auth()->user()->temp_pass == 1)) {
                return redirect()->route('pengguna.reset')
                ->with('success','Anda Berjaya Log Masuk. Sila Reset Katalaluan');
            }
        }else{
            return redirect()->route('login')
                ->with('error','ID Pengguna dan Katalaluan Salah.');
        }

    }

}
