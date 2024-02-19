<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Certificate;
use App\Donation;
use Hashids;
use PDF;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function preCheck($hash)
    {
        $certificate = Hashids::decode($hash);

        if (empty($certificate)) {
            abort(403);
        }

        $certificate = Certificate::find($certificate[0]);

        if (!$certificate) {
            abort(403);
        }

        $count     = [];
        $donations = collect(unserialize($certificate->data))->unique()->map(function ($donation_id) {
            return Donation::find($donation_id);
        })->filter(function ($donation) {
            return ($donation->isFullyAllocated() || $donation->isLesserThanSponsorshipValue());
        });

        return $donations;
    }

    public function index($hash)
    {
        $this->authorize('create.certificates');

        $donations = $this->preCheck($hash);

        $count['donations'] = $donations->count();
        $count['students']  = $donations->sum(function ($donation) {
            return $donation->sponsorships->count(); //one sponsorship equal to one students, should be fine to use
        });

        return view('certificates.index')->with(compact('count', 'hash'));
    }

    public function download($hash)
    {
        $this->authorize('create.certificates');

        $donations = $this->preCheck($hash);
        $filename = sprintf('certificates-%s.pdf', $hash);

        $pdf = PDF::setOptions(['defaultFont' => 'Times-Roman']);
        $pdf->loadView('certificates.pdf', compact('donations'));

        // Update as printed
        Donation::whereIn('id', $donations->pluck('id')->toArray())->update(['is_printed' => 1]);

        if (request()->get('debug')) {
            return $pdf->stream($filename);
        } else {
            return $pdf->download($filename);
        }

    }
}
