<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FAQRCode\QRCode\Chillerlan;

class TwoFAController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $google2fa = new Google2FA(new Chillerlan);

        $qr_code_url = $google2fa->getQRCodeInline(
            config('app.name'),
            $request->user()->email,
            $request->user()->google2fa_secret
        );

        return view('2fa.index')->with(compact('qr_code_url'));
    }

    public function enable(Request $request)
    {
        $request->user()->enableGoogle2FA();

        return redirect()->route('2fa.index')->with('status', 'Two-factor authentication has been successfully enabled for your account');
    }

    public function disable(Request $request)
    {
        $request->user()->disableGoogle2FA();

        return redirect()->route('2fa.index')->with('status', 'Two-factor authentication has been successfully disabled for your account');
    }

    public function adminReset(Request $request, User $user)
    {
        $user->disableGoogle2FA();

        $msg = 'Two-factor authentication has been successfully disabled';

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function verify()
    {
        return redirect()->route('home');
    }
}
