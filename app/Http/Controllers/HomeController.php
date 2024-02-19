<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
        if(!$request->user()->google2fa_enabled_at){
            return redirect()->route('2fa.index')->with(['msg' => 'Please enable Two-Factor Authentication for your account!']);;
        }

        return view('home');
    }

    public function download($file)
    {
        return response()->download(storage_path('docs/' . config('vinnies.documents.' . $file)));
    }

    public function tos()
    {
        return view('tos')->with('checkboxes', $this->getAllCheckboxes());
    }

    public function acceptTos(Request $request)
    {
        if (!$request->filled('terms') || count($request->input('terms')) !== count($this->getAllCheckboxes())) {
            return redirect()->route('home.tos');
        }

        $request->user()->update([
            'has_accepted_terms' => true,
        ]);

        return redirect()->route('home');
    }

    private function getAllCheckboxes()
    {
        return [
            'a' => 'I confirm I have read, signed and agree to abide by the requirements of the National Council’s policy documents on Privacy, Safeguarding, Acceptable Use of ICT, Cybersecurity and Working Together policies.',
            'b' => 'I confirm I have read and agree to be bound by the unconditional acceptance and compliance with, the ‘Terms and Conditions’ set out above.',
            'c' => 'I confirm I have undergone a Police Check (mandatory).',
            'd' => 'I confirm I have a current working with vulnerable people card or equivalent for my State/Territory (as applicable).',
        ];
    }
}
