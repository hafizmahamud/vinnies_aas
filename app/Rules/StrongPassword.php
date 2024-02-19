<?php

namespace App\Rules;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    protected $messages = [];

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
        // taken from Laravel 8 Password validation object: https://github.com/laravel/framework/blob/c5d57a7dbad9e3495e2e569d1aad17bb797ee969/src/Illuminate/Validation/Rules/Password.php

        // mixed case
        if (!preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            $this->fail('The :attribute must contain at least one uppercase, one lowercase letter, one number and one symbol.');
        }

        // letters
        if (!preg_match('/\pL/u', $value)) {
            $this->fail('The :attribute must contain at least one uppercase, one lowercase letter, one number and one symbol.');
        }

        // symbols
        if (!preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)) {
            $this->fail('The :attribute must contain at least one uppercase, one lowercase letter, one number and one symbol.');
        }

        // numbers
        if (!preg_match('/\pN/u', $value)) {
            $this->fail('The :attribute must contain at least one uppercase, one lowercase letter, one number and one symbol.');
        }

        return empty($this->messages);
    }

    public function message()
    {
        return $this->messages;
    }

    protected function fail($messages)
    {
        $messages = collect(Arr::wrap($messages))->map(function ($message) {
            return __($message);
        })->all();

        $this->messages = array_merge($this->messages, $messages);

        return false;
    }
}
