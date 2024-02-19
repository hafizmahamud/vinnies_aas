<?php

use Illuminate\Database\Seeder;
use App\Treshold;

class TresholdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Treshold::create(['amount' => 70]);
    }
}
