<?php

namespace App\Vinnies;

class Access
{
    public static function get()
    {
        return collect(config('vinnies.access'));
    }
    public static function getRoles()
    {
        return collect(config('vinnies.access'))->keys();
    }

    public static function getPermissions()
    {
        return collect(config('vinnies.access'))
            ->flatten()
            ->unique();
    }
}
