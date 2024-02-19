<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate users of 60 days of inactivity';

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
        User::all()->filter(function ($user) {
            if ($user->last_login !== null && Carbon::now()->diffInDays($user->last_login) >= 60) {
                return true;
            }

            if ($user->last_login === null && Carbon::now()->diffInDays($user->created_at) >= 60) {
                return true;
            }

            return false;
        })->each(function ($user) {
            $this->info('Deactivating user ID: ' . $user->id);
            $user->deactivate();
        });

        return 0;
    }
}
