<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use PragmaRX\Google2FA\Google2FA;

class UsersPopulate2FASecretCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:populate-2fa-secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill in Google 2FA Secret column for all users';

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
     * @return int
     */
    public function handle()
    {
        User::all()->each(function ($user) {
            $this->info('Processing user ID: ' . $user->id);

            $user->update([
                'google2fa_secret' => (new Google2FA)->generateSecretKey(),
            ]);
        });

        return 0;
    }
}
