<?php

use Illuminate\Database\Seeder;
use App\User;

class ModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereEmail(env('ADMIN_EMAIL'))->first();
        $user->assignRole('Full Admin');
    }
}
