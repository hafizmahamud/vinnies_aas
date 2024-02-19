<?php

namespace App\Rules;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class ValidEmailDomain implements Rule
{
    /**
    * Create a new rule instance.
    *
    * @return void
    */
    public function __construct()
    {
        //
    }

    /**
    * Determine if the validation rule passes.
    *
    * @param  string  $attribute
    * @param  mixed  $value
    * @return bool
    */
    public function passes($attribute, $value)
    {
        $domain = Str::lower(Arr::get(explode('@', $value, 2), 1));

        return in_array($domain, config('vinnies.validEmailDomains'));
    }

    /**
    * Get the validation error message.
    *
    * @return string
    */
    public function message()
    {
        return "For security purposes, only SVDP Society email addresses are permitted, e.g. firstname.lastname@vinnies.org.au.";
    }
}
