<?php

namespace App\Console\Commands;

use App\Student;
use Illuminate\Console\Command;

class RM10517Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:10517';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unallocate students and donatiosn from a list of countries given by the client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $valid_countries = [
            'Cambodia',
            'Indonesia',
            'Kiribati',
            'Myanmar',
            'Philippines',
            'Sri Lanka',
            'Thailand',
        ];

        $filtered_id = [
            14735,14991,15247,16271,16527,14480,14736,
            14992,15248,16619,16618,16617,16616,16615,16614,16613,16612,16611,16610,16609,
            16608,16607,16606,16605,16604,16603,16592,16591,16590,16589,16588,16587,16586,
            16585,16584,16583,16582,16581,16580,16579,16578,16577,16576,16575,16574,16573,
            16572,16571,16570,16569,16568,16567,16566,16565,16564,16563,16562,16561,16560,
            16559,16558,16557,16556,16555,16554,16553,16552,16551,16550,16549,16548,16547,
            16546,16545,16544,16543,16542,16541,16540,16539,16538,16537,16536,16535,16534,
            16533,16532,16531,16530,16529,16528,16382,16381,16380,16379,16378,16377,16376,
            16375,16374,16373,16372,16371,16370,16369,16368,16367,16366,16365,16364,16363,
            16362,16361,16360,16359,16358,16357,16356,16355,16354,16353,16352,16351,16350,
            16349,16348,16347,16346,16345,16344,16343,16342,16341,16340,16339,16338,16337,
            16336,16335,16334,16333,16332,16331,16330,16329,16328,16327,16326,16325,16324,
            16323,16322,16321,16320,16319,16318,16317,16316,16315,16314,16313,16312,16311,
            16310,16309,16308,16307,16306,16305,16304,16303,16302,16301,16300,16299,16298,
            16297,16296,16295,16294,16293,16292,16291,16290,16289,16288,16287,16286,16285,
            16284,16283,16282,16281,16280,16279,16278,16277,16276,16275,16274,16273,16272
        ];

        //$students = Student::whereNotIn('country', $valid_countries)
        $students = Student::whereIn('id', $filtered_id)
            ->with('sponsorship.donation')
            ->get()
            ->reject(function ($student) {
                return !$student->sponsorship;
            })
            ->filter(function ($student) {
                return $student->sponsorship->donation->created_at >= '2021-01-01 00:00:00';
            })
            ->each(function ($student) {

                $sponsorship = $student->sponsorship;
                $donation    = $sponsorship->donation;

                $this->info('Processing user ID: ' . $student->id . ' (' .$student->first_name. ' ' .$student->last_name. ':' .$student->country. ') -> donation ID: ' .$donation->id . ' (' .$donation->name_on_certificate. ')');

                // Set the student to unallocate
                $student->update(['is_allocated' => false]);

                // Reduce allocated sponsorship count for donation
                $count = intval($donation->allocated_sponsorships);

                if ($count > 0) {
                    $donation->update(['allocated_sponsorships' => ($count - 1)]);
                }

                // Delete the sponsorship
                $sponsorship->delete();
            });

          $this->info('Total unallocated students : ' . count($students));

    }
}
