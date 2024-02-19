<?php

namespace App\Vinnies;

use App\Sponsorship;
use App\Donation;
use App\Student;
use DB;

class Allocator
{
    protected $students;
    protected $donation_ids;
    protected $student_ids;

    public function __construct(array $students = [])
    {
        $this->students = collect($students)->map(function ($student) {
            return Student::find($student);
        });

        $this->donation_ids = [];
        $this->student_ids  = [];

        return $this;
    }

    public function getAvailableSponsorships()
    {
        $sponsorships_total = Donation::where('is_active', 1)->get()->sum(function ($donation) {
            return $donation->total_sponsorships;
        });

        $sponsorships_allocated = Donation::where('is_active', 1)->withCount('sponsorships')->get()->sum(function ($donation) {
            return $donation->sponsorships_count;
        });

        return $sponsorships_total - $sponsorships_allocated;
    }

    public function allocate()
    {
        $this->students->each(function ($student) {
            $donation = $this->getRandomDonation();

            // Just allocate as much as we can
            if (!$donation) {
                return false;
            }

            if (!in_array($donation->id, $this->donation_ids)) {
                $this->donation_ids[] = $donation->id;
            }

            if (!in_array($student->id, $this->student_ids)) {
                $this->student_ids[] = $student->id;
            }


            $sponsorship = new Sponsorship;
            $sponsorship->donation_id = $donation->id;
            $sponsorship->student_id  = $student->id;

            $result = $sponsorship->save();

            if ($result) {
                DB::transaction(function () use ($student, $donation) {
                    $donation->allocated_sponsorships = $donation->allocated_sponsorships + 1;
                    $donation->save();

                    $student->is_allocated = true;
                    $student->save();
                });
            }
        });

        return [
            'donations' => count($this->donation_ids),
            'students'  => count($this->student_ids)
        ];
    }

    private function getRandomDonation()
    {
        $donation = Donation::where('is_active', 1)
            ->where('special_allocation_required', 0)
            ->get()
            ->reject(function ($donation) {
                return $donation->isLesserThanSponsorshipValue();
            })->reject(function ($donation) {
                return $donation->isFullyAllocated();
            });

        if ($donation->isEmpty()) {
            return false;
        } else {
            return $donation->random();
        }
    }
}
