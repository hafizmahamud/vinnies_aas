<?php

use Illuminate\Database\Seeder;

class GalleriesDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Gallery::class, 100)->create();
    }
}
