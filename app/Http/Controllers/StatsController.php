<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Donation;
use App\Student;
use App\Sponsorship;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $students  = Student::where('is_active', 1)->get();
        $donations = Donation::where('is_active', 1)->get();
        $stats     = [];

        // Stats for students
        $stats['students']['total'] = $students->count();

        $stats['students']['allocated'] = $students->filter(function ($student) {
            return $student->is_allocated;
        })->count();

        $stats['students']['unallocated'] = $stats['students']['total'] - $stats['students']['allocated'];

        // Stats for donations
        $stats['donations']['total'] = $donations->count();

        $stats['donations']['fully_allocated'] = $donations->filter(function ($donation) {
            return $donation->isFullyAllocated();
        })->count();

        $stats['donations']['partially_allocated'] = $donations->filter(function ($donation) {
            return $donation->isPartiallyAllocated();
        })->count();

        $stats['donations']['lesser_than_sponsorship_value'] = $donations->filter(function ($donation) {
            return $donation->isLesserThanSponsorshipValue();
        })->count();

        $stats['donations']['unallocated'] = $stats['donations']['total'] - $stats['donations']['fully_allocated'] - $stats['donations']['partially_allocated'] - $stats['donations']['lesser_than_sponsorship_value'];

        // Stats for sponsorship
        $stats['sponsorships']['total'] = $donations->sum(function ($donation) {
            return $donation->total_sponsorships;
        });

        $stats['sponsorships']['allocated'] = $donations->sum(function ($donation) {
            return $donation->sponsorships->count();
        });

        $stats['sponsorships']['unallocated'] = $stats['sponsorships']['total'] - $stats['sponsorships']['allocated'];

        return view('stats.index')->with(compact('stats'));
    }
}
