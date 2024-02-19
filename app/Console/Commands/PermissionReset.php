<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PermissionReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload roles and permissions';

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
        $exitCode = Artisan::call('db:seed', [
            '--class' => 'RolesAndPermissionsTableSeeder'
        ]);
    }
}
